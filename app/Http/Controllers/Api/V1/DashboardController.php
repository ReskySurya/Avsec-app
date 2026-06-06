<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ChecklistKendaraan;
use App\Models\ChecklistPenyisiran;
use App\Models\FormPencatatanPI;
use App\Models\EquipmentLocation;
use App\Models\Location;
use App\Models\Report;
use App\Models\Logbook;
use App\Models\LogbookChief;
use App\Models\LogbookRotasi;
use App\Models\LogbookSweepingPI;
use App\Models\LogbookSweepingPIDetail;
use App\Models\ManualBook;
use App\Models\ReportStatus;
use App\Models\Tenant;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use ApiResponse;

    /**
     * Get dashboard data for Officer role
     */
    public function getOfficerDashboard()
    {
        $currentUserId = Auth::id();

        // 1. Daily Test Submissions (simplified)
        $equipmentTypes = ['hhmd', 'wtmd', 'xraycabin', 'xraybagasi'];
        $locations = EquipmentLocation::with(['equipment', 'location'])
            ->whereHas('equipment', function ($q) use ($equipmentTypes) {
                $q->whereIn('name', $equipmentTypes);
            })->get();

        $submittedLocationIds = Report::whereIn('equipmentLocationID', $locations->pluck('id'))
            ->whereDate('created_at', today())
            ->pluck('equipmentLocationID')
            ->unique();

        $dailyTests = [
            'total' => $locations->count(),
            'submitted' => $submittedLocationIds->count(),
            'pending' => $locations->count() - $submittedLocationIds->count()
        ];

        // 2. Draft/Rejected tasks count
        $rejectedReportsCount = Report::rejected()->where('submittedByID', $currentUserId)->count();
        
        $draftLogbooksCount = Logbook::where('status', 'draft')
            ->where(function ($query) use ($currentUserId) {
                $query->where('senderID', $currentUserId)
                      ->orWhereHas('personil', function ($q) use ($currentUserId) {
                          $q->where('staffID', $currentUserId);
                      });
            })->count();

        $actionRequired = $rejectedReportsCount + $draftLogbooksCount;

        // 3. To review logbooks count
        $toReviewCount = Logbook::where('receivedID', $currentUserId)
            ->whereNull('receivedSignature')
            ->count() +
            ChecklistKendaraan::where('received_id', $currentUserId)
            ->whereNull('receivedSignature')
            ->count() +
            ChecklistPenyisiran::where('received_id', $currentUserId)
            ->whereNull('receivedSignature')
            ->count();

        return $this->successResponse([
            'summary' => [
                'action_required' => $actionRequired,
                'to_review' => $toReviewCount,
                'daily_tests' => $dailyTests
            ],
            'rejected_reports' => $rejectedReportsCount,
            'draft_logbooks' => $draftLogbooksCount
        ], 'Dashboard Officer berhasil diambil');
    }

    /**
     * Get dashboard data for Supervisor & SuperAdmin role
     */
    public function getSupervisorDashboard()
    {
        $authId = Auth::id();
        $isSuperAdmin = Auth::user()->isSuperAdmin();

        // Pending items summary
        $pendingDailyTests = Report::whereHas('status', function ($query) {
                $query->where('name', 'pending');
            });
            
        $pendingLogbookPosJaga = Logbook::where('status', 'submitted');
        $pendingLogbookChief = LogbookChief::where('status', 'submitted');
        $pendingRotasi = LogbookRotasi::where('status', 'submitted');
        $pendingManualBook = ManualBook::where('status', 'submitted');
        
        $pendingChecklistKendaraan = ChecklistKendaraan::where('status', 'submitted');
        $pendingChecklistPenyisiran = ChecklistPenyisiran::where('status', 'submitted');
        $pendingChecklistPI = FormPencatatanPI::where('status', 'submitted');

        // Apply approver filter if not SuperAdmin
        if (!$isSuperAdmin) {
            $pendingDailyTests->where('approvedByID', $authId);
            $pendingLogbookPosJaga->where('approvedID', $authId);
            $pendingLogbookChief->where('approved_by', $authId);
            $pendingRotasi->where('approved_by', $authId);
            $pendingManualBook->where('approved_by', $authId);
            $pendingChecklistKendaraan->where('approved_id', $authId);
            $pendingChecklistPenyisiran->where('approved_id', $authId);
            $pendingChecklistPI->where('approved_id', $authId);
        }

        $summary = [
            'daily_tests' => $pendingDailyTests->count(),
            'logbook_pos_jaga' => $pendingLogbookPosJaga->count(),
            'logbook_chief' => $pendingLogbookChief->count(),
            'logbook_rotasi' => $pendingRotasi->count(),
            'manual_book' => $pendingManualBook->count(),
            'checklist_kendaraan' => $pendingChecklistKendaraan->count(),
            'checklist_penyisiran' => $pendingChecklistPenyisiran->count(),
            'form_pencatatan_pi' => $pendingChecklistPI->count()
        ];

        $totalPending = array_sum($summary);

        return $this->successResponse([
            'summary' => $summary,
            'total_pending' => $totalPending
        ], 'Dashboard Supervisor berhasil diambil');
    }

    /**
     * Main dashboard entry point handling roles
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin() || $user->isSupervisor()) {
            return $this->getSupervisorDashboard();
        }

        return $this->getOfficerDashboard();
    }
}
