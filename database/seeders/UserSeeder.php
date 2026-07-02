<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat Akun Admin
        User::create([
            'name' => 'Admin Polda',
            'email' => 'admin@polda.com',
            'password' => Hash::make('password123'),
            'role' => 'admin', // Pastikan kolom role sudah ada di migration users
        ]);

        // Membuat Akun User Biasa untuk Tes
        User::create([
            'name' => 'Firnanda User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);
    }
}