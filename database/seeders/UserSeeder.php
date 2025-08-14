<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', Role::SUPERADMIN)->first();
        $supervisorRole = Role::where('name', Role::SUPERVISOR)->first();
        $officerRole = Role::where('name', Role::OFFICER)->first();

        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'nip' => '2024001',
            'lisensi' => 'LIC001',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('1'),
            'role_id' => $superAdminRole->id,
        ]);

        // Create Supervisor
        User::create([
            'name' => 'Supervisor',
            'nip' => '2024002',
            'lisensi' => 'LIC002',
            'email' => 'supervisor@example.com',
            'password' => Hash::make('1'),
            'role_id' => $supervisorRole->id,
        ]);

        // Create Officer (contoh default)
        User::create([
            'name' => 'Officer',
            'nip' => '2024003',
            'lisensi' => 'LIC003',
            'email' => 'officer@example.com',
            'password' => Hash::make('1'),
            'role_id' => $officerRole->id,
        ]);

        // Officer Data dari gambar
        $officers = [
            // Junior
            ['name' => 'Toni Pranoto', 'nip' => '2202187', 'email' => 'Tonypranoto1989@gmail.com', 'lisensi' => 'JUNIOR'],
            ['name' => 'Bernadus Ryan', 'nip' => '2202150', 'email' => 'ryan.arki@gmail.com', 'lisensi' => 'JUNIOR'],
            ['name' => 'New Warso Dwi H.', 'nip' => '2202171', 'email' => 'Trontontono66@gmail.com', 'lisensi' => 'JUNIOR'],
            ['name' => 'Tri Hartono', 'nip' => '2202188', 'email' => 'trihartono1205@gmail.com', 'lisensi' => 'JUNIOR'],
            ['name' => 'Muhammad Apang Kartono', 'nip' => '2202169', 'email' => 'apangkartono@gmail.com', 'lisensi' => 'JUNIOR'],

            // Basic
            ['name' => 'Agus Budi Santoso', 'nip' => '2202140', 'email' => 'Budiandoxs18@gmail.com', 'lisensi' => 'BASIC'],
            ['name' => 'Amalia Marsiati', 'nip' => '2202144', 'email' => 'amaliamarsiati82@gmail.com', 'lisensi' => 'BASIC'],
            ['name' => 'Danny Sukarno', 'nip' => '2202154', 'email' => 'dannysukarno27@gmail.com', 'lisensi' => 'BASIC'],
            ['name' => 'Ervan Budianto', 'nip' => '2202160', 'email' => 'evandgadink@gmail.com', 'lisensi' => 'BASIC'],
            ['name' => 'Hengky Tias Saputra', 'nip' => '2202162', 'email' => 'cuttirex86@gmail.com', 'lisensi' => 'BASIC'],
        ];

        foreach ($officers as $officer) {
            User::create([
                'name' => $officer['name'],
                'nip' => $officer['nip'],
                'lisensi' => $officer['lisensi'],
                'email' => $officer['email'],
                'password' => Hash::make('1'),
                'role_id' => $officerRole->id,
            ]);
        }
    }
}
