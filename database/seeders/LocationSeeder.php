<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Equipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Semua lokasi yang dibutuhkan untuk WTMD dan HHMD
        $allLocations = [
            // HHMD Locations
            ['name' => 'HBSCP', 'description' => 'Lokasi untuk Form HHMD'],
            ['name' => 'Pos Timur', 'description' => 'Lokasi untuk Form HHMD dan WTMD'],
            ['name' => 'Pos Barat', 'description' => 'Lokasi untuk Form HHMD'],
            ['name' => 'PSCP Utara', 'description' => 'Lokasi untuk Form HHMD dan WTMD'],
            ['name' => 'PSCP Selatan', 'description' => 'Lokasi untuk Form HHMD dan WTMD'],
            ['name' => 'Pos Kedatangan', 'description' => 'Lokasi untuk Form HHMD'],
        ];

        foreach ($allLocations as $locationData) {
            Location::create([
                'name' => $locationData['name'],
                'description' => $locationData['description'],
                'creationID' => 1
            ]);
        }

        // XRAY Bagasi Locations
        $xrayBagasiLocations = [
            'HBSCP Bagasi Timur',
            'HBSCP Bagasi Barat'
        ];

        foreach ($xrayBagasiLocations as $location) {
            Location::create([
                'name' => $location,
                'description' => 'Lokasi untuk Form Xray Bagasi',
                'creationID' => 1
            ]);
        }

        // XRAY Cabin Locations
        $xrayCabinLocations = [
            'PSCP Cabin Utara',
            'PSCP Cabin Selatan'
        ];

        // Additional Locations for Logbook
        $additionalLocations = [
            'PSCP',
            'CCTV',
            'Patroli',
            'Walking Patrol',
            'Sweeping PI',
            'Rotasi HBSCP',
            'Rotasi PSCP',
        ];

        foreach ($additionalLocations as $location) {
            Location::create([
                'name' => $location,
                'description' => 'Lokasi untuk Logbook Pos Jaga',
                'creationID' => 1
            ]);
        }

        foreach ($xrayCabinLocations as $location) {
            Location::create([
                'name' => $location,
                'description' => 'Lokasi untuk Form Xray Cabin',
                'creationID' => 1
            ]);
        }
    }
}
