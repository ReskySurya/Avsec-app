<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\ChecklistKendaraan;
use App\Models\ChecklistPenyisiran;
use App\Models\LogbookRotasi;
use App\Models\FormPencatatanPI;
use App\Models\Report;
use App\Models\ManualBook;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $currentUserId = Auth::id();
        $isSuperAdmin = Auth::user()->isSuperAdmin();

        $historyData = [
            'daily_tests' => $this->getDailyTestHistory($currentUserId, $isSuperAdmin),
            'logbook_pos_jaga' => $this->getLogbookPosJagaHistory($currentUserId, $isSuperAdmin),
            'logbook_rotasi' => $this->getLogbookRotasiHistory($currentUserId, $isSuperAdmin),
            'checklist_kendaraan' => $this->getChecklistKendaraanHistory($currentUserId, $isSuperAdmin),
            'checklist_penyisiran' => $this->getChecklistPenyisiranHistory($currentUserId, $isSuperAdmin),
            'manual_book' => $this->getManualBookHistory($currentUserId, $isSuperAdmin),
            'form_pencatatan_pi' => $this->getFormPencatatanPIHistory($currentUserId, $isSuperAdmin),
        ];

        return $this->successResponse($historyData, 'Data History berhasil diambil');
    }

    private function getDailyTestHistory($userId, $isSuperAdmin)
    {
        $query = Report::with(['equipmentLocation.location', 'equipmentLocation.equipment', 'status'])
            ->whereIn('statusID', [2, 3]); // approved or rejected

        if (!$isSuperAdmin) {
            $query->where(function($q) use ($userId) {
                $q->where('submittedByID', $userId)
                  ->orWhere('approvedByID', $userId);
            });
        }

        return $query->orderBy('created_at', 'desc')->limit(20)->get();
    }

    private function getLogbookPosJagaHistory($userId, $isSuperAdmin)
    {
        $query = Logbook::with('locationArea')
            ->where('status', 'approved');

        if (!$isSuperAdmin) {
            $query->where(function($q) use ($userId) {
                $q->where('senderID', $userId)
                  ->orWhere('receivedID', $userId)
                  ->orWhere('approvedID', $userId);
            });
        }

        return $query->orderBy('created_at', 'desc')->limit(20)->get();
    }

    private function getLogbookRotasiHistory($userId, $isSuperAdmin)
    {
        $query = LogbookRotasi::where('status', 'approved');

        if (!$isSuperAdmin) {
            $query->where(function($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhere('approved_by', $userId);
            });
        }

        return $query->orderBy('created_at', 'desc')->limit(20)->get();
    }

    private function getChecklistKendaraanHistory($userId, $isSuperAdmin)
    {
        $query = ChecklistKendaraan::where('status', 'approved');

        if (!$isSuperAdmin) {
            $query->where(function($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('received_id', $userId)
                  ->orWhere('approved_id', $userId);
            });
        }

        return $query->orderBy('created_at', 'desc')->limit(20)->get();
    }

    private function getChecklistPenyisiranHistory($userId, $isSuperAdmin)
    {
        $query = ChecklistPenyisiran::where('status', 'approved');

        if (!$isSuperAdmin) {
            $query->where(function($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('received_id', $userId)
                  ->orWhere('approved_id', $userId);
            });
        }

        return $query->orderBy('created_at', 'desc')->limit(20)->get();
    }

    private function getManualBookHistory($userId, $isSuperAdmin)
    {
        $query = ManualBook::where('status', 'approved');

        if (!$isSuperAdmin) {
            $query->where(function($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhere('approved_by', $userId);
            });
        }

        return $query->orderBy('created_at', 'desc')->limit(20)->get();
    }

    private function getFormPencatatanPIHistory($userId, $isSuperAdmin)
    {
        $query = FormPencatatanPI::where('status', 'approved');

        if (!$isSuperAdmin) {
            $query->where(function($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhere('approved_id', $userId);
            });
        }

        return $query->orderBy('created_at', 'desc')->limit(20)->get();
    }
}
