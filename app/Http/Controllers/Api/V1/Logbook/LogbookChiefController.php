<?php

namespace App\Http\Controllers\Api\V1\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\LogbookChief;
use App\Models\LogbookChiefKemajuan;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LogbookChiefController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $logbooks = LogbookChief::with('createdBy')
            ->where('created_by', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        return $this->successResponse($logbooks, 'Data Logbook Chief berhasil diambil');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'team' => 'required|string|max:255',
            'shift' => 'required|in:pagi,malam',
            'chief_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $logbook = LogbookChief::create([
                'id' => $this->generateChiefId(),
                'date' => $request->date,
                'team' => $request->team,
                'shift' => $request->shift,
                'chief_name' => $request->chief_name,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            return $this->successResponse($logbook, 'Logbook Chief berhasil dibuat', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $logbook = LogbookChief::with('kemajuan')->find($id);

        if (!$logbook) {
            return $this->notFoundResponse('Logbook Chief tidak ditemukan');
        }

        return $this->successResponse($logbook, 'Detail Logbook Chief berhasil diambil');
    }

    public function storeKemajuan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'laporan_chief_id' => 'required|exists:logbook_chief,id',
            'time' => 'required|date_format:H:i',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $kemajuan = LogbookChiefKemajuan::create($request->all());
            return $this->successResponse($kemajuan, 'Progress berhasil ditambahkan', 201);
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
            $logbook = LogbookChief::find($id);
            if (!$logbook) return $this->notFoundResponse('Logbook tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $logbook->senderSignature = $signature;
            $logbook->approved_by = $request->approved_by;
            $logbook->status = 'submitted';
            $logbook->save();

            return $this->successResponse($logbook, 'Logbook Chief berhasil diserahkan');
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
            $logbook = LogbookChief::find($id);
            if (!$logbook) return $this->notFoundResponse('Logbook tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $logbook->approvedSignature = $signature;
            $logbook->status = 'approved';
            $logbook->save();

            return $this->successResponse($logbook, 'Logbook Chief berhasil disetujui');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    private function generateChiefId()
    {
        $date = Carbon::now()->format('ymd');
        $prefix = 'LBC-' . $date . '-';
        
        $lastRecord = LogbookChief::where('id', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($lastRecord) {
            $parts = explode('-', $lastRecord->id);
            $nextSeq = intval(end($parts)) + 1;
        }

        return $prefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
    }
}
