<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
