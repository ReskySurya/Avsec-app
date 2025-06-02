<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => Role::SUPERADMIN,
                'description' => 'Super Administrator dengan akses penuh'
            ],
            [
                'name' => Role::SUPERVISOR,
                'description' => 'Supervisor dengan akses terbatas'
            ],
            [
                'name' => Role::OFFICER,
                'description' => 'Officer dengan akses dasar'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
