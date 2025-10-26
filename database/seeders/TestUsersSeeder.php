<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@exammaster.com'],
            [
                'full_name' => 'Admin User',
                'password_hash' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified' => true,
            ]
        );

        // Create regular user
        User::updateOrCreate(
            ['email' => 'user@exammaster.com'],
            [
                'full_name' => 'Test User',
                'password_hash' => Hash::make('password123'),
                'role' => 'student',
                'email_verified' => true,
            ]
        );

        $this->command->info('Test users created successfully!');
        $this->command->info('Admin: admin@exammaster.com / password123');
        $this->command->info('User: user@exammaster.com / password123');
    }
}