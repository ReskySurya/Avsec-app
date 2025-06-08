<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Location;
use Auth;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function indexEquipment()
    {
        // $equipments = Equipment::with('creator')->get();
        // $locations = Location::with('creator')->get();
        $equipments = Equipment::with(['locations', 'creator'])->get();
        $locations = Location::with(['equipments', 'creator'])->get();
        return view('master-data.equipment-locations.index-equipment', compact('equipments', 'locations'));
    }

    /**
     * Store a new equipment
     */
    public function storeEquipment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            Equipment::create([
                'name' => $request->name,
                'description' => $request->description,
                'creationID' => Auth::id(), // ID user yang sedang login
            ]);

            return redirect()->back()->with('success', 'Equipment berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan equipment: ' . $e->getMessage());
        }
    }

    /**
     * Store a new location
     */
    public function storeLocation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            Location::create([
                'name' => $request->name,
                'description' => $request->description,
                'creationID' => Auth::id(), // ID user yang sedang login
            ]);

            return redirect()->back()->with('success', 'Location berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan location: ' . $e->getMessage());
        }
    }

    /**
     * Store equipment location relationship
     */
    public function storeEquipmentLocation(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $equipment = Equipment::findOrFail($request->equipment_id);

            // Check if relationship already exists
            if ($equipment->locations()->where('location_id', $request->location_id)->exists()) {
                return redirect()->back()->with('error', 'Relasi equipment dan location sudah ada!');
            }

            $equipment->locations()->attach($request->location_id, [
                'description' => $request->description,
            ]);

            return redirect()->back()->with('success', 'Equipment location berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan equipment location: ' . $e->getMessage());
        }
    }

    // Update Equipment
    public function updateEquipment(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $equipment = Equipment::findOrFail($id);
            $equipment->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return redirect()->back()->with('success', 'Equipment berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui equipment: ' . $e->getMessage());
        }
    }
    // Update Location
    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $location = Location::findOrFail($id);
            $location->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            
            return redirect()->back()->with('success', 'Location berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui location: ' . $e->getMessage());
        }
    }

    // Hapus Equipment
    public function destroyEquipment($id)
    {
        try {
            $equipment = Equipment::findOrFail($id);
            $equipment->delete();

            return redirect()->back()->with('success', 'Equipment berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus equipment: ' . $e->getMessage());
        }
    }

    // Hapus Location
    public function destroyLocation($id)
    {
        try {
            $location = Location::findOrFail($id);
            $location->delete();

            return redirect()->back()->with('success', 'Location berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus location: ' . $e->getMessage());
        }
    }

    // Hapus Equipment Location Relationship
    public function destroyEquipmentLocation($equipmentId, $locationId)
    {
        try {
            $equipment = Equipment::findOrFail($equipmentId);
            $equipment->locations()->detach($locationId);
            return redirect()->back()->with('success', 'Relasi equipment dan location berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus relasi equipment dan location: ' . $e->getMessage()); 
        }
    }
}
