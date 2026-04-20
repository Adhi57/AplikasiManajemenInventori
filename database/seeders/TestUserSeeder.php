<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'nama_lengkap' => 'Test SuperAdmin',
                'username'     => 'superadmin_test',
                'email'        => 'superadmin@test.com',
                'password'     => Hash::make('password'),
                'role'         => 'SuperAdmin',
            ],
            [
                'nama_lengkap' => 'Test Admin',
                'username'     => 'admin_test',
                'email'        => 'admin@test.com',
                'password'     => Hash::make('password'),
                'role'         => 'Admin',
            ],
            [
                'nama_lengkap' => 'Test Head',
                'username'     => 'head_test',
                'email'        => 'head@test.com',
                'password'     => Hash::make('password'),
                'role'         => 'Head',
            ],
            [
                'nama_lengkap' => 'Test Staff',
                'username'     => 'staff_test',
                'email'        => 'staff@test.com',
                'password'     => Hash::make('password'),
                'role'         => 'Staff',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
