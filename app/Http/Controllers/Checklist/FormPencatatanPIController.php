<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Models\FormPencatatanPI;
use App\Models\FormPencatatanPIDetail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormPencatatanPIController extends Controller
{
    public function indexChecklistPencatatanPI()
    {
        $pencatatanPI = FormPencatatanPI::with('details')->orderBy('date', 'desc')->orderBy('in_time', 'desc')->get();
        return view('checklist.pencatatanpi.formPencatatanPI', compact('pencatatanPI'));
    }

    public function create()
    {
        return view('checklist.pencatatanpi.create');
    }

    public function storeChecklistPencatatanPI(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'grup' => 'required|string|max:255',
            'in_time' => 'required|date_format:H:i',
            'out_time' => 'nullable|date_format:H:i',
            'name_person' => 'required|string|max:255',
            'agency' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.jenis_pi' => 'required|string|max:255',
            'items.*.in_quantity' => 'required|string|max:255',
            'items.*.out_quantity' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $pencatatanPI = FormPencatatanPI::create([
                'date' => $request->date,
                'grup' => $request->grup,
                'in_time' => $request->in_time,
                'out_time' => $request->out_time,
                'name_person' => $request->name_person,
                'agency' => $request->agency,
                'summary' => $request->summary,
                'status' => 'draft',
                'sender_id' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                $pencatatanPI->details()->create([
                    'jenis_pi' => $item['jenis_pi'],
                    'in_quantity' => $item['in_quantity'],
                    'out_quantity' => $item['out_quantity'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('checklist.pencatatanpi.index')->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi. Error: ' . $e->getMessage())->withInput();
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
        $pencatatanPI = FormPencatatanPI::with('details')->findOrFail($id);
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
            'summary' => 'nullable|string',
            'senderSignature' => 'required|string',
            'approved_id' => 'required|integer|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.jenis_pi' => 'required|string|max:255',
            'items.*.in_quantity' => 'required|string|max:255',
            'items.*.out_quantity' => 'nullable|string|max:255',
        ]);

        $pencatatanPI = FormPencatatanPI::findOrFail($id);
        $signature = $request->input('senderSignature');
        if (preg_match('/^data:image\/(\w+);base64,/', $signature, $type)) {
            $signature = substr($signature, strpos($signature, ',') + 1);
        }

        DB::beginTransaction();
        try {
            $pencatatanPI->update([
                'date' => $request->date,
                'grup' => $request->grup,
                'in_time' => $request->in_time,
                'out_time' => $request->out_time,
                'name_person' => $request->name_person,
                'agency' => $request->agency,
                'summary' => $request->summary,
                'status' => 'submitted',
                'sender_id' => Auth::id(),
                'senderSignature' => $signature,
                'approved_id' => $request->approved_id,
            ]);

            // Delete old details and create new ones
            $pencatatanPI->details()->delete();
            foreach ($request->items as $item) {
                $pencatatanPI->details()->create([
                    'jenis_pi' => $item['jenis_pi'],
                    'in_quantity' => $item['in_quantity'],
                    'out_quantity' => $item['out_quantity'] ?? null,
                ]);
            }
            
            DB::commit();

            return redirect()->route('checklist.pencatatanpi.index')->with('success', 'Data berhasil diperbarui dan dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Harap isi semua field yang diperlukan. Error: ' . $e->getMessage());
        }
    }

    public function showDetailPencatatanPI($id)
    {
        $pencatatanPI = FormPencatatanPI::with(['sender', 'approver', 'details'])->findOrFail($id);
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
