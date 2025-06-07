<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterDataController extends Controller
{

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
