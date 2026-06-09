<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@sportiptv.com'],
            [
                'name' => 'Sport IPTV Admin',
                'password' => 'password',
                'role' => UserRole::ADMIN,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@zinoutv.com'],
            [
                'name' => 'Zinou TV Admin',
                'password' => 'password',
                'role' => UserRole::ADMIN,
                'is_active' => true,
            ]
        );
    }
}
