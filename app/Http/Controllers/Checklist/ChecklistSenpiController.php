<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Models\ChecklistSenpi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChecklistSenpiController extends Controller
{
    public function indexChecklistSenpi(Request $request)
    {
        $currentuser = Auth::user();

        // 1. Mulai dengan query builder, bukan langsung mengeksekusi
        $query = ChecklistSenpi::query();

        // 2. Cek apakah ada input 'search' dari view
        if ($request->filled('search')) {
            $search = $request->search;

            // 3. Tambahkan kondisi WHERE untuk mencari di beberapa kolom sekaligus
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('agency', 'LIKE', "%{$search}%")
                    ->orWhere('licenseNumber', 'LIKE', "%{$search}%");
            });
        }

        // 4. Urutkan dan gunakan paginate() untuk memecah data per halaman
        $senpi = $query->orderBy('date', 'desc')->paginate(10); // Anda bisa ganti 10 sesuai kebutuhan

        // 5. Kembalikan view dengan data yang sudah difilter dan dipaginasi
        return view('checklist.senpi.checklistSenpi', compact('senpi', 'currentuser'));
    }

    public function storeChecklistSenpi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'agency' => 'nullable|string|max:255',
            'flightNumber' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'typeSenpi' => 'nullable|string|max:255',
            'quantitySenpi' => 'nullable|integer|min:0',
            'quantityMagazine' => 'nullable|integer|min:0',
            'quantityBullet' => 'nullable|integer|min:0',
            'licenseNumber' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Generate custom ID with format: SNP-YYMMDD-AGENCY-XXXX
        $dateString = Carbon::parse($validated['date'])->format('ymd');
        $agencyString = !empty($validated['agency']) ? strtoupper(substr($validated['agency'], 0, 3)) : 'GEN'; // Ambil 3 karakter pertama dari agency atau 'GEN' jika kosong

        do {
            $randomPart = strtoupper(Str::random(4));
            $id = "SNP-{$dateString}-{$agencyString}";
        } while (ChecklistSenpi::find($id));

        $validated['id'] = $id;

        ChecklistSenpi::create([
            'id' => $id,
            'date' => $validated['date'],
            'name' => $validated['name'],
            'agency' => $validated['agency'],
            'flightNumber' => $validated['flightNumber'],
            'destination' => $validated['destination'],
            'typeSenpi' => $validated['typeSenpi'],
            'quantitySenpi' => $validated['quantitySenpi'],
            'quantityMagazine' => $validated['quantityMagazine'],
            'quantityBullet' => $validated['quantityBullet'],
            'licenseNumber' => $validated['licenseNumber'],
        ]);

        return redirect()
            ->route('checklist.senpi.index')
            ->with('success', 'Checklist senjata api berhasil disimpan.');
    }

    public function updateChecklistSenpi(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'agency' => 'nullable|string|max:255',
            'flightNumber' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'typeSenpi' => 'nullable|string|max:255',
            'quantitySenpi' => 'nullable|integer|min:0',
            'quantityMagazine' => 'nullable|integer|min:0',
            'quantityBullet' => 'nullable|integer|min:0',
            'licenseNumber' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $checklist = ChecklistSenpi::findOrFail($id);
        $checklist->update($validated);

        return redirect()
            ->route('checklist.senpi.index')
            ->with('success', 'Checklist senjata api berhasil diperbarui.');
    }

    public function destroyChecklistSenpi($id)
    {
        try {
            $checklist = ChecklistSenpi::findOrFail($id);
            $checklist->delete();

            return redirect()
                ->route('checklist.senpi.index')
                ->with('success', 'Checklist senjata api berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('checklist.senpi.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
