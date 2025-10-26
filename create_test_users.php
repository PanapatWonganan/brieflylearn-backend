<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Create admin user
$admin = User::updateOrCreate(
    ['email' => 'admin@exammaster.com'],
    [
        'name' => 'Admin User',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]
);

// Create regular user
$user = User::updateOrCreate(
    ['email' => 'user@exammaster.com'],
    [
        'name' => 'Test User',
        'password' => Hash::make('password123'),
        'role' => 'user',
        'email_verified_at' => now(),
    ]
);

echo "Users created successfully!\n";
echo "Admin: admin@exammaster.com / password123\n";
echo "User: user@exammaster.com / password123\n";