<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeploySetupController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        $setupKey = env('DEPLOY_SETUP_KEY');

        if (! $setupKey || ! hash_equals($setupKey, $token)) {
            abort(404);
        }

        $steps = [];
        $steps['db_check'] = [
            'host' => env('DB_HOST') ?: '(empty)',
            'port' => env('DB_PORT') ?: '(empty)',
            'database' => env('DB_DATABASE') ?: '(empty)',
            'username' => env('DB_USERNAME') ?: '(empty)',
            'password_set' => env('DB_PASSWORD') ? 'yes ('.strlen((string) env('DB_PASSWORD')).' chars)' : 'NO — add DB_PASSWORD in Render',
            'db_url_set' => env('DB_URL') ? 'yes — remove DB_URL if using DB_* vars' : 'no',
            'ssl_ca' => env('MYSQL_ATTR_SSL_CA') ?: '(auto for aiven)',
        ];

        $needsReset = Schema::hasTable('users') && ! Schema::hasColumn('users', 'name');
        if ($needsReset) {
            $steps['warning'] = 'Old incompatible users table found. Use ?reset=1 in URL to recreate all tables.';
        }

        try {
            Artisan::call('config:clear');

            if ($request->query('reset') === '1') {
                Artisan::call('migrate:fresh', [
                    '--force' => true,
                    '--seeder' => 'Database\\Seeders\\AdminSeeder',
                ]);
                $steps['migrate'] = 'FRESH OK — all tables recreated';
                $steps['seed'] = 'OK — admin user created';
            } else {
                Artisan::call('migrate', ['--force' => true]);
                $steps['migrate'] = trim(Artisan::output()) ?: 'OK';

                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\AdminSeeder',
                    '--force' => true,
                ]);
                $steps['seed'] = trim(Artisan::output()) ?: 'OK';
            }
        } catch (\Throwable $e) {
            $steps['migrate'] = 'FAILED: '.$e->getMessage();
            if (! isset($steps['seed'])) {
                $steps['seed'] = 'skipped';
            }
            if ($needsReset || str_contains($e->getMessage(), 'already exists')) {
                $steps['fix'] = 'Open this URL: /deploy/setup/'.$token.'?reset=1';
            }
        }

        try {
            $steps['users_count'] = DB::table('users')->count();
            $steps['admin_exists'] = DB::table('users')
                ->whereIn('email', ['admin@sportiptv.com', 'admin@zinoutv.com'])
                ->exists() ? 'yes' : 'no';
        } catch (\Throwable $e) {
            $steps['users_count'] = 'FAILED: '.$e->getMessage();
        }

        return response()->json([
            'status' => 'done',
            'message' => 'Remove DEPLOY_SETUP_KEY from Render after success.',
            'admin_emails' => ['admin@sportiptv.com', 'admin@zinoutv.com'],
            'admin_password' => 'password',
            'steps' => $steps,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
