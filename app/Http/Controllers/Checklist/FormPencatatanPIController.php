<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Models\FormPencatatanPI;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormPencatatanPIController extends Controller
{
    public function indexChecklistPencatatanPI()
    {
        $pencatatanPI = FormPencatatanPI::orderBy('date', 'desc')->orderBy('in_time', 'desc')->get();
        return view('checklist.pencatatanpi.formPencatatanPI', compact('pencatatanPI'));
    }

    public function storeChecklistPencatatanPI(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'grup' => 'required|string|max:255',
                'in_time' => 'required|date_format:H:i',
                'out_time' => 'nullable|date_format:H:i',
                'name_person' => 'required|string|max:255',
                'agency' => 'required|string|max:255',
                'jenis_PI' => 'required|string|max:255',
                'in_quantity' => 'required|string|max:255',
                'out_quantity' => 'nullable|string|max:255',
                'summary' => 'nullable|string',
                'status' => 'nullable|enum:draft,submitted,approved',
                'approved_id' => 'nullable|integer',
                'approvedSignature' => 'nullable|string'
            ]);

            FormPencatatanPI::create($request->all());

            return redirect()->route('checklist.pencatatanpi.index')->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi.');
        }
    }

    public function destroyChecklistPencatatanPI($id)
    {
        $pencatatan = FormPencatatanPI::findOrFail($id);
        $pencatatan->delete();

        return redirect()->route('checklist.pencatatanpi.index')->with('success', 'Data berhasil dihapus.');
    }

    public function editChecklistPencatatanPI($id)
    {
        $pencatatanPI = FormPencatatanPI::findOrFail($id);
        $supervisorRole = Role::where('name', 'supervisor')->first();
        $supervisors = User::where('role_id', $supervisorRole->id)->get();
        return view('checklist.pencatatanpi.detailFormPencatatanPI', compact('pencatatanPI', 'supervisors'));
    }

    public function updateChecklistPencatatanPI(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'grup' => 'required|string|max:255',
            'in_time' => 'required|date_format:H:i',
            'out_time' => 'required|date_format:H:i',
            'name_person' => 'required|string|max:255',
            'agency' => 'required|string|max:255',
            'jenis_PI' => 'required|string|max:255',
            'in_quantity' => 'required|string|max:255',
            'out_quantity' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'senderSignature' => 'required|string',
            'approved_id' => 'required|integer|exists:users,id',
        ]);

        $pencatatanPI = FormPencatatanPI::findOrFail($id);
        $signature = $request->input('senderSignature');
        if (preg_match('/^data:image\/(\w+);base64,/', $signature, $type)) {
            $signature = substr($signature, strpos($signature, ',') + 1);
        }
        try {
            $pencatatanPI->update([
                'date' => $request->date,
                'grup' => $request->grup,
                'in_time' => $request->in_time,
                'out_time' => $request->out_time,
                'name_person' => $request->name_person,
                'agency' => $request->agency,
                'jenis_PI' => $request->jenis_PI,
                'in_quantity' => $request->in_quantity,
                'out_quantity' => $request->out_quantity,
                'summary' => $request->summary,
                'status' => 'submitted',
                'sender_id' => Auth::id(),
                'senderSignature' => $signature,
                'approved_id' => $request->approved_id,
            ]);
            return redirect()->route('checklist.pencatatanpi.index')->with('success', 'Data berhasil diperbarui dan dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Harap isi semua field yang diperlukan. Error: ');
        }
    }

    public function showDetailPencatatanPI($id)
    {
        $pencatatanPI = FormPencatatanPI::with(['sender', 'approver'])->findOrFail($id);
        return view('supervisor.detailFormPencatatanPI', compact('pencatatanPI'));
    }

    public function storeSignatureApproved(Request $request, $id)
    {
        $request->validate([
            'approvedSignature' => 'required|string',
        ]);

        $pencatatanPI = FormPencatatanPI::findOrFail($id);

        if (Auth::id() !== $pencatatanPI->approved_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki otorisasi untuk menandatangani checklist ini.');
        }

        $signature = $request->input('approvedSignature');
        if (preg_match('/^data:image\/(\w+);base64,/', $signature, $type)) {
            $signature = substr($signature, strpos($signature, ',') + 1);
        }

        // Gunakan update() untuk konsistensi
        $pencatatanPI->update([
            'approvedSignature' => $signature,
            'status' => 'approved'
        ]);

        return redirect()->route('supervisor.form-pencatatan-pi.detail', $pencatatanPI->id)
            ->with('success', 'Formulir berhasil disetujui.');
    }
}
