<?php

use App\Http\Controllers\DailyTest\HhmdController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\LogBook\LogbookSweppingPIController;
use App\Http\Controllers\MasterDataController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyTest\WtmdController;
use App\Http\Controllers\DailyTest\XrayController;
use App\Http\Controllers\LogBook\LogbookPosJagaController;
use App\Http\Controllers\LogBook\LogbookRotasiHBSCPController;
use App\Http\Controllers\LogBook\LogbookRotasiPSCPController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


// Admin Routes
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/dashboard/superadmin', function () {
        return view('superadmin.dashboardSuperadmin');
    })->name('dashboard.superadmin');

    Route::get('/export', [ExportController::class, 'index'])->name('export.index');
});

// Supervisor Routes
Route::middleware(['auth', 'role:supervisor'])->group(function () {
    Route::get('/dashboard/supervisor', function () {
        return view('supervisor.dashboardSupervisor');
    })->name('dashboard.supervisor');

    Route::get('/supervisor/dailytest-form',  [DashboardController::class, 'showDataDailyTest'])->name('supervisor.dailytest-form');
    Route::get('/supervisor/logbook-form',  [DashboardController::class, 'showDataLogbook'])->name('supervisor.logbook-form');
    Route::post('/logbook/signature/approve/{logbookID}', [LogbookPosJagaController::class, 'signatureApprove'])->name('supervisor.logbook.signature');
});

// Officer Routes
Route::middleware(['auth', 'role:officer'])->group(function () {

    Route::get('/dashboard/officer', [DashboardController::class, 'index'])->name('dashboard.officer');

    Route::get('officer/edit-rejected-report/hhmd/{id}', [HhmdController::class, 'editRejectedReport'])->name('officer.hhmd.editRejectedReport');
    Route::put('officer/edit-rejected-report/hhmd/{id}', [HhmdController::class, 'update'])->name('officer.hhmd.update');
    Route::get('officer/edit-rejected-report/wtmd/{id}', [WtmdController::class, 'editRejectedReport'])->name('officer.wtmd.editRejectedReport');
    Route::put('officer/edit-rejected-report/wtmd/{id}', [WtmdController::class, 'update'])->name('officer.wtmd.update');
    Route::get('officer/edit-rejected-report/xraycabin/{id}', [XrayController::class, 'editRejectedReportCabin'])->name('officer.xraycabin.editRejectedReport');
    Route::put('officer/edit-rejected-report/xraycabin/{id}', [XrayController::class, 'updateCabin'])->name('officer.xraycabin.update');
    Route::get('officer/edit-rejected-report/xraybagasi/{id}', [XrayController::class, 'editRejectedReportBagasi'])->name('officer.xraybagasi.editRejectedReport');
    Route::put('officer/edit-rejected-report/xraybagasi/{id}', [XrayController::class, 'updateBagasi'])->name('officer.xraybagasi.update');
});

// Daily Test Routes
Route::middleware(['auth'])->group(function () {
    // Daily Test HHMD Routes
    Route::get('/daily-test/hhmd', [HhmdController::class, 'hhmdLayout'])->name('daily-test.hhmd');
    Route::post('/daily-test/hhmd/check-submission', [HhmdController::class, 'checkSubmission'])->name('daily-test.hhmd.check-submission');
    Route::post('/daily-test/hhmd/store', [HhmdController::class, 'store'])->name('daily-test.hhmd.store');
    Route::get('/daily-test/hhmd/get/{id}', [HhmdController::class, 'get'])->name('hhmd.get');
    Route::get('/daily-test/hhmd/review/{id}', [HhmdController::class, 'reviewForm'])->name('hhmd.reviewForm');
    Route::patch('/daily-test/hhmd/update-status/{id}', [HhmdController::class, 'updateStatus'])->name('hhmd.updateStatus');
    Route::post('/daily-test/hhmd/save-supervisor-signature/{id}', [HhmdController::class, 'saveSupervisorSignature'])->name('hhmd.saveSupervisorSignature');

    // Daily Test WTMD Routes
    Route::get('/daily-test/wtmd', [WtmdController::class, 'wtmdLayout'])->name('daily-test.wtmd');
    Route::post('/daily-test/wtmd/store', [WtmdController::class, 'store'])->name('daily-test.wtmd.store');
    Route::get('/daily-test/wtmd/review/{id}', [WtmdController::class, 'reviewForm'])->name('wtmd.reviewForm');
    Route::patch('/daily-test/wtmd/update-status/{id}', [WtmdController::class, 'updateStatus'])->name('wtmd.updateStatus');
    Route::post('/daily-test/wtmd/save-supervisor-signature/{id}', [WtmdController::class, 'saveSupervisorSignature'])->name('wtmd.saveSupervisorSignature');


    // Daily Test XRAY Routes
    Route::get('/daily-test/xraycabin', [XrayController::class, 'xrayCabinLayout'])->name('daily-test.xraycabin');
    Route::post('/daily-test/xraycabin/store', [XrayController::class, 'storeXrayCabin'])->name('daily-test.xrayCabin.store');

    Route::get('/daily-test/xraybagasi', [XrayController::class, 'xrayBagasiLayout'])->name('daily-test.xraybagasi');
    Route::post('/daily-test/xraybagasi/store', [XrayController::class, 'storeXrayBagasi'])->name('daily-test.xrayBagasi.store');

    // Common XRAY review routes
    Route::get('/daily-test/xray/review/{id}', [XrayController::class, 'reviewForm'])->name('xray.reviewForm');
    Route::patch('/daily-test/xray/update-status/{id}', [XrayController::class, 'updateStatus'])->name('xray.updateStatus');
    Route::post('/daily-test/xray/save-supervisor-signature/{id}', [XrayController::class, 'saveSupervisorSignature'])->name('xray.saveSupervisorSignature');
});

