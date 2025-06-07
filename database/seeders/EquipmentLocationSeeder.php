<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Location;
use Illuminate\Database\Seeder;

class EquipmentLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mendapatkan semua equipment dan location
        $hhmd = Equipment::where('name', 'hhmd')->first();
        $wtmd = Equipment::where('name', 'wtmd')->first();
        $xrayBagasi = Equipment::where('name', 'xraybagasi')->first();
        $xrayCabin = Equipment::where('name', 'xraycabin')->first();

        // Lokasi untuk HHMD
        $hhmdLocations = Location::whereIn('name', [
            'HBSCP',
            'Pos Timur',
            'Pos Barat',
            'PSCP Utara',
            'PSCP Selatan',
            'Pos Kedatangan'
        ])->get();

        // Lokasi untuk WTMD - menggunakan lokasi yang sama dengan HHMD untuk lokasi yang sama
        $wtmdLocations = Location::whereIn('name', [
            'Pos Timur',
            'PSCP Utara',
            'PSCP Selatan'
        ])->get();

        // Lokasi untuk XRAY Bagasi
        $xrayBagasiLocations = Location::whereIn('name', [
            'HBSCP Bagasi Timur',
            'HBSCP Bagasi Barat'
        ])->get();

        // Lokasi untuk XRAY Cabin
        $xrayCabinLocations = Location::whereIn('name', [
            'PSCP Cabin Utara',
            'PSCP Cabin Selatan'
        ])->get();

        // Menghubungkan HHMD dengan lokasinya
        foreach ($hhmdLocations as $location) {
            $hhmd->locations()->attach($location->id, [
                'description' => 'HHMD di ' . $location->name
            ]);
        }

        // Menghubungkan WTMD dengan lokasinya
        foreach ($wtmdLocations as $location) {
            $wtmd->locations()->attach($location->id, [
                'description' => 'WTMD di ' . $location->name
            ]);
        }

        // Menghubungkan XRAY Bagasi dengan lokasinya
        foreach ($xrayBagasiLocations as $location) {
            $xrayBagasi->locations()->attach($location->id, [
                'description' => 'XRAY Bagasi di ' . $location->name
            ]);
        }

        // Menghubungkan XRAY Cabin dengan lokasinya
        foreach ($xrayCabinLocations as $location) {
            $xrayCabin->locations()->attach($location->id, [
                'description' => 'XRAY Cabin di ' . $location->name
            ]);
        }
    }
}