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

        // Create Officer
        User::create([
            'name' => 'Officer',
            'nip' => '2024003',
            'lisensi' => 'LIC003',
            'email' => 'officer@example.com',
            'password' => Hash::make('1'),
            'role_id' => $officerRole->id,
        ]);
    }
}
