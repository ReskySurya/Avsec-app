<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Equipment;
use App\Models\EquipmentLocation;
use App\Models\Location;
use App\Models\ProhibitedItem;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterDataController extends Controller
{
    //EquipmentLocation management
    public function indexEquipmentLocation(Request $request)
    {
        $searchTable = $request->input('search_table');

        $equipmentLocations = EquipmentLocation::with(['equipment', 'location'])
            ->whereHas('equipment')
            ->whereHas('location')
            ->when($searchTable, function ($query) use ($searchTable) {
                $query->where(function ($q) use ($searchTable) {
                    $q->whereHas('equipment', function ($subQ) use ($searchTable) {
                        $subQ->where('name', 'like', "%{$searchTable}%");
                    })
                        ->orWhereHas('location', function ($subQ) use ($searchTable) {
                            $subQ->where('name', 'like', "%{$searchTable}%");
                        })
                        ->orWhere('merkType', 'like', "%{$searchTable}%")
                        ->orWhere('description', 'like', "%{$searchTable}%");
                });
            })
            ->get();
        $equipmentList = Equipment::with('creator')->orderBy('name')->get();

        $locationList = Location::with('creator')->orderBy('name')->get();
        // Equipment
        // $equipmentList = Equipment::with('creator')
        //     ->when($searchTable, function ($query) use ($searchTable) {
        //         $query->where('name', 'like', "%{$searchTable}%")
        //             ->orWhere('merkType', 'like', "%{$searchTable}%")
        //             ->orWhere('description', 'like', "%{$searchTable}%");
        //     })
        //     ->orderBy('id')
        //     ->paginate(5, ['*'], 'equipmentPage')
        //     ->appends(request()->query());

        // // Location
        // $locationList = Location::with('creator')
        //     ->when($searchTable, function ($query) use ($searchTable) {
        //         $query->where('name', 'like', "%{$searchTable}%")
        //             ->orWhere('description', 'like', "%{$searchTable}%");
        //     })
        //     ->orderBy('id')
        //     ->paginate(5, ['*'], 'locationPage')
        //     ->appends(request()->query());

        return view('master-data.equipment-locations.index', compact(
            'equipmentLocations',
            'equipmentList',
            'locationList',
            'searchTable'
        ));
    }

    public function storeEquipment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:equipment,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            Equipment::create([
                'name' => $request->name,
                'description' => $request->description,
                'creationID' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Equipment berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error creating equipment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan equipment: ' . $e->getMessage());
        }
    }

    public function storeLocation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            Location::create([
                'name' => $request->name,
                'description' => $request->description,
                'creationID' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Location berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error creating location: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan location: ' . $e->getMessage());
        }
    }

    public function storeEquipmentLocation(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'location_id' => 'required|exists:locations,id',
            'merk_type' => 'nullable|string|max:255',
            'certificateInfo' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            // Check if relationship already exists
            $exists = EquipmentLocation::where('equipment_id', $request->equipment_id)
                ->where('location_id', $request->location_id)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Relasi equipment dan location sudah ada!');
            }

            // Create new equipment location relationship
            EquipmentLocation::create([
                'equipment_id' => $request->equipment_id,
                'location_id' => $request->location_id,
                'merk_type' => $request->merk_type,
                'certificateInfo' => $request->certificate,
                'description' => $request->description,
            ]);

            return redirect()->back()->with('success', 'Equipment location berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error creating equipment location: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan equipment location: ' . $e->getMessage());
        }
    }

    public function updateEquipment(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:equipment,name,' . $id,
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
            Log::error('Error updating equipment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui equipment: ' . $e->getMessage());
        }
    }

    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $id,
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
            Log::error('Error updating location: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui location: ' . $e->getMessage());
        }
    }

    public function updateEquipmentLocation(Request $request, $id)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'location_id' => 'required|exists:locations,id',
            'merk_type' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $equipmentLocation = EquipmentLocation::findOrFail($id);

            Log::info('Updating equipment location relationship', [
                'id' => $id,
                'old_equipment_id' => $equipmentLocation->equipment_id,
                'old_location_id' => $equipmentLocation->location_id,
                'new_equipment_id' => $request->equipment_id,
                'new_location_id' => $request->location_id,
            ]);

            // Check if the new combination already exists (but not the current record)
            $exists = EquipmentLocation::where('equipment_id', $request->equipment_id)
                ->where('location_id', $request->location_id)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Relasi equipment dan location yang baru sudah ada!');
            }

            // Update the relationship
            $equipmentLocation->update([
                'equipment_id' => $request->equipment_id,
                'location_id' => $request->location_id,
                'merk_type' => $request->merk_type,
                'description' => $request->description,
            ]);

            return redirect()->back()->with('success', 'Equipment location berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('Error updating equipment location: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengupdate equipment location: ' . $e->getMessage());
        }
    }

    public function destroyEquipment($id)
    {
        try {
            $equipment = Equipment::findOrFail($id);

            // Check if equipment has relationships
            if ($equipment->equipmentLocations()->exists()) {
                return redirect()->back()->with('error', 'Equipment tidak dapat dihapus karena masih memiliki relasi dengan location!');
            }

            $equipment->delete();

            return redirect()->back()->with('success', 'Equipment berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting equipment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus equipment: ' . $e->getMessage());
        }
    }

    public function destroyLocation($id)
    {
        try {
            $location = Location::findOrFail($id);

            // Check if location has relationships
            if ($location->equipmentLocations()->exists()) {
                return redirect()->back()->with('error', 'Location tidak dapat dihapus karena masih memiliki relasi dengan equipment!');
            }

            $location->delete();

            return redirect()->back()->with('success', 'Location berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting location: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus location: ' . $e->getMessage());
        }
    }

    public function destroyEquipmentLocation($id)
    {
        try {
            $equipmentLocation = EquipmentLocation::findOrFail($id);
            $equipmentLocation->delete();

            return redirect()->back()->with('success', 'Relasi equipment dan location berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting equipment location: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus relasi equipment dan location: ' . $e->getMessage());
        }
    }

    // User Management
    public function indexUserManagement()
    { {
            // Ambil data officer dan supervisor dari database
            $officers = User::whereHas('role', function ($query) {
                $query->where('name', Role::OFFICER);
            })->get();

            $supervisors = User::whereHas('role', function ($query) {
                $query->where('name', Role::SUPERVISOR);
            })->get();

            $superadmins = User::whereHas('role', function ($query) {
                $query->where('name', Role::SUPERADMIN);
            })->get();

            $roles = Role::all();

            return view('master-data.user-management.index', compact('officers', 'supervisors', 'superadmins', 'roles'));
        }
    }

    public function getUserManagement($id)
    {

        $user = User::with('role')->findOrFail($id);
        return response()->json($user);
    }

    public function storeUserManagement(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'role_id' => 'required|exists:roles,id',
            ]);

            // Begin database transaction
            DB::beginTransaction();

            // Create new user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
                'role_id' => $validatedData['role_id'],
                'nip' => $request->nip,
                'lisensi' => $request->lisensi
            ]);

            // Commit transaction
            DB::commit();

            return redirect()
                ->route('user-management.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            // Log the error
            Log::error('Failed to create user: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create user. Please try again.');
        }
    }

    public function updateUserManagement(Request $request)
    {
        try {
            // ... (Validasi dan logika update Anda tetap sama) ...
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->user_id,
                'role_id' => 'required|exists:roles,id',
                'nip' => 'nullable|string',
                'lisensi' => 'nullable|string'
            ]);

            DB::beginTransaction();
            $user = User::findOrFail($request->user_id);
            $user->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role_id' => $validatedData['role_id'],
                'nip' => $request->nip,
                'lisensi' => $request->lisensi
            ]);
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'required|string|min:8'
                ]);
                $user->update([
                    'password' => bcrypt($request->password)
                ]);
            }
            DB::commit();

            // ---- PERUBAHAN DIMULAI DARI SINI ----

            // Ambil data user yang sudah terupdate beserta relasinya
            $updatedUser = User::with('role')->find($user->id);

            // Kirim response JSON jika ini adalah request AJAX/fetch
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully.',
                    'user' => $updatedUser
                ]);
            }

            // Redirect untuk non-AJAX request (fallback)
            return redirect()
                ->route('user-management.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user: ' . $e->getMessage());

            // ---- PERUBAHAN KECIL DI BLOK CATCH ----
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user. Please try again.',
                    'errors' => $e->getMessage() // Mengirim pesan error lebih detail
                ], 500); // Gunakan status code error
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update user. Please try again.');
        }
    }

    public function destroyUserManagement(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Find the user
            $user = User::findOrFail($id);

            // Delete the user
            $user->delete();

            DB::commit();

            // Return JSON response for AJAX requests
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User deleted successfully.'
                ]);
            }

            // Regular redirect response
            return redirect()
                ->route('user-management.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete user.',
                    'errors' => $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    // Tenant Management
    public function indexTenantManagement()
    {
        $tenantList = Tenant::orderBy('tenantID')->get();
        return view(
            'master-data.tenant-management.index',
            ['tenantList' => $tenantList]
        );
    }

    public function storeTenant(Request $request)
    {
        $request->validate([
            'tenant_name' => 'required|string|max:255|unique:tenants,tenant_name',
            'signature' => 'nullable|string', // Validasi untuk signature
        ]);

        try {
            $tenantID = Tenant::generateTenantID();

            // Process signature data jika ada
            $signatureData = null;
            if ($request->signature && $request->signature !== '') {
                // Remove data:image/png;base64, prefix jika ada
                $signatureData = str_replace('data:image/png;base64,', '', $request->signature);
            }

            Tenant::create([
                'tenantID' => $tenantID,
                'tenant_name' => $request->tenant_name,
                'supervisorName' => $request->supervisorName, // Simpan nama supervisor
                'supervisorSignature' => $signatureData, // Simpan signature
            ]);

            return redirect()->back()->with('success', 'Tenant berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error creating tenant: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan tenant: ' . $e->getMessage());
        }
    }

    public function updateTenant(Request $request, $tenantID)
    {
        $request->validate([
            'tenant_name' => 'required|string|max:255|unique:tenants,tenant_name,' . $tenantID . ',tenantID',
        ]);

        try {
            $tenant = Tenant::findOrFail($tenantID);

            // Hanya update nama tenant saja
            $tenant->update([
                'tenant_name' => $request->tenant_name,
            ]);

            return redirect()->back()->with('success', 'Tenant berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating tenant: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui tenant: ' . $e->getMessage());
        }
    }

    public function destroyTenant($id)
    {
        try {
            $tenant = Tenant::find($id);
            if (!$tenant) {
                return redirect()->back()->with('error', 'Tenant tidak ditemukan.');
            }
            $tenant->delete();
            return redirect()->back()->with('success', 'Tenant berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting tenant: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus tenant: ' . $e->getMessage());
        }
    }

    // Prohibited Items
    public function indexProhibitedItem($tenantID)
    {
        $tenant = Tenant::all()
            ->where('tenantID', $tenantID)
            ->first();
        $prohibitedItems = ProhibitedItem::where('tenantID', $tenantID)
            ->orderBy('items_name')
            ->get();

        return view('master-data.tenant-management.prohibitedItems', [
            'tenant' => $tenant,
            'tenantID' => $tenantID,
            'prohibitedItems' => $prohibitedItems,
        ]);
    }

    public function storeProhibitedItem(Request $request)
    {
        $request->validate([
            'tenantID'   => 'required|exists:tenants,tenantID',
            'items_name'  => 'required|string|max:255',
            'quantity'  => 'required|integer|min:1',
        ]);

        try {
            ProhibitedItem::create([
                'tenantID' => $request->tenantID,
                'items_name'  => $request->items_name,
                'quantity'  => $request->quantity,
            ]);

            return redirect()->back()->with('success', 'Prohibited Item berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Prohibited Item. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function updateProhibitedItem(Request $request, $id)
    {
        try {
            // Validasi input - sesuai dengan struktur database Anda
            $validatedData = $request->validate([
                'tenantID'   => 'required|exists:tenants,tenantID',
                'items_name'  => 'required|string|max:255',
                'quantity'  => 'required|integer|min:1',
            ]);

            // Cari record berdasarkan ID
            $prohibitedItem = ProhibitedItem::findOrFail($id);

            // Update data sesuai dengan struktur database
            $prohibitedItem->update([
                'tenantID'   => $validatedData['tenantID'],
                'items_name'  => $validatedData['items_name'],
                'quantity'    => $validatedData['quantity'],
            ]);

            return redirect()->back()->with('success', 'Item berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi data');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data yang ingin diperbarui tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroyProhibitedItem($id)
    {
        try {
            $prohibitedItem = ProhibitedItem::findOrFail($id);
            $prohibitedItem->delete();

            return redirect()->back()->with('success', 'Prohibited Item berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting prohibited item: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus Prohibited Item: ' . $e->getMessage());
        }
    }

    //Checklist Items Kendaraan
    public function indexChecklistItems()
    {
        $checklistItems = ChecklistItem::all();

        return view('master-data.checklist-items.index', [
            'checklistItems' => $checklistItems
        ]);
    }

    public function storeChecklistItems(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:checklist_items,name',
            'category' => 'required|string|max:50',
            'type' => 'required|string|max:50',
        ]);

        try {
            ChecklistItem::create([
                'name' => $request->name,
                'category' => $request->category,
                'type' => $request->type,
            ]);

            return redirect()->back()->with('success', 'Item berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error creating item: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan Item: ' . $e->getMessage());
        }
    }

    public function updateChecklistItems(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:checklist_items,name,' . $id,
            'category' => 'required|string|max:50',
            'type' => 'required|string|max:50',
        ]);

        try {
            $item = ChecklistItem::findOrFail($id);

            $item->update([
                'name' => $request->name,
                'category' => $request->category,
                'type' => $request->type,
            ]);

            return redirect()->back()->with('success', 'Item berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating item: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui Item: ' . $e->getMessage());
        }
    }

    public function destroyChecklistItems($id)
    {
        try {
            $item = ChecklistItem::findOrFail($id);
            $item->delete();

            return redirect()->back()->with('success', 'Item berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting item: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus Item: ' . $e->getMessage());
        }
    }
}
