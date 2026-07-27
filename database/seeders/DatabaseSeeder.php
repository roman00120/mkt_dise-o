<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $password = env('LOCAL_AUTH_PASSWORD', 'TG-Local-2026!');

        foreach ([
            ['Administrador', 'admin@totalground.local', UserRole::ADMIN],
            ['Andrea Martínez', 'marketing@totalground.local', UserRole::MARKETING],
            ['Luis Hernández', 'design@totalground.local', UserRole::DESIGN],
            ['Mariana Torres', 'video@totalground.local', UserRole::VIDEO],
            ['Carlos Ramírez', 'render@totalground.local', UserRole::RENDER],
            ['Sofía Navarro', 'supervisor@totalground.local', UserRole::SUPERVISOR],
        ] as [$name, $email, $role]) {
            User::updateOrCreate(['email' => $email], [
                'name' => $name,
                'password' => Hash::make($password),
                'status' => UserStatus::ACTIVE,
                'role' => $role,
            ]);
        }
    }
}
