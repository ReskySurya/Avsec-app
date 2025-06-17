<?php

namespace App\Http\Controllers\DailyTest;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class XrayController extends Controller
{
    public function xrayBagasiLayout()
    {
        // Ambil equipment X-Ray Bagasi
        $xrayBagasiEquipment = Equipment::where('name', 'xraybagasi')->first();


        // Ambil lokasi yang terhubung dengan X-Ray Cabin
        $xrayBagasiLocations = [];
        if ($xrayBagasiEquipment) {
            $xrayBagasiLocations = $xrayBagasiEquipment->locations()
                ->wherePivot('equipment_id', $xrayBagasiEquipment->id)
                ->withPivot('description', 'id')
                ->get();
        }
        return view(
            'daily-test.xrayBagasiLayout',
            [
                'xrayBagasiLocations' => $xrayBagasiLocations
            ]
        );
    }

    public function xrayCabinLayout()
    {
        // Ambil equipment X-Ray Cabin
        $xrayCabinEquipment = Equipment::where('name', 'xraycabin')->first();

        // Ambil lokasi yang terhubung dengan X-Ray Cabin
        $xrayCabinLocations = [];
        if ($xrayCabinEquipment) {
            $xrayCabinLocations = $xrayCabinEquipment->locations()
                ->wherePivot('equipment_id', $xrayCabinEquipment->id)
                ->withPivot('description', 'id')
                ->get();
        }
        return view(
            'daily-test.xrayCabinLayout',
            [
                'xrayCabinLocations' => $xrayCabinLocations
            ]
        );
    }
}
