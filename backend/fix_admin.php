<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$users = DB::table('users')->get();
foreach ($users as $user) {
    echo "User ID: {$user->id}, Email: {$user->email}, Role: {$user->role}\n";
    
    $updateData = [];

    // If role is invalid (like 19961997), reset to 'admin'
    if (!in_array($user->role, ['admin', 'user'])) {
        echo "  -> Invalid role detected ('{$user->role}'). Resetting to 'admin'.\n";
        $updateData['role'] = 'admin';
        
        // Maybe the user put the password in the role column by mistake
        if (!empty($user->role) && is_numeric($user->role)) {
            echo "  -> Found password inside role column: {$user->role}. Hashing it...\n";
            $updateData['password'] = Hash::make($user->role);
        }
    }

    // Check if password starts with $2y$ (Bcrypt)
    if (!isset($updateData['password']) && !str_starts_with($user->password, '$2y$')) {
        echo "  -> Invalid password hash detected! Fixing...\n";
        $plain = empty($user->password) ? '19961997' : $user->password;
        $updateData['password'] = Hash::make($plain);
        echo "  -> Password successfully hashed.\n";
    }

    if (!empty($updateData)) {
        DB::table('users')->where('id', $user->id)->update($updateData);
        echo "  -> User updated.\n";
    }
}
