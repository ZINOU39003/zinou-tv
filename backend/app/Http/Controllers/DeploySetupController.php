<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DeploySetupController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        $setupKey = env('DEPLOY_SETUP_KEY');

        if (! $setupKey || ! hash_equals($setupKey, $token)) {
            abort(404);
        }

        $steps = [];

        try {
            Artisan::call('migrate', ['--force' => true]);
            $steps['migrate'] = trim(Artisan::output()) ?: 'OK';
        } catch (\Throwable $e) {
            $steps['migrate'] = 'FAILED: '.$e->getMessage();
        }

        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\AdminSeeder',
                '--force' => true,
            ]);
            $steps['seed'] = trim(Artisan::output()) ?: 'OK';
        } catch (\Throwable $e) {
            $steps['seed'] = 'FAILED: '.$e->getMessage();
        }

        try {
            $steps['users_count'] = DB::table('users')->count();
        } catch (\Throwable $e) {
            $steps['users_count'] = 'FAILED: '.$e->getMessage();
        }

        return response()->json([
            'status' => 'done',
            'message' => 'Remove DEPLOY_SETUP_KEY from Render after success.',
            'admin_email' => 'admin@sportiptv.com',
            'admin_password' => 'password',
            'steps' => $steps,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
