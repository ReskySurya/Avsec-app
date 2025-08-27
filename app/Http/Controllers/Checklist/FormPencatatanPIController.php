<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Models\FormPencatatanPI;
use Illuminate\Http\Request;

class FormPencatatanPIController extends Controller
{
    public function indexChecklistPencatatanPI()
    {
        $pencatatanPI = FormPencatatanPI::orderBy('date', 'desc')->orderBy('in_time', 'desc')->get();
        return view('checklist.pencatatanpi.formPencatatanPI', compact('pencatatanPI'));
    }

    public function storeChecklistPencatatanPI(Request $request)
    {
        $request->validate([
            'date' => 'required|date|required',
            'grup' => 'nullable|string|max:255|required',
            'in_time' => 'nullable|date_format:H:i|required',
            'out_time' => 'nullable|date_format:H:i',
            'name_person' => 'nullable|string|max:255|required',
            'agency' => 'nullable|string|max:255',
            'jenis_PI' => 'nullable|string|max:255|required',
            'in_quantity' => 'nullable|string|max:255|required',
            'out_quantity' => 'nullable|string|max:255',
            'summary' => 'nullable|string',
        ]);

        FormPencatatanPI::create($request->all());

        return redirect()->route('checklist.pencatatanpi.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function destroyChecklistPencatatanPI($id)
    {
        $pencatatan = FormPencatatanPI::findOrFail($id);
        $pencatatan->delete();

        return redirect()->route('checklist.pencatatanpi.index')->with('success', 'Data berhasil dihapus.');
    }
}
