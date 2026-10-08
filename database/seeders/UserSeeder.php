<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Sistem', 'email' => 'admin@simjenazah.test', 'role' => User::ROLE_ADMIN],
            ['name' => 'dr. Rahma Wulandari', 'email' => 'forensik1@simjenazah.test', 'role' => User::ROLE_PETUGAS_FORENSIK],
            ['name' => 'dr. Budi Santosa', 'email' => 'forensik2@simjenazah.test', 'role' => User::ROLE_PETUGAS_FORENSIK],
            ['name' => 'Siti Aminah', 'email' => 'admin1@simjenazah.test', 'role' => User::ROLE_PETUGAS_ADMINISTRASI],
            ['name' => 'Joko Prasetyo', 'email' => 'pelapor1@simjenazah.test', 'role' => User::ROLE_KELUARGA_PELAPOR],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
