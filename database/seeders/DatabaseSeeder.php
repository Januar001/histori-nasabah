<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Petugas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $petugasAdmin = Petugas::create([
            'kode_petugas' => 'ADM001',
            'nama_petugas' => 'Administrator',
            'divisi' => 'AO',
            'email' => 'admin@bank.com',
            'telepon' => '081234567890',
            'status_aktif' => true,
        ]);

        User::create([
            'name' => 'Administrator',
            'email' => 'admin@histori.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'petugas_id' => $petugasAdmin->id,
        ]);

        $petugasAo = Petugas::create([
            'kode_petugas' => 'AO001',
            'nama_petugas' => 'Budi Santoso',
            'divisi' => 'AO',
            'email' => 'budi@bank.com',
            'telepon' => '081234567891',
            'status_aktif' => true,
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@histori.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'petugas_id' => $petugasAo->id,
        ]);

        $petugasRemedial = Petugas::create([
            'kode_petugas' => 'REM001',
            'nama_petugas' => 'Sari Wijaya',
            'divisi' => 'Remedial',
            'email' => 'sari@bank.com',
            'telepon' => '081234567892',
            'status_aktif' => true,
        ]);

        User::create([
            'name' => 'Sari Wijaya',
            'email' => 'sari@histori.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'petugas_id' => $petugasRemedial->id,
        ]);

        $petugasSpecial = Petugas::create([
            'kode_petugas' => 'SPC001',
            'nama_petugas' => 'Andi Pratama',
            'divisi' => 'Special',
            'email' => 'andi@bank.com',
            'telepon' => '081234567893',
            'status_aktif' => true,
        ]);

        User::create([
            'name' => 'Andi Pratama',
            'email' => 'andi@histori.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'petugas_id' => $petugasSpecial->id,
        ]);
    }
}
