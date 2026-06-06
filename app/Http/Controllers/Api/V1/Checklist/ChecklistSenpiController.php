<?php

namespace App\Http\Controllers\Api\V1\Checklist;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ChecklistSenpi;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChecklistSenpiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $checklists = ChecklistSenpi::where('created_by', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        return $this->successResponse($checklists, 'Data Checklist Senpi berhasil diambil');
    }

    public function show($id)
    {
        $checklist = ChecklistSenpi::with(['creator', 'approver'])->find($id);

        if (!$checklist) {
            return $this->notFoundResponse('Checklist Senpi tidak ditemukan');
        }

        return $this->successResponse($checklist, 'Detail Checklist Senpi berhasil diambil');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'waktu' => 'required|date_format:H:i',
            'nama_pemilik' => 'required|string',
            'jenis_senpi' => 'required|string',
            'no_senpi' => 'required|string',
            'jumlah_peluru' => 'required|integer',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $checklist = ChecklistSenpi::create([
                'id' => $this->generateChecklistId(),
                'date' => $request->date,
                'waktu' => $request->waktu,
                'nama_pemilik' => $request->nama_pemilik,
                'jenis_senpi' => $request->jenis_senpi,
                'no_senpi' => $request->no_senpi,
                'jumlah_peluru' => $request->jumlah_peluru,
                'keterangan' => $request->keterangan,
                'created_by' => Auth::id(),
                'status' => 'draft',
            ]);

            return $this->successResponse($checklist, 'Checklist Senpi berhasil dibuat', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function signatureSend(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
            'approved_by' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $checklist = ChecklistSenpi::find($id);
            if (!$checklist) return $this->notFoundResponse('Checklist Senpi tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $checklist->creator_signature = $signature;
            $checklist->approved_by = $request->approved_by;
            $checklist->status = 'submitted';
            $checklist->save();

            return $this->successResponse($checklist, 'Checklist Senpi berhasil diserahkan');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function signatureApprove(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $checklist = ChecklistSenpi::find($id);
            if (!$checklist) return $this->notFoundResponse('Checklist Senpi tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $checklist->approver_signature = $signature;
            $checklist->status = 'approved';
            $checklist->save();

            return $this->successResponse($checklist, 'Checklist Senpi berhasil disetujui');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    private function generateChecklistId()
    {
        $prefix = 'CS';
        $date = Carbon::now()->format('ymd');
        
        $lastRecord = ChecklistSenpi::where('id', 'like', $prefix . '-' . $date . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($lastRecord) {
            $parts = explode('-', $lastRecord->id);
            $nextSeq = intval(end($parts)) + 1;
        }

        return $prefix . '-' . $date . '-' . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
    }
}
