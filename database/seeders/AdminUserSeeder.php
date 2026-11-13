<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@sipandsmoke.com.au');
        $password = env('ADMIN_PASSWORD', 'SipSmoke!2026');

        User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Sip & Smoke Admin'),
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Keep the seeded account usable if an older local user exists.
        User::query()->where('email', $email)->update(['is_admin' => true]);
    }
}
