<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Models\ChecklistSenpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChecklistSenpiController extends Controller
{
    public function indexChecklistSenpi()
    {
        $currentuser = Auth::user();
        $senpi = ChecklistSenpi::orderBy('date', 'desc')->get();

        return view('checklist.checklistSenpi', compact('senpi', 'currentuser'));
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

        // Generate unique ID
        $validated['id'] = (string) Str::uuid();

        ChecklistSenpi::create($validated);

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
