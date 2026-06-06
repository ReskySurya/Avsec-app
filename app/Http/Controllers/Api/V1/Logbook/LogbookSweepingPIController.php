<?php

namespace App\Http\Controllers\Api\V1\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\LogbookSweepingPI;
use App\Models\LogbookSweepingPIDetail;
use App\Models\Tenant;
use App\Models\NoteSweepingPI;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class LogbookSweepingPIController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $logbooks = LogbookSweepingPI::with('tenant')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse($logbooks, 'Data Logbook Sweeping PI berhasil diambil');
    }

    public function show($id)
    {
        $logbook = LogbookSweepingPI::with(['tenant', 'details', 'notes'])->find($id);

        if (!$logbook) {
            return $this->notFoundResponse('Logbook Sweeping PI tidak ditemukan');
        }

        return $this->successResponse($logbook, 'Detail Logbook Sweeping PI berhasil diambil');
    }

    public function storeDetail(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|integer|min:1|max:31',
            'value' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $logbook = LogbookSweepingPI::find($id);
            if (!$logbook) return $this->notFoundResponse('Logbook Sweeping PI tidak ditemukan');

            $dateField = 'tanggal_' . $request->tanggal;

            $detail = LogbookSweepingPIDetail::firstOrCreate(
                ['sweepingpiID' => $id]
            );

            $detail->$dateField = $request->value;
            $detail->save();

            return $this->successResponse($detail, 'Detail Sweeping PI berhasil diupdate');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function storeNote(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|integer|min:1|max:31',
            'note' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $logbook = LogbookSweepingPI::find($id);
            if (!$logbook) return $this->notFoundResponse('Logbook Sweeping PI tidak ditemukan');

            $note = NoteSweepingPI::create([
                'sweepingpiID' => $id,
                'tanggal' => $request->tanggal,
                'note' => $request->note,
            ]);

            return $this->successResponse($note, 'Catatan Sweeping PI berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}
