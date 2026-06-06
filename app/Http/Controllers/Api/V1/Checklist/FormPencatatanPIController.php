<?php

namespace App\Http\Controllers\Api\V1\Checklist;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\FormPencatatanPI;
use App\Models\FormPencatatanPIDetail;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FormPencatatanPIController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $forms = FormPencatatanPI::where('created_by', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        return $this->successResponse($forms, 'Data Form Pencatatan PI berhasil diambil');
    }

    public function show($id)
    {
        $form = FormPencatatanPI::with(['details', 'creator', 'approver'])->find($id);

        if (!$form) {
            return $this->notFoundResponse('Form Pencatatan PI tidak ditemukan');
        }

        return $this->successResponse($form, 'Detail Form Pencatatan PI berhasil diambil');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'grup' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $form = FormPencatatanPI::create([
                'id' => $this->generateFormId(),
                'date' => $request->date,
                'grup' => $request->grup,
                'created_by' => Auth::id(),
                'status' => 'draft',
            ]);

            return $this->successResponse($form, 'Form Pencatatan PI berhasil dibuat', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function storeDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pencatatan_pi_id' => 'required|exists:form_pencatatan_pis,id',
            'waktu' => 'required|date_format:H:i',
            'sektor' => 'required|string',
            'item_id' => 'required|exists:prohibited_items,id',
            'tindakan' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $detail = FormPencatatanPIDetail::create($request->all());
            return $this->successResponse($detail, 'Detail Form Pencatatan PI berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function signatureSend(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
            'approved_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $form = FormPencatatanPI::find($id);
            if (!$form) return $this->notFoundResponse('Form Pencatatan PI tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $form->creator_signature = $signature;
            $form->approved_id = $request->approved_id;
            $form->status = 'submitted';
            $form->save();

            return $this->successResponse($form, 'Form Pencatatan PI berhasil diserahkan');
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
            $form = FormPencatatanPI::find($id);
            if (!$form) return $this->notFoundResponse('Form Pencatatan PI tidak ditemukan');

            $signature = str_replace('data:image/png;base64,', '', $request->signature);
            
            $form->approvedSignature = $signature;
            $form->status = 'approved';
            $form->save();

            return $this->successResponse($form, 'Form Pencatatan PI berhasil disetujui');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    private function generateFormId()
    {
        $prefix = 'FPI';
        $date = Carbon::now()->format('ymd');
        
        $lastRecord = FormPencatatanPI::where('id', 'like', $prefix . '-' . $date . '-%')
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
