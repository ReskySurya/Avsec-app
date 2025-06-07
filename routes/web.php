<?php

use App\Http\Controllers\EquipmentLocationController;
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
});

// Officer Routes
Route::middleware(['auth', 'role:officer'])->group(function () {
    Route::get('/dashboard/officer', function () {
        return view('officer.dashboardOfficer');
    })->name('dashboard.officer');
});

// Daily Test Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/daily-test/hhmd', [DailyTestController::class, 'hhmdLayout'])->name('daily-test.hhmd');
    Route::get('/daily-test/wtmd', [DailyTestController::class, 'wtmdLayout'])->name('daily-test.wtmd');
    Route::get('/daily-test/xraycabin', [DailyTestController::class,'xrayCabinLayout'])->name('daily-test.xraycabin');
    Route::get('/daily-test/xraybagasi', [DailyTestController::class,'xrayBagasiLayout'])->name('daily-test.xraybagasi');
});

// Master Data Routes
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    // Equipment Locations
    Route::get('/master-data/equipment-locations', [MasterDataController::class, 'index'])->name('equipment-locations.index');
    // Route::get('/master-data/equipment-locations/create', [MasterDataController::class, 'create'])->name('equipment-locations.create');
    // Route::post('/master-data/equipment-locations', [MasterDataController::class, 'store'])->name('equipment-locations.store');
    // Route::get('/master-data/equipment-locations/{id}', [MasterDataController::class, 'showEquipment'])->name('equipment-locations.show-equipment');
    // Route::get('/master-data/locations/{id}', [MasterDataController::class, 'showLocation'])->name('equipment-locations.show-location');
    // Route::get('/master-data/equipment-locations/{id}/edit', [MasterDataController::class, 'edit'])->name('equipment-locations.edit');
    // Route::put('/master-data/equipment-locations/{id}', [MasterDataController::class, 'update'])->name('equipment-locations.update');
    // Route::delete('/master-data/equipment-locations/{id}', [MasterDataController::class, 'destroy'])->name('equipment-locations.destroy');
    // Route::get('/master-data/search', [MasterDataController::class, 'search'])->name('equipment-locations.search');

    // User Management
    Route::get('/users-management', [MasterDataController::class, 'indexUserManagement'])->name('users-management.index');
    Route::post('/users-management/tambah', [MasterDataController::class, 'storeUserManagement'])->name('users-management.store');
    Route::get('/users-management/update/{id}', [MasterDataController::class, 'getUserManagement'])->name('users-management.get');
    Route::put('/users-management/update/{id}', [MasterDataController::class,'updateUserManagement'])->name('users-management.update');
});
