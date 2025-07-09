<?php

use App\Http\Controllers\DailyTest\HhmdController;
use App\Http\Controllers\MasterDataController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyTest\WtmdController;
use App\Http\Controllers\DailyTest\XrayController;

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
});

// Supervisor Routes
Route::middleware(['auth', 'role:supervisor'])->group(function () {
    Route::get('/dashboard/supervisor', function () {
        return view('supervisor.dashboardSupervisor');
    })->name('dashboard.supervisor');

    Route::get('/dashboard/supervisor/dailytest-form',  [HhmdController::class, 'showData'])->name('supervisor.dailytest-form');
});

// Officer Routes
Route::middleware(['auth', 'role:officer'])->group(function () {
    Route::get('/dashboard/officer', function () {
        $rejectedReports = \App\Models\Report::rejected()
            ->where('submittedByID', auth()->id())
            ->with([
                'status:id,name',
                'equipmentLocation.location:id,name',
                'equipmentLocation.equipment:id,name',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('officer.dashboardOfficer', compact('rejectedReports'));
    })->name('dashboard.officer');

    Route::get('officer/edit-rejected-report/hhmd/{id}', [HhmdController::class, 'editRejectedReport'])->name('officer.hhmd.editRejectedReport');
    Route::put('officer/edit-rejected-report/hhmd/{id}', [HhmdController::class, 'update'])->name('officer.hhmd.update');
    Route::get('officer/edit-rejected-report/wtmd/{id}', [WtmdController::class, 'editRejectedReport'])->name('officer.wtmd.editRejectedReport');
    Route::get('officer/edit-rejected-report/xraycabin/{id}', [XrayController::class, 'editRejectedReportCabin'])->name('officer.xraycabin.editRejectedReport');
    Route::get('officer/edit-rejected-report/xraybagasi/{id}', [XrayController::class, 'editRejectedReportBagasi'])->name('officer.xraybagasi.editRejectedReport');
});

// Daily Test Routes
Route::middleware(['auth'])->group(function () {
    // Daily Test HHMD Routes
    Route::get('/daily-test/hhmd', [HhmdController::class, 'hhmdLayout'])->name('daily-test.hhmd');
    // Route::post('/daily-test/hhmd/check-location', [HhmdController::class, 'checkLocation'])->name('daily-test.hhmd.check-location');
    Route::post('/daily-test/hhmd/store', [HhmdController::class, 'store'])->name('daily-test.hhmd.store');
    Route::get('/daily-test/hhmd/get/{id}', [HhmdController::class, 'get'])->name('hhmd.get');
    Route::post('/daily-test/hhmd/update/{id}', [HhmdController::class, 'update'])->name('hhmd.update');
    Route::get('/daily-test/hhmd/review/{id}', [HhmdController::class, 'reviewForm'])->name('hhmd.reviewForm');
    Route::patch('/daily-test/hhmd/update-status/{id}', [HhmdController::class, 'updateStatus'])->name('hhmd.updateStatus');
    Route::post('/daily-test/hhmd/save-supervisor-signature/{id}', [HhmdController::class, 'saveSupervisorSignature'])->name('hhmd.saveSupervisorSignature');

    // Daily Test WTMD Routes
    Route::get('/daily-test/wtmd', [WtmdController::class, 'wtmdLayout'])->name('daily-test.wtmd');
    // Route::post('/daily-test/wtmd/check-location', [WtmdController::class, 'checkLocation'])->name('daily-test.wtmd.check-location');
    Route::post('/daily-test/wtmd/store', [WtmdController::class, 'store'])->name('daily-test.wtmd.store');
    Route::get('/daily-test/wtmd/review/{id}', [WtmdController::class, 'reviewForm'])->name('wtmd.reviewForm');
    Route::patch('/daily-test/wtmd/update-status/{id}', [WtmdController::class, 'updateStatus'])->name('wtmd.updateStatus');
    Route::post('/daily-test/wtmd/save-supervisor-signature/{id}', [WtmdController::class, 'saveSupervisorSignature'])->name('wtmd.saveSupervisorSignature');


    // Daily Test XRAY Routes
    Route::get('/daily-test/xraycabin', [XrayController::class, 'xrayCabinLayout'])->name('daily-test.xraycabin');
    // Route::post('/daily-test/xray/check-location', [XrayController::class, 'checkLocation'])->name('daily-test.xrayCabin.check-location');
    Route::post('/daily-test/xraycabin/store', [XrayController::class, 'storeXrayCabin'])->name('daily-test.xrayCabin.store');

    Route::get('/daily-test/xraybagasi', [XrayController::class, 'xrayBagasiLayout'])->name('daily-test.xraybagasi');
    Route::post('/daily-test/xraybagasi/store', [XrayController::class, 'storeXrayBagasi'])->name('daily-test.xrayBagasi.store');

    // Common XRAY review routes
    Route::get('/daily-test/xray/review/{id}', [XrayController::class, 'reviewForm'])->name('xray.reviewForm');
    Route::patch('/daily-test/xray/update-status/{id}', [XrayController::class, 'updateStatus'])->name('xray.updateStatus');
    Route::post('/daily-test/xray/save-supervisor-signature/{id}', [XrayController::class, 'saveSupervisorSignature'])->name('xray.saveSupervisorSignature');

    // Reports Routes
    Route::get('/reports/{id}', [HhmdController::class, 'show'])->name('reports.show');
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
    Route::put('/users-management/update/{id}', [MasterDataController::class,'updateUserManagement'])->name('users-management.update');
    Route::delete('/users-management/hapus/{id}', [MasterDataController::class, 'destroyUserManagement'])->name('users-management.destroy');

});
