<?php

namespace App\Http\Controllers\Api\V1\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\LogbookRotasi;
use App\Models\LogbookRotasiDetail;
use App\Models\OfficerRotasiAssignments;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class LogbookRotasiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $logbooks = LogbookRotasi::where('created_by', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        return $this->successResponse($logbooks, 'Data Logbook Rotasi berhasil diambil');
    }

    public function show($id)
    {
        $logbook = LogbookRotasi::with(['details', 'officers.user'])->find($id);

        if (!$logbook) {
            return $this->notFoundResponse('Logbook Rotasi tidak ditemukan');
        }

        return $this->successResponse($logbook, 'Detail Logbook Rotasi berhasil diambil');
    }

    public function storeDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logbook_rotasi_id' => 'required|exists:logbook_rotasi,id',
            'time' => 'required|date_format:H:i',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $detail = LogbookRotasiDetail::create($request->all());
            return $this->successResponse($detail, 'Uraian tugas berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function storeAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logbook_rotasi_id' => 'required|exists:logbook_rotasi,id',
            'officer_id' => 'required|exists:users,id',
            'location' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $assignment = OfficerRotasiAssignments::create($request->all());
            return $this->successResponse($assignment, 'Penugasan officer berhasil ditambahkan', 201);
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
            $logbook = LogbookRotasi::find($id);
            if (!$logbook) return $this->notFoundResponse('Logbook Rotasi tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $logbook->creator_signature = $signature;
            $logbook->approved_by = $request->approved_by;
            $logbook->status = 'submitted';
            $logbook->save();

            return $this->successResponse($logbook, 'Logbook Rotasi berhasil diserahkan');
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
            $logbook = LogbookRotasi::find($id);
            if (!$logbook) return $this->notFoundResponse('Logbook Rotasi tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $logbook->approver_signature = $signature;
            $logbook->status = 'approved';
            $logbook->save();

            return $this->successResponse($logbook, 'Logbook Rotasi berhasil disetujui');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}
