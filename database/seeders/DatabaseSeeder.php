<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat akun Admin Utama
        User::create([
            'name' => 'Billy Bintoro',
            'email' => 'admin@sppsmkutama.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
        ]);
    }
}