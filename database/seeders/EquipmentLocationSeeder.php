<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Location;
use App\Models\EquipmentLocation;
use Illuminate\Database\Seeder;

class EquipmentLocationSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Mendapatkan semua equipment
        $hhmd = Equipment::where('name', 'hhmd')->first();
        $wtmd = Equipment::where('name', 'wtmd')->first();
        $xrayBagasi = Equipment::where('name', 'xraybagasi')->first();
        $xrayCabin = Equipment::where('name', 'xraycabin')->first();

        // Pastikan equipment ada
        if (!$hhmd || !$wtmd || !$xrayBagasi || !$xrayCabin) {
            $this->command->error('Some equipment not found. Please run EquipmentSeeder first.');
            return;
        }

        // Data untuk HHMD dengan berbagai merk dan lokasi
        // Data untuk HHMD
        $hhmdData = [
            ['location_name' => 'HBSCP', 'merk_type' => 'CEIA 35100130207','certificateInfo'=>'-', 'description' => 'HHMD untuk pemeriksaan di HBSCP'],
            ['location_name' => 'PSCP Utara', 'merk_type' => 'CEIA 35100130205','certificateInfo'=>'-', 'description' => 'HHMD untuk pemeriksaan di PSCP Utara'],
            ['location_name' => 'PSCP Selatan', 'merk_type' => 'CEIA 35100130204','certificateInfo'=>'-', 'description' => 'HHMD untuk pemeriksaan di PSCP Selatan'],
            ['location_name' => 'Pos Timur', 'merk_type' => 'CEIA 35100130199','certificateInfo'=>'-', 'description' => 'HHMD untuk pemeriksaan di Pos Timur'],
            ['location_name' => 'Pos Barat', 'merk_type' => 'CEIA 35100130200','certificateInfo'=>'-', 'description' => 'HHMD untuk pemeriksaan di Pos Barat'],
            ['location_name' => 'Pos Kedatangan', 'merk_type' => 'CEIA 35100130202','certificateInfo'=>'-', 'description' => 'HHMD untuk pemeriksaan di Pos Kedatangan'],
        ];

        // Data untuk WTMD
        $wtmdData = [
            ['location_name' => 'PSCP Utara', 'merk_type' => 'METOR 200 / 397661','certificateInfo'=>'S/WTMD.0039/DKP/I/2014 & 2014-01-07', 'description' => 'WTMD untuk screening PSCP Utara'],
            ['location_name' => 'PSCP Selatan', 'merk_type' => 'METOR 250 / OM 536306','certificateInfo'=>'S/WTMD.0074/DKP/I/2014 & 2014-01-07', 'description' => 'WTMD untuk screening PSCP Selatan'],
            ['location_name' => 'Pos Timur', 'merk_type' => 'Heimann 9506563','certificateInfo'=>'S/WTMD.0073/DKP/I/2014 & 2014-01-07', 'description' => 'WTMD untuk screening Pos Timur'],
        ];
        // Data untuk XRAY Bagasi
        // Data untuk XRAY Bagasi
        $xrayBagasiData = [
            ['location_name' => 'HBSCP Bagasi Timur', 'merk_type' => 'Rapiscan 6183802','certificateInfo'=>'S/XR-B.0125/DKP/IV/2019 & 2019-04-15', 'description' => 'X-Ray Bagasi di HBSCP Timur'],
            ['location_name' => 'HBSCP Bagasi Barat', 'merk_type' => 'Astrophysics ASTKDI80XDX11','certificateInfo'=>'S/XR-B.0097/DKP/VIII/2018 & 2018-08-21', 'description' => 'X-Ray Bagasi di HBSCP Barat'],
        ];
        // Data untuk XRAY Cabin
        // Data untuk XRAY Cabin
        $xrayCabinData = [
            ['location_name' => 'PSCP Cabin Utara', 'merk_type' => 'Heimann Smith 143483','certificateInfo'=>'S/XR-C.8035/DKP/VIII/2020 & 2020-08-31', 'description' => 'X-Ray Cabin di PSCP Utara'],
            ['location_name' => 'PSCP Cabin Selatan', 'merk_type' => 'Astrophysics ASTKDI80SDV34','certificateInfo'=>'S/XR-C.8010/DKP/I/2014 & 2014-01-07', 'description' => 'X-Ray Cabin di PSCP Selatan'],
        ];


        // Insert HHMD equipment locations
        $this->insertEquipmentLocations($hhmd->id, $hhmdData);

        // Insert WTMD equipment locations  
        $this->insertEquipmentLocations($wtmd->id, $wtmdData);

        // Insert XRAY Bagasi equipment locations
        $this->insertEquipmentLocations($xrayBagasi->id, $xrayBagasiData);

        // Insert XRAY Cabin equipment locations
        $this->insertEquipmentLocations($xrayCabin->id, $xrayCabinData);

        $this->command->info('Equipment locations seeded successfully!');
    }

    /**
     * Helper method untuk insert equipment locations
     */
    private function insertEquipmentLocations($equipment_id, $locationData)
    {
        foreach ($locationData as $data) {
            $location = Location::where('name', $data['location_name'])->first();

            if ($location) {
                // Cek apakah kombinasi equipment_id dan location_id sudah ada
                $exists = EquipmentLocation::where('equipment_id', $equipment_id)
                    ->where('location_id', $location->id)
                    ->exists();

                if (!$exists) {
                    EquipmentLocation::create([
                        'equipment_id' => $equipment_id,
                        'location_id' => $location->id,
                        'merk_type' => $data['merk_type'],
                        'certificateInfo' => $data['certificateInfo'],
                        'description' => $data['description'],
                    ]);
                } else {
                    $this->command->warn("Equipment location combination already exists: Equipment {$equipment_id} - Location {$location->id}");
                }
            } else {
                $this->command->error("Location '{$data['location_name']}' not found!");
            }
        }
    }
}