<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Equipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        // Daily-Test Equipment
        $dailyTestEquipment = [
            'hhmd',
            'wtmd',
            'xraybagasi',
            'xraycabin',
        ];

        foreach ($dailyTestEquipment as $equipment) {
            Equipment::create([
                'name' => $equipment,
                'description' => 'Alat untuk Daily Test',
                'creationID' => 1
            ]);
        }
    }
}
