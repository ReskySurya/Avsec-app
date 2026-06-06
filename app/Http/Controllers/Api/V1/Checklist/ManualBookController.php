<?php

namespace App\Http\Controllers\Api\V1\Checklist;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ManualBook;
use App\Models\ManualBookDetail;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ManualBookController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $manualBooks = ManualBook::where('created_by', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        return $this->successResponse($manualBooks, 'Data Manual Book berhasil diambil');
    }

    public function show($id)
    {
        $manualBook = ManualBook::with(['details', 'creator', 'approver'])->find($id);

        if (!$manualBook) {
            return $this->notFoundResponse('Manual Book tidak ditemukan');
        }

        return $this->successResponse($manualBook, 'Detail Manual Book berhasil diambil');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'shift' => 'required|in:pagi,malam',
            'type' => 'required|in:hbscp,pscp',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $manualBook = ManualBook::create([
                'id' => $this->generateManualBookId($request->type, $request->date),
                'date' => $request->date,
                'shift' => $request->shift,
                'type' => $request->type,
                'created_by' => Auth::id(),
                'status' => 'draft',
            ]);

            return $this->successResponse($manualBook, 'Manual Book berhasil dibuat', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function storeDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'manual_book_id' => 'required|exists:manual_books,id',
            'description' => 'required|string',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
            'hasil' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $detail = ManualBookDetail::create($request->all());
            return $this->successResponse($detail, 'Detail Manual Book berhasil ditambahkan', 201);
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
            $manualBook = ManualBook::find($id);
            if (!$manualBook) return $this->notFoundResponse('Manual Book tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $manualBook->creator_signature = $signature;
            $manualBook->approved_by = $request->approved_by;
            $manualBook->status = 'submitted';
            $manualBook->save();

            return $this->successResponse($manualBook, 'Manual Book berhasil diserahkan');
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
            $manualBook = ManualBook::find($id);
            if (!$manualBook) return $this->notFoundResponse('Manual Book tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $manualBook->approver_signature = $signature;
            $manualBook->status = 'approved';
            $manualBook->save();

            return $this->successResponse($manualBook, 'Manual Book berhasil disetujui');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    private function generateManualBookId(string $type, string $date): string
    {
        $prefix = ($type === 'hbscp') ? 'MBH' : 'MBP';
        $datePart = Carbon::parse($date)->format('ym');

        $lastRecord = ManualBook::where('id', 'like', $prefix . '-' . $datePart . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextSequence = $lastRecord ? ((int) substr($lastRecord->id, -4)) + 1 : 1;
        return $prefix . '-' . $datePart . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
    }
}
