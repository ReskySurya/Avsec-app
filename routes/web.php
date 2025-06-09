<?php

use App\Http\Controllers\DailyTest\HhmdController;
use App\Http\Controllers\MasterDataController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyTestController;

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
        return view('officer.dashboardOfficer');
    })->name('dashboard.officer');
});

// Daily Test Routes
Route::middleware(['auth'])->group(function () {
    // Daily Test HHMD Routes
    Route::get('/daily-test/hhmd', [HhmdController::class, 'hhmdLayout'])->name('daily-test.hhmd');
    Route::post('/daily-test/hhmd/check-location', [HhmdController::class, 'checkLocation'])->name('daily-test.hhmd.check-location');
    Route::post('/daily-test/hhmd/store', [HhmdController::class, 'store'])->name('daily-test.hhmd.store');

    // Daily Test WTMD Routes
    Route::get('/daily-test/wtmd', [DailyTestController::class, 'wtmdLayout'])->name('daily-test.wtmd');
    Route::get('/daily-test/xraycabin', [DailyTestController::class, 'xrayCabinLayout'])->name('daily-test.xraycabin');
    Route::get('/daily-test/xraybagasi', [DailyTestController::class, 'xrayBagasiLayout'])->name('daily-test.xraybagasi');
});

// Master Data Routes
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/master-data/equipment-locations', [MasterDataController::class, 'indexEquipment'])->name('equipment-locations.index-equipment');
    // Route untuk menyimpan equipment location relationship
    Route::post('/equipment/store', [MasterDataController::class, 'storeEquipment'])->name('equipment.store');
    Route::post('/location/store', [MasterDataController::class, 'storeLocation'])->name('location.store');
    Route::post('/equipment-location/store', [MasterDataController::class, 'storeEquipmentLocation'])->name('equipment-location.store');

    // Route untuk mengedit equipment
    Route::post('/equipment/update/{id}', [MasterDataController::class, 'updateEquipment'])->name('equipment.update');
    Route::post('/location/update/{id}', [MasterDataController::class, 'updateLocation'])->name('location.update');

    // Route untuk menghapus equipment location relationship
    Route::delete('/equipment/{id}/destroy', [MasterDataController::class, 'destroyEquipment'])->name('equipment.destroy');
    Route::delete('/location/{id}/destroy', [MasterDataController::class, 'destroyLocation'])->name('location.destroy');
    Route::delete('/equipment-location/{equipmentId}/{locationId}', [MasterDataController::class, 'destroyEquipmentLocation'])->name('equipment-location.destroy');

    //Route untuk UserManagement
    Route::get('/users-management', [MasterDataController::class, 'indexUserManagement'])->name('users-management.index');
    Route::post('/users-management/tambah', [MasterDataController::class, 'storeUserManagement'])->name('users-management.store');
    Route::get('/users-management/update/{id}', [MasterDataController::class, 'getUserManagement'])->name('users-management.get');
    Route::put('/users-management/update/{id}', [MasterDataController::class,'updateUserManagement'])->name('users-management.update');
    Route::delete('/users-management/hapus/{id}', [MasterDataController::class, 'destroyUserManagement'])->name('users-management.destroy');

});
