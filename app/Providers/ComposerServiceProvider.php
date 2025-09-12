<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\Report;
use App\Models\Logbook;
use App\Models\LogbookRotasi;
use App\Models\ChecklistKendaraan;
use App\Models\ChecklistPenyisiran;
use App\Models\ManualBook;
use App\Models\FormPencatatanPI;

class ComposerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.sidebar', function ($view) {

            // Inisialisasi semua hitungan dengan 0
            $approvalCounts = [
                'reports' => 0,
                'logbookPosJaga' => 0,
                'logbookRotasi' => 0,
                'kendaraan' => 0,
                'penyisiran' => 0,
                'manualBook' => 0,
                'pencatatanPI' => 0,
                'totalLogbook' => 0, // Untuk menu utama Logbook
                'totalChecklist' => 0, // Untuk menu utama Checklist
                'total' => 0, // Untuk total keseluruhan jika diperlukan
            ];

            if (Auth::check() && Auth::user()->role->name === Role::SUPERVISOR) {

                $supervisorId = Auth::id();

                // 💡 Hitung setiap form secara individual
                $approvalCounts['reports'] = Report::where('statusID', 1)
                                                    ->where('approvedByID', $supervisorId)
                                                    ->count();

                $approvalCounts['logbookPosJaga'] = Logbook::where('status', 'submitted')
                                                            ->where('approvedID', $supervisorId)
                                                            ->count();

                $approvalCounts['logbookRotasi'] = LogbookRotasi::where('status', 'submitted')
                                                                ->where('approved_by', $supervisorId)
                                                                ->count();

                $approvalCounts['kendaraan'] = ChecklistKendaraan::where('status', 'submitted')
                                                                ->where('approved_id', $supervisorId)
                                                                ->count();

                $approvalCounts['penyisiran'] = ChecklistPenyisiran::where('status', 'submitted')
                                                                ->where('approved_id', $supervisorId)
                                                                ->count();

                $approvalCounts['manualBook'] = ManualBook::where('status', 'submitted')
                                                            ->where('approved_by', $supervisorId)
                                                            ->count();

                $approvalCounts['pencatatanPI'] = FormPencatatanPI::where('status', 'submitted')
                                                                ->where('approved_id', $supervisorId)
                                                                ->count();

                // 💡 Hitung total untuk setiap menu utama
                $approvalCounts['totalLogbook'] = $approvalCounts['logbookPosJaga'] + $approvalCounts['logbookRotasi'];
                $approvalCounts['totalChecklist'] = $approvalCounts['kendaraan'] + $approvalCounts['penyisiran'] + $approvalCounts['manualBook'] + $approvalCounts['pencatatanPI'];

                // 💡 Hitung total keseluruhan
                $approvalCounts['total'] = $approvalCounts['reports'] + $approvalCounts['totalLogbook'] + $approvalCounts['totalChecklist'];
            }

            // 💡 Kirim satu array '$approvalCounts' ke view
            $view->with('approvalCounts', $approvalCounts);
        });
    }
}
