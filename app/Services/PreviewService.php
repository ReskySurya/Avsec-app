<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Logbook;
use App\Models\LogbookChief;
use App\Models\LogbookRotasi;
use App\Models\LogbookSweepingPI;
use App\Models\ChecklistKendaraan;
use App\Models\ChecklistPenyisiran;
use App\Models\ChecklistSenpi;
use App\Models\FormPencatatanPI;
use App\Models\ManualBook;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PreviewService
{
    /**
     * Generate a preview for a given category, ID, and form type.
     */
    public function getPreview(string $category, $id, ?string $formType)
    {
        try {
            switch ($category) {
                case 'daily-test':
                    $report = Report::findOrFail($id);
                    return $this->previewDailyTest($report);
                case 'logbook':
                    return $this->previewLogbook($id, $formType);
                case 'checklist':
                    return $this->previewChecklist($id, $formType);
                default:
                    abort(404, 'Kategori tidak ditemukan');
            }
        } catch (\Exception $e) {
            Log::error("Error generating preview: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menampilkan pratinjau: ' . $e->getMessage());
        }
    }

    // ============== DAILY TEST PREVIEW ==============

    private function previewDailyTest(Report $report)
    {
        $formType = $report->equipmentLocation->equipment->name;
        $type = strtolower($formType);
        $viewMapping = [
            'hhmd'       => 'superadmin.export.pdf.dailytest.hhmdTemplate',
            'wtmd'       => 'superadmin.export.pdf.dailytest.wtmdTemplate',
            'xraycabin'  => 'superadmin.export.pdf.dailytest.xraycabinTemplate',
            'xraybagasi' => 'superadmin.export.pdf.dailytest.xraybagasiTemplate',
        ];

        if (!isset($viewMapping[$type])) {
            return response('Template not found for this form type.', 404);
        }
        $viewName = $viewMapping[$type];

        $report->load(['reportDetails', 'submittedBy', 'approvedBy', 'equipmentLocation.location']);

        $form = $this->prepareFormData($report);
        $forms = collect([$form]);

        return view($viewName, ['forms' => $forms]);
    }

    private function prepareFormData(Report $report)
    {
        $form = new \stdClass();
        $detail = $report->reportDetails->first();
        if ($detail instanceof \Illuminate\Support\Collection) {
            $detail = $detail->first();
        }
        if ($detail instanceof \Illuminate\Database\Eloquent\Model) {
            foreach ($detail->toArray() as $key => $value) {
                $form->$key = $value;
            }
        }
        $form->operatorName      = $report->submittedBy->name ?? 'N/A';
        $form->testDateTime      = $report->testDate;
        $form->location          = $report->equipmentLocation->location->name ?? 'N/A';
        $form->deviceInfo        = $report->equipmentLocation->merk_type ?? 'N/A';
        $form->certificateInfo   = $report->equipmentLocation->certificateInfo ?? 'N/A';
        $form->officerName       = $report->submittedBy->name ?? 'N/A';
        $form->supervisor        = $report->approvedBy;
        $form->officer_signature = $report->submitterSignature;
        $form->supervisor_signature = $report->approverSignature;
        $form->result            = strtolower($report->result ?? '');
        $form->notes             = $report->note;
        $form->terpenuhi = ($form->result === 'pass');
        $form->tidakTerpenuhi = ($form->result === 'fail');
        if (!isset($form->testCondition1)) $form->testCondition1 = true;
        if (!isset($form->testCondition2)) $form->testCondition2 = true;
        if (!isset($form->test1)) $form->test1 = ($form->result === 'pass');
        return $form;
    }

    // ============== LOGBOOK PREVIEW ==============

    private function previewLogbook($id, $formType)
    {
        if (!$formType) {
            $formType = $this->detectLogbookType($id);
        }

        switch ($formType) {
            case 'pos_jaga':
                return $this->previewPosJagaLogbook($id);
            case 'sweeping_pi':
                return $this->previewSweepingPILogbook($id);
            case 'rotasi':
                return $this->previewRotasiLogbook($id);
            case 'chief':
                return $this->previewChiefLogbook($id);
            default:
                abort(404, 'Jenis logbook tidak ditemukan');
        }
    }

    private function detectLogbookType($logbookID)
    {
        if (str_starts_with($logbookID, 'SPI-')) return 'sweeping_pi';
        if (str_starts_with($logbookID, 'LRH-') || str_starts_with($logbookID, 'LRP-')) return 'rotasi';
        if (str_starts_with($logbookID, 'CHF-')) return 'chief';
        return 'pos_jaga';
    }

    private function previewPosJagaLogbook($logbookID)
    {
        $logbook = Logbook::with(['details', 'senderBy', 'receiverBy', 'approverBy', 'locationArea', 'facility', 'personil.user'])->findOrFail($logbookID);
        $form = $this->prepareLogbookFormData($logbook);
        return view('superadmin.export.pdf.logbook.logbookPosJagaTemplate', ['forms' => collect([$form])]);
    }

    private function previewSweepingPILogbook($sweepingpiID)
    {
        $sweeping = LogbookSweepingPI::with(['tenant', 'sweepingPIDetails', 'notesSweepingPI'])->findOrFail($sweepingpiID);
        $form = $this->prepareSweepingPIFormData($sweeping);
        return view('superadmin.export.pdf.logbook.logbookSweepingTemplate', ['forms' => collect([$form])]);
    }

    private function previewRotasiLogbook($id)
    {
        $rotasi = LogbookRotasi::with(['creator', 'approver', 'submitter', 'details.officerAssignment.officer'])->findOrFail($id);
        $form = $this->prepareRotasiFormData($rotasi);
        $form->officerLog = $this->prepareOfficerLogData($rotasi);
        return view('superadmin.export.pdf.logbook.logbookRotasiTemplate', ['forms' => collect([$form])]);
    }

    private function previewChiefLogbook($logbookID)
    {
        $logbook = LogbookChief::with(['details', 'createdBy', 'approvedBy', 'facility', 'personil.user', 'kemajuan'])->findOrFail($logbookID);
        $form = $this->prepareLogbookChief($logbook);
        return view('superadmin.export.pdf.logbook.logbookChiefTemplate', ['forms' => collect([$form])]);
    }

    private function prepareSweepingPIFormData(LogbookSweepingPI $sweeping)
    {
        $form = new \stdClass();
        $form->sweepingpiID = $sweeping->sweepingpiID;
        $form->created_at = $sweeping->created_at;
        $form->bulan = $sweeping->bulan;
        $form->tahun = $sweeping->tahun;
        $form->notes = $sweeping->notes;
        $form->tenant = $sweeping->tenant;
        $form->sweepingDetails = $sweeping->sweepingPIDetails;
        $form->notesSweeping = $sweeping->notesSweepingPI;
        $form->completionStats = $sweeping->getCompletionStats();
        return $form;
    }

    private function prepareRotasiFormData(LogbookRotasi $rotasi)
    {
        $form = new \stdClass();
        $form->rotasiID = $rotasi->id;
        $form->date = $rotasi->date;
        $form->type = $rotasi->type;
        $form->status = $rotasi->status;
        $form->notes = $rotasi->notes;
        $form->creatorName = $rotasi->creator->name ?? 'N/A';
        $form->approverName = $rotasi->approver->name ?? 'N/A';
        $form->submittedSignature = $rotasi->submittedSignature;
        $form->approvedSignature = $rotasi->approvedSignature;
        return $form;
    }

    private function prepareOfficerLogData(LogbookRotasi $rotasi)
    {
        $officerLog = [];
        $officerIds = $rotasi->details->pluck('pemeriksaan_dokumen')
            ->merge($rotasi->details->pluck('pengatur_flow'))
            ->merge($rotasi->details->pluck('operator_xray'))
            ->merge($rotasi->details->pluck('hhmd_petugas'))
            ->merge($rotasi->details->pluck('manual_kabin_petugas'))
            ->filter()->unique();

        $officers = User::whereIn('id', $officerIds)->get()->keyBy('id');

        foreach ($rotasi->details as $detail) {
            $roles = [
                'pemeriksaan_dokumen' => $detail->pemeriksaan_dokumen,
                'pengatur_flow' => $detail->pengatur_flow,
                'operator_xray' => $detail->operator_xray,
                'hhmd_petugas' => $detail->hhmd_petugas,
                'manual_kabin_petugas' => $detail->manual_kabin_petugas,
            ];

            foreach ($roles as $roleName => $officerId) {
                if (!$officerId) continue;
                if (!isset($officerLog[$officerId])) {
                    $officer = $officers->get($officerId);
                    $officerLog[$officerId] = [
                        'officer_name' => $officer ? $officer->name : 'Officer Tidak Dikenal (' . $officerId . ')',
                        'roles' => [],
                        'keterangan' => []
                    ];
                }
                $roleData = [
                    'start' => optional($detail->officerAssignment)->start_time ?? '00:00',
                    'end' => optional($detail->officerAssignment)->end_time ?? '23:59',
                    'hhmd_random' => $detail->hhmd_random ?? '-',
                    'hhmd_unpredictable' => $detail->hhmd_unpredictable ?? '-',
                    'cek_random_barang' => $detail->cek_random_barang ?? '-',
                    'barang_unpredictable' => $detail->barang_unpredictable ?? '-',
                ];
                $officerLog[$officerId]['roles'][$roleName][] = $roleData;
                if ($detail->keterangan) {
                    $officerLog[$officerId]['keterangan'][] = $detail->keterangan;
                }
            }
        }
        return $officerLog;
    }

    private function prepareLogbookFormData(Logbook $logbook)
    {
        $form = new \stdClass();
        $form->logbookID = $logbook->logbookID;
        $form->date = $logbook->date;
        $form->location = $logbook->locationArea->name ?? 'N/A';
        $form->grup = $logbook->grup;
        $form->shift = $logbook->shift;
        $form->status = $logbook->status;
        $form->senderName = $logbook->senderBy->name ?? 'N/A';
        $form->receiverName = $logbook->receiverBy->name ?? 'N/A';
        $form->approverName = $logbook->approverBy->name ?? 'N/A';
        $form->senderSignature = $logbook->senderSignature;
        $form->receivedSignature = $logbook->receivedSignature;
        $form->approvedSignature = $logbook->approvedSignature;
        $form->rejectedReason = $logbook->rejected_reason;
        $form->logbookDetails = $logbook->details;
        $form->facility = $logbook->facility;
        $form->personil = $logbook->personil;
        return $form;
    }

    private function prepareLogbookChief(LogbookChief $logbook)
    {
        $form = new \stdClass();
        $form->logbookID = $logbook->logbookID;
        $form->date = $logbook->date;
        $form->grup = $logbook->grup;
        $form->shift = $logbook->shift;
        $form->status = 'Approved';
        $form->senderName = $logbook->createdBy->name ?? 'N/A';
        $form->receiverName = $logbook->approvedBy->name ?? 'N/A';
        $form->senderSignature = $logbook->senderSignature;
        $form->approvedSignature = $logbook->approvedSignature;
        $form->logbookDetails = $logbook->details;
        $form->facility = $logbook->facility;
        $form->personil = $logbook->personil;
        $form->kemajuan = $logbook->kemajuan;
        $form->lokasi = $logbook->grup;
        $form->chief = $logbook->createdBy->name ?? 'N/A';
        $form->supervisor = $logbook->approvedBy->name ?? 'N/A';
        $form->notes = $logbook->notes ?? 'Tidak ada catatan khusus.';
        $form->chiefSignature = $logbook->senderSignature;
        $form->supervisorSignature = $logbook->approvedSignature;
        return $form;
    }

    // ============== CHECKLIST PREVIEW ==============

    private function previewChecklist($id, $formType)
    {
        if (!$formType) {
            abort(400, 'Jenis checklist tidak disediakan.');
        }

        switch ($formType) {
            case 'kendaraan':
                return $this->previewChecklistKendaraan($id);
            case 'penyisiran':
                return $this->previewChecklistPenyisiran($id);
            case 'senpi':
                return $this->previewChecklistSenpi($id);
            case 'pencatatan_pi':
                return $this->previewFormPencatatanPI($id);
            case 'manual_book':
                return $this->previewManualBook($id);
            default:
                abort(404, 'Jenis checklist tidak valid.');
        }
    }

    private function previewChecklistKendaraan($id)
    {
        $checklist = ChecklistKendaraan::with(['details.item', 'sender', 'receiver', 'approver'])->findOrFail($id);
        return view('superadmin.export.pdf.checklist.checklistKendaraanTemplate', ['forms' => collect([$checklist]), 'formType' => 'kendaraan']);
    }

    private function previewChecklistPenyisiran($id)
    {
        $checklist = ChecklistPenyisiran::with(['details.item', 'sender', 'receiver', 'approver'])->findOrFail($id);
        return view('superadmin.export.pdf.checklist.checklistPenyisiranTemplate', ['forms' => collect([$checklist]), 'formType' => 'penyisiran']);
    }

    private function previewChecklistSenpi($id)
    {
        $checklist = ChecklistSenpi::findOrFail($id);
        return view('superadmin.export.pdf.checklist.checklistSenpiTemplate', ['forms' => collect([$checklist]), 'formType' => 'senpi']);
    }

    private function previewFormPencatatanPI($id)
    {
        $checklist = FormPencatatanPI::with(['sender', 'approver', 'details'])->findOrFail($id);
        return view('superadmin.export.pdf.checklist.checklistPencatatanPITemplate', ['forms' => collect([$checklist]), 'formType' => 'pencatatan_pi']);
    }

    private function previewManualBook($id)
    {
        $checklist = ManualBook::with(['details', 'creator', 'approver'])->findOrFail($id);
        return view('superadmin.export.pdf.checklist.checklistManualBookTemplate', ['forms' => collect([$checklist]), 'formType' => 'manual_book']);
    }
}
