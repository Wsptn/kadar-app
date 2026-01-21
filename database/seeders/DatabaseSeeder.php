<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // WAJIB: Import ini untuk enkripsi password

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat User Admin Manual
        User::create([
            'name'     => 'Administrator',      // Nama lengkap (bebas)
            'username' => 'admin',              // Username Login
            'password' => Hash::make('admin123'), // Password (Wajib di-Hash)
        ]);
    }
}
