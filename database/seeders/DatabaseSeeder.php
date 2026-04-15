<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'role' => 'admin',
                'password' => 'password',
                'payment_status' => User::PAYMENT_APPROVED,
                'payment_approved_at' => now(),
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Default Student',
                'role' => 'student',
                'password' => 'password',
                'payment_status' => User::PAYMENT_APPROVED,
                'payment_approved_at' => now(),
                'email_verified_at' => now(),
            ],
        );
    }
}
