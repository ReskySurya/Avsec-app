<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Location;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    //
    // public function index()
    // {
    //     return view('master-data.equipment-locations.index');
    // }

   public function index()
{
    $equipments = Equipment::with('creator')->get();
    $locations = Location::with('creator')->get();
    return view('master-data.equipment-locations.index', compact('equipments', 'locations'));
}

    /**
     * Menampilkan detail equipment beserta locations
     */
    public function showEquipment($id)  
    {
        $equipment = Equipment::with(['locations', 'creator'])->findOrFail($id);
        return view('master-data.equipment-locations.show-equipment', compact('equipment'));
    }

    /**
     * Menampilkan detail location beserta equipments
     */
    public function showLocation($id)  
    {
        $location = Location::with(['equipments', 'creator'])->findOrFail($id);
        return view('master-data.equipment-locations.show-location', compact('location'));
    }

}
