<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@histori.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add sample petugas
        DB::table('users')->insert([
            'name' => 'Budi Santoso',
            'email' => 'budi@histori.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'divisi' => 'AO',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