// Master Data Routes
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/master-data/equipment-locations', [MasterDataController::class, 'indexEquipmentLocation'])->name('equipment-locations.index');
    // Route untuk menyimpan equipment location relationship
    Route::post('/equipment/store', [MasterDataController::class, 'storeEquipment'])->name('equipment.store');
    Route::post('/location/store', [MasterDataController::class, 'storeLocation'])->name('location.store');
    Route::post('/equipment-location/store', [MasterDataController::class, 'storeEquipmentLocation'])->name('equipment-location.store');

    // Route untuk mengedit equipment
    Route::post('/equipment/update/{id}', [MasterDataController::class, 'updateEquipment'])->name('equipment.update');
    Route::post('/location/update/{id}', [MasterDataController::class, 'updateLocation'])->name('location.update');
    Route::patch('/equipment-location/update/{equipment_id}/{location_id}', [MasterDataController::class, 'updateEquipmentLocation'])->name('equipment-location.update');

    // Route untuk menghapus equipment location relationship
    Route::delete('/equipment/{id}/destroy', [MasterDataController::class, 'destroyEquipment'])->name('equipment.destroy');
    Route::delete('/location/{id}/destroy', [MasterDataController::class, 'destroyLocation'])->name('location.destroy');
    Route::delete('/equipment-location/destroy/{id}', [MasterDataController::class, 'destroyEquipmentLocation'])->name('equipment-location.destroy');

    //Route untuk UserManagement
    Route::get('/users-management', [MasterDataController::class, 'indexUserManagement'])->name('users-management.index');
    Route::post('/users-management/tambah', [MasterDataController::class, 'storeUserManagement'])->name('users-management.store');
    Route::get('/users-management/update/{id}', [MasterDataController::class, 'getUserManagement'])->name('users-management.get');
    Route::put('/users-management/update/{id}', [MasterDataController::class, 'updateUserManagement'])->name('users-management.update');
    Route::delete('/users-management/hapus/{id}', [MasterDataController::class, 'destroyUserManagement'])->name('users-management.destroy');
});

// Logbook Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/logbook-rotasihbscp', [LogbookRotasiHBSCPController::class, 'index'])->name('logbookRotasiHBSCP.index');
    Route::get('/logbook-rotasipscp', [LogbookRotasiPSCPController::class, 'index'])->name('logbookRotasiPSCP.index');
    Route::get('/logbook-sweppingpi', [LogbookSweppingPIController::class, 'index'])->name('logbookSweppingPI.index');

    // Logbook Pos Jaga
    Route::get('/logbook/posjaga', [LogbookPosJagaController::class, 'index'])->name('logbook.index');
    Route::post('/logbook/posjaga', [LogbookPosJagaController::class, 'store'])->name('logbook.store');
    Route::patch('/logbook/posjaga/{logbookID}', [LogbookPosJagaController::class, 'update'])->name('logbook.update');
    Route::delete('/logbook/posjaga/{logbook}', [LogbookPosJagaController::class, 'destroy'])->name('logbook.destroy');
    Route::post('/logbook/{location}/signature/send/{logbookID}', [LogbookPosJagaController::class, 'signatureSend'])->name('logbook.signature.send');
    Route::post('/logbook/{location}/signature/receive/{logbookID}', [LogbookPosJagaController::class, 'signatureReceive'])->name('logbook.signature.receive');

    // Logbook Detail Pos Jaga
    Route::get('/logbook/posjaga/detail/{id}', [LogbookPosJagaController::class, 'detail'])->name('logbook.detail');
    Route::post('/logbook/detail/store', [LogbookPosJagaController::class, 'storeDetail'])->name('logbook.detail.store');
    Route::post('/logbook/detail/update/{id}', [LogbookPosJagaController::class, 'updateDetail'])->name('logbook.detail.update');
    Route::delete('/logbook/detail/delete/{id}', [LogbookPosJagaController::class, 'deleteDetail'])->name('logbook.detail.delete');


    Route::post('/logbook/staff/store', [LogbookPosJagaController::class, 'storeStaff'])->name('logbook.staff.store');
    Route::post('/logbook/staff/update/{id}', [LogbookPosJagaController::class, 'updateStaff'])->name('logbook.staff.update');
    Route::delete('/logbook/staff/delete/{id}', [LogbookPosJagaController::class, 'deleteStaff'])->name('logbook.staff.delete');

    Route::post('/logbook/facility/store', [LogbookPosJagaController::class, 'storeFacility'])->name('logbook.facility.store');
    Route::post('/logbook/facility/update/{id}', [LogbookPosJagaController::class, 'updateFacility'])->name('logbook.facility.update');
    Route::delete('/logbook/facility/delete/{id}', [LogbookPosJagaController::class, 'deleteFacility'])->name('logbook.facility.delete');

    // logbook Review
    Route::get('/officer/received/{location}/{logbookID}', [LogbookPosJagaController::class, 'showReceivedLogbook'])->name('officer.received.show');
    Route::get('/supervisor/posjaga/detail/{logbookID}', [LogbookPosJagaController::class, 'supervisorReviewLogbook'])->name('supervisor.logbook.detail');
    Route::post('/supervisor/posjaga/detail/reject/{logbookID}', [LogbookPosJagaController::class, 'rejectLogbook'])->name('supervisor.logbook.reject');
});
