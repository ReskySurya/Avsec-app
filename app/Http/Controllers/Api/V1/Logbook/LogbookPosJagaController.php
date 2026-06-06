<?php

namespace App\Http\Controllers\Api\V1\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\Location;
use App\Models\LogbookRotasi;
use App\Models\ManualBook;
use App\Models\LogbookDetail;
use App\Models\LogbookStaff;
use App\Models\LogbookFacility;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LogbookPosJagaController extends Controller
{
    use ApiResponse;

    protected $allowedLocations = [
        'Pos Kedatangan',
        'Pos Barat',
        'Pos Timur',
        'HBSCP',
        'PSCP',
        'CCTV',
        'Patroli',
        'Walking Patrol',
        'Pos Keberangkatan'
    ];

    /**
     * Get Logbook Pos Jaga index (drafts for current user)
     */
    public function index()
    {
        $currentUserId = Auth::id();

        $staffLogbookIds = LogbookStaff::where('staffID', $currentUserId)
            ->where('description', 'hadir')
            ->pluck('logbookID');

        $logbooks = Logbook::with('locationArea')
            ->where('status', 'draft')
            ->where(function ($query) use ($currentUserId, $staffLogbookIds) {
                $query->where('senderID', $currentUserId)
                    ->orWhereIn('logbookID', $staffLogbookIds);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $locations = Location::whereIn('name', $this->allowedLocations)
            ->orderBy('name', 'asc')
            ->get();

        return $this->successResponse([
            'logbooks' => $logbooks,
            'locations' => $locations
        ], 'Data logbook pos jaga berhasil diambil');
    }

    /**
     * Store new Logbook Pos Jaga
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'location_area_id' => 'required|exists:locations,id',
            'grup' => 'required|string|max:255',
            'shift' => 'required|in:pagi,malam',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $existingLogbook = Logbook::where('date', $request->date)
            ->where('location_area_id', $request->location_area_id)
            ->where('shift', $request->shift)
            ->first();

        if ($existingLogbook) {
            return $this->errorResponse('Logbook untuk lokasi, tanggal, dan shift ini sudah ada.', 409);
        }

        DB::beginTransaction();
        try {
            $logbookPosJaga = Logbook::create([
                'date' => $request->date,
                'location_area_id' => $request->location_area_id,
                'grup' => $request->grup,
                'shift' => $request->shift,
                'senderID' => Auth::id(),
                'status' => 'draft'
            ]);

            $location = Location::find($request->location_area_id);
            $message = 'Logbook Pos Jaga berhasil dibuat.';

            if ($location && in_array($location->name, ['PSCP', 'HBSCP'])) {
                $logbookType = strtolower($location->name);
                $this->_createLogbookRotasiDraft($request, $logbookType);
                $this->_createManualBookDraft($request, $logbookType);
                $message .= ' Draft untuk Logbook Rotasi dan Manual Book juga telah dibuat.';
            }

            DB::commit();
            return $this->successResponse($logbookPosJaga, $message, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    private function _createLogbookRotasiDraft(Request $request, string $logbookType)
    {
        $exists = LogbookRotasi::where('date', $request->date)
            ->where('type', $logbookType)
            ->exists();

        if (!$exists) {
            LogbookRotasi::create([
                'id'         => $this->generateLogbookRotasiId($logbookType),
                'date'       => $request->date,
                'type'       => $logbookType,
                'status'     => 'draft',
                'created_by' => Auth::id(),
            ]);
        }
    }

    private function _createManualBookDraft(Request $request, string $logbookType)
    {
        $exists = ManualBook::where('date', $request->date)
            ->where('shift', $request->shift)
            ->where('type', $logbookType)
            ->exists();

        if (!$exists) {
            ManualBook::create([
                'id'         => $this->generateManualBookId($logbookType, $request->date),
                'created_by' => Auth::id(),
                'date'       => $request->date,
                'shift'      => $request->shift,
                'type'       => $logbookType,
                'status'     => 'draft',
            ]);
        }
    }

    private function generateLogbookRotasiId($type)
    {
        $prefix = ($type == 'pscp') ? 'LRP' : 'LRH';
        $date = now()->format('ymd');

        $lastLogbook = LogbookRotasi::where('id', 'LIKE', $prefix . '-' . $date . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $newNumber = 1;
        if ($lastLogbook) {
            $parts = explode('-', $lastLogbook->id);
            $lastNumber = (int)end($parts);
            $newNumber = $lastNumber + 1;
        }

        return $prefix . '-' . $date . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
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

    /**
     * Get Logbook details including staff, facilities, etc.
     */
    public function show($id)
    {
        $logbook = Logbook::with('locationArea')->find($id);

        if (!$logbook) {
            return $this->notFoundResponse('Logbook tidak ditemukan');
        }

        $uraianKegiatan = LogbookDetail::where('logbookID', $id)->get();
        $personil = LogbookStaff::with('user')->where('logbookID', $id)->get();
        $facility = LogbookFacility::where('logbookID', $id)->get();

        return $this->successResponse([
            'logbook' => $logbook,
            'kegiatan' => $uraianKegiatan,
            'personil' => $personil,
            'fasilitas' => $facility
        ], 'Detail logbook berhasil diambil');
    }

    /**
     * Add activity detail
     */
    public function storeDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logbookID'   => 'required|exists:logbooks,logbookID',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i',
            'summary'     => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $detail = LogbookDetail::create($request->only([
                'logbookID', 'start_time', 'end_time', 'summary', 'description'
            ]));
            return $this->successResponse($detail, 'Uraian kegiatan berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menambahkan uraian kegiatan', 500);
        }
    }

    /**
     * Add staff to logbook
     */
    public function storeStaff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logbookID'   => 'required|exists:logbooks,logbookID',
            'staffID'     => 'required|exists:users,id',
            'classification' => 'nullable|string|max:255',
            'description'  => 'required|in:hadir,izin,sakit,cuti',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $user = User::find($request->staffID);
            $staff = LogbookStaff::create([
                'logbookID' => $request->logbookID,
                'staffID' => $request->staffID,
                'classification' => $user->lisensi ?? $request->classification,
                'description' => $request->description,
            ]);
            return $this->successResponse($staff, 'Personil berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menambahkan personil', 500);
        }
    }

    /**
     * Add facility to logbook
     */
    public function storeFacility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logbookID'   => 'required|exists:logbooks,logbookID',
            'facility'    => 'required|string|max:255',
            'quantity'    => 'required|integer|min:1',
            'description' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $facility = LogbookFacility::create($request->only([
                'logbookID', 'facility', 'quantity', 'description'
            ]));
            return $this->successResponse($facility, 'Fasilitas berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menambahkan fasilitas', 500);
        }
    }

    /**
     * Send logbook (Sign and submit)
     */
    public function signatureSend(Request $request, $logbookID)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
            'receivedID' => 'required|exists:users,id',
            'approvedID' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $logbook = Logbook::find($logbookID);
            if (!$logbook) {
                return $this->notFoundResponse('Logbook tidak ditemukan');
            }

            $signature = $request->signature;
            if (preg_match('/^data:image\/(\w+);base64,/', $signature)) {
                $signature = substr($signature, strpos($signature, ',') + 1);
            }

            $logbook->fill([
                'senderSignature' => $signature,
                'receivedID' => $request->receivedID,
                'approvedID' => $request->approvedID,
                'status' => 'submitted'
            ]);
            $logbook->save();

            return $this->successResponse($logbook, 'Logbook berhasil diserahkan');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Receive logbook (Sign as Receiver)
     */
    public function signatureReceive(Request $request, $logbookID)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $logbook = Logbook::find($logbookID);
            if (!$logbook) {
                return $this->notFoundResponse('Logbook tidak ditemukan');
            }

            $signature = $request->signature;
            if (preg_match('/^data:image\/(\w+);base64,/', $signature)) {
                $signature = substr($signature, strpos($signature, ',') + 1);
            }

            $logbook->receivedSignature = $signature;
            $logbook->save();

            return $this->successResponse($logbook, 'Logbook berhasil diterima oleh officer pengganti');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Approve logbook (Sign as Supervisor)
     */
    public function signatureApprove(Request $request, $logbookID)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $logbook = Logbook::find($logbookID);
            if (!$logbook) {
                return $this->notFoundResponse('Logbook tidak ditemukan');
            }

            $signature = $request->signature;
            if (preg_match('/^data:image\/(\w+);base64,/', $signature)) {
                $signature = substr($signature, strpos($signature, ',') + 1);
            }

            $logbook->approvedSignature = $signature;
            $logbook->status = 'approved';
            $logbook->save();

            return $this->successResponse($logbook, 'Logbook berhasil disetujui');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}
