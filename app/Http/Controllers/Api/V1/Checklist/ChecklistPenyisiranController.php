<?php

namespace App\Http\Controllers\Api\V1\Checklist;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ChecklistPenyisiran;
use App\Models\ChecklistPenyisiranDetail;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChecklistPenyisiranController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $checklists = ChecklistPenyisiran::with('sender')
            ->where('sender_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        return $this->successResponse($checklists, 'Data Checklist Penyisiran berhasil diambil');
    }

    public function show($id)
    {
        $checklist = ChecklistPenyisiran::with(['sender', 'details'])->find($id);

        if (!$checklist) {
            return $this->notFoundResponse('Checklist Penyisiran tidak ditemukan');
        }

        return $this->successResponse($checklist, 'Detail Checklist Penyisiran berhasil diambil');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'grup' => 'required|string',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            DB::beginTransaction();

            $checklist = ChecklistPenyisiran::create([
                'id' => $this->generateChecklistId(),
                'date' => $request->date,
                'grup' => $request->grup,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'sender_id' => Auth::id(),
                'status' => 'draft',
            ]);

            DB::commit();
            return $this->successResponse($checklist, 'Checklist Penyisiran berhasil dibuat', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function storeDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checklist_penyisiran_id' => 'required|exists:checklist_penyisirans,id',
            'area_objek' => 'required|string',
            'hasil' => 'required|boolean',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $detail = ChecklistPenyisiranDetail::create($request->all());
            return $this->successResponse($detail, 'Detail Checklist Penyisiran berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function signatureSend(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
            'approved_id' => 'required|exists:users,id',
            'received_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $checklist = ChecklistPenyisiran::find($id);
            if (!$checklist) return $this->notFoundResponse('Checklist Penyisiran tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $checklist->senderSignature = $signature;
            $checklist->approved_id = $request->approved_id;
            $checklist->received_id = $request->received_id;
            $checklist->status = 'submitted';
            $checklist->save();

            return $this->successResponse($checklist, 'Checklist Penyisiran berhasil diserahkan');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function signatureReceive(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $checklist = ChecklistPenyisiran::find($id);
            if (!$checklist) return $this->notFoundResponse('Checklist Penyisiran tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $checklist->receivedSignature = $signature;
            $checklist->save();

            return $this->successResponse($checklist, 'Checklist Penyisiran berhasil diterima');
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
            $checklist = ChecklistPenyisiran::find($id);
            if (!$checklist) return $this->notFoundResponse('Checklist Penyisiran tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $checklist->approvedSignature = $signature;
            $checklist->status = 'approved';
            $checklist->save();

            return $this->successResponse($checklist, 'Checklist Penyisiran berhasil disetujui');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    private function generateChecklistId()
    {
        $prefix = 'CP';
        $date = Carbon::now()->format('ymd');
        
        $lastRecord = ChecklistPenyisiran::where('id', 'like', $prefix . '-' . $date . '-%')
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
