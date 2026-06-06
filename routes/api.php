<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
// Add other controllers here as they are created

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    
    // Protected Auth Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('api.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('api.password.change');
    });
});

// Protected Application Routes
Route::middleware(['auth:sanctum', 'api.password.changed'])->group(function () {
    
    // -------------------------------------------------------------
    // SuperAdmin & Supervisor Routes
    // -------------------------------------------------------------
    Route::middleware(['api.role:superadmin,supervisor'])->group(function () {
        // Will be added later
    });

    // -------------------------------------------------------------
    // Officer & Supervisor Routes
    // -------------------------------------------------------------
    Route::middleware(['api.role:officer,supervisor'])->group(function () {
        // Will be added later
    });

    // -------------------------------------------------------------
    // Shared Routes (All Authenticated Roles)
    // -------------------------------------------------------------
    Route::get('/dashboard', [\App\Http\Controllers\Api\V1\DashboardController::class, 'index'])->name('api.dashboard');

    // Daily Test
    Route::prefix('daily-test')->group(function () {
        // HHMD
        Route::get('/hhmd', [\App\Http\Controllers\Api\V1\DailyTest\HhmdController::class, 'index']);
        Route::post('/hhmd/check-submission', [\App\Http\Controllers\Api\V1\DailyTest\HhmdController::class, 'checkSubmission']);
        Route::post('/hhmd/store', [\App\Http\Controllers\Api\V1\DailyTest\HhmdController::class, 'store']);
        Route::get('/hhmd/{id}', [\App\Http\Controllers\Api\V1\DailyTest\HhmdController::class, 'show']);
        Route::put('/hhmd/{id}', [\App\Http\Controllers\Api\V1\DailyTest\HhmdController::class, 'update']);
        Route::patch('/hhmd/{id}/status', [\App\Http\Controllers\Api\V1\DailyTest\HhmdController::class, 'updateStatus'])->middleware('api.role:superadmin,supervisor');
        Route::post('/hhmd/{id}/signature', [\App\Http\Controllers\Api\V1\DailyTest\HhmdController::class, 'saveSignature'])->middleware('api.role:superadmin,supervisor');

        // WTMD
        Route::get('/wtmd', [\App\Http\Controllers\Api\V1\DailyTest\WtmdController::class, 'index']);
        Route::post('/wtmd/store', [\App\Http\Controllers\Api\V1\DailyTest\WtmdController::class, 'store']);

        // XRAY
        Route::get('/xray-cabin', [\App\Http\Controllers\Api\V1\DailyTest\XrayController::class, 'indexCabin']);
        Route::get('/xray-bagasi', [\App\Http\Controllers\Api\V1\DailyTest\XrayController::class, 'indexBagasi']);
    });

    // Logbook
    Route::prefix('logbook')->group(function () {
        // Pos Jaga
        Route::get('/pos-jaga', [\App\Http\Controllers\Api\V1\Logbook\LogbookPosJagaController::class, 'index']);
        Route::post('/pos-jaga', [\App\Http\Controllers\Api\V1\Logbook\LogbookPosJagaController::class, 'store']);
        Route::get('/pos-jaga/{id}', [\App\Http\Controllers\Api\V1\Logbook\LogbookPosJagaController::class, 'show']);
        Route::post('/pos-jaga/detail', [\App\Http\Controllers\Api\V1\Logbook\LogbookPosJagaController::class, 'storeDetail']);
        Route::post('/pos-jaga/staff', [\App\Http\Controllers\Api\V1\Logbook\LogbookPosJagaController::class, 'storeStaff']);
        Route::post('/pos-jaga/facility', [\App\Http\Controllers\Api\V1\Logbook\LogbookPosJagaController::class, 'storeFacility']);
        Route::post('/pos-jaga/{id}/send', [\App\Http\Controllers\Api\V1\Logbook\LogbookPosJagaController::class, 'signatureSend']);
        Route::post('/pos-jaga/{id}/receive', [\App\Http\Controllers\Api\V1\Logbook\LogbookPosJagaController::class, 'signatureReceive']);
        Route::post('/pos-jaga/{id}/approve', [\App\Http\Controllers\Api\V1\Logbook\LogbookPosJagaController::class, 'signatureApprove'])->middleware('api.role:superadmin,supervisor');

        // Chief
        Route::get('/chief', [\App\Http\Controllers\Api\V1\Logbook\LogbookChiefController::class, 'index']);
        Route::post('/chief', [\App\Http\Controllers\Api\V1\Logbook\LogbookChiefController::class, 'store']);
        Route::get('/chief/{id}', [\App\Http\Controllers\Api\V1\Logbook\LogbookChiefController::class, 'show']);
        Route::post('/chief/kemajuan', [\App\Http\Controllers\Api\V1\Logbook\LogbookChiefController::class, 'storeKemajuan']);
        Route::post('/chief/{id}/send', [\App\Http\Controllers\Api\V1\Logbook\LogbookChiefController::class, 'signatureSend']);
        Route::post('/chief/{id}/approve', [\App\Http\Controllers\Api\V1\Logbook\LogbookChiefController::class, 'signatureApprove'])->middleware('api.role:superadmin,supervisor');

        // Rotasi
        Route::get('/rotasi', [\App\Http\Controllers\Api\V1\Logbook\LogbookRotasiController::class, 'index']);
        Route::get('/rotasi/{id}', [\App\Http\Controllers\Api\V1\Logbook\LogbookRotasiController::class, 'show']);
        Route::post('/rotasi/detail', [\App\Http\Controllers\Api\V1\Logbook\LogbookRotasiController::class, 'storeDetail']);
        Route::post('/rotasi/assignment', [\App\Http\Controllers\Api\V1\Logbook\LogbookRotasiController::class, 'storeAssignment']);
        Route::post('/rotasi/{id}/send', [\App\Http\Controllers\Api\V1\Logbook\LogbookRotasiController::class, 'signatureSend']);
        Route::post('/rotasi/{id}/approve', [\App\Http\Controllers\Api\V1\Logbook\LogbookRotasiController::class, 'signatureApprove'])->middleware('api.role:superadmin,supervisor');

        // Sweeping PI
        Route::get('/sweeping-pi', [\App\Http\Controllers\Api\V1\Logbook\LogbookSweepingPIController::class, 'index']);
        Route::get('/sweeping-pi/{id}', [\App\Http\Controllers\Api\V1\Logbook\LogbookSweepingPIController::class, 'show']);
        Route::post('/sweeping-pi/{id}/detail', [\App\Http\Controllers\Api\V1\Logbook\LogbookSweepingPIController::class, 'storeDetail']);
        Route::post('/sweeping-pi/{id}/note', [\App\Http\Controllers\Api\V1\Logbook\LogbookSweepingPIController::class, 'storeNote']);
    });

    // Checklist
    Route::prefix('checklist')->group(function () {
        // Kendaraan
        Route::get('/kendaraan', [\App\Http\Controllers\Api\V1\Checklist\ChecklistKendaraanController::class, 'index']);
        Route::post('/kendaraan', [\App\Http\Controllers\Api\V1\Checklist\ChecklistKendaraanController::class, 'store']);
        Route::get('/kendaraan/{id}', [\App\Http\Controllers\Api\V1\Checklist\ChecklistKendaraanController::class, 'show']);
        Route::post('/kendaraan/detail', [\App\Http\Controllers\Api\V1\Checklist\ChecklistKendaraanController::class, 'storeDetail']);
        Route::post('/kendaraan/{id}/send', [\App\Http\Controllers\Api\V1\Checklist\ChecklistKendaraanController::class, 'signatureSend']);
        Route::post('/kendaraan/{id}/receive', [\App\Http\Controllers\Api\V1\Checklist\ChecklistKendaraanController::class, 'signatureReceive']);
        Route::post('/kendaraan/{id}/approve', [\App\Http\Controllers\Api\V1\Checklist\ChecklistKendaraanController::class, 'signatureApprove'])->middleware('api.role:superadmin,supervisor');

        // Penyisiran
        Route::get('/penyisiran', [\App\Http\Controllers\Api\V1\Checklist\ChecklistPenyisiranController::class, 'index']);
        Route::post('/penyisiran', [\App\Http\Controllers\Api\V1\Checklist\ChecklistPenyisiranController::class, 'store']);
        Route::get('/penyisiran/{id}', [\App\Http\Controllers\Api\V1\Checklist\ChecklistPenyisiranController::class, 'show']);
        Route::post('/penyisiran/detail', [\App\Http\Controllers\Api\V1\Checklist\ChecklistPenyisiranController::class, 'storeDetail']);
        Route::post('/penyisiran/{id}/send', [\App\Http\Controllers\Api\V1\Checklist\ChecklistPenyisiranController::class, 'signatureSend']);
        Route::post('/penyisiran/{id}/receive', [\App\Http\Controllers\Api\V1\Checklist\ChecklistPenyisiranController::class, 'signatureReceive']);
        Route::post('/penyisiran/{id}/approve', [\App\Http\Controllers\Api\V1\Checklist\ChecklistPenyisiranController::class, 'signatureApprove'])->middleware('api.role:superadmin,supervisor');

        // Senpi
        Route::get('/senpi', [\App\Http\Controllers\Api\V1\Checklist\ChecklistSenpiController::class, 'index']);
        Route::post('/senpi', [\App\Http\Controllers\Api\V1\Checklist\ChecklistSenpiController::class, 'store']);
        Route::get('/senpi/{id}', [\App\Http\Controllers\Api\V1\Checklist\ChecklistSenpiController::class, 'show']);
        Route::post('/senpi/{id}/send', [\App\Http\Controllers\Api\V1\Checklist\ChecklistSenpiController::class, 'signatureSend']);
        Route::post('/senpi/{id}/approve', [\App\Http\Controllers\Api\V1\Checklist\ChecklistSenpiController::class, 'signatureApprove'])->middleware('api.role:superadmin,supervisor');

        // Manual Book
        Route::get('/manual-book', [\App\Http\Controllers\Api\V1\Checklist\ManualBookController::class, 'index']);
        Route::post('/manual-book', [\App\Http\Controllers\Api\V1\Checklist\ManualBookController::class, 'store']);
        Route::get('/manual-book/{id}', [\App\Http\Controllers\Api\V1\Checklist\ManualBookController::class, 'show']);
        Route::post('/manual-book/detail', [\App\Http\Controllers\Api\V1\Checklist\ManualBookController::class, 'storeDetail']);
        Route::post('/manual-book/{id}/send', [\App\Http\Controllers\Api\V1\Checklist\ManualBookController::class, 'signatureSend']);
        Route::post('/manual-book/{id}/approve', [\App\Http\Controllers\Api\V1\Checklist\ManualBookController::class, 'signatureApprove'])->middleware('api.role:superadmin,supervisor');

        // Form Pencatatan PI
        Route::get('/pencatatan-pi', [\App\Http\Controllers\Api\V1\Checklist\FormPencatatanPIController::class, 'index']);
        Route::post('/pencatatan-pi', [\App\Http\Controllers\Api\V1\Checklist\FormPencatatanPIController::class, 'store']);
        Route::get('/pencatatan-pi/{id}', [\App\Http\Controllers\Api\V1\Checklist\FormPencatatanPIController::class, 'show']);
        Route::post('/pencatatan-pi/detail', [\App\Http\Controllers\Api\V1\Checklist\FormPencatatanPIController::class, 'storeDetail']);
        Route::post('/pencatatan-pi/{id}/send', [\App\Http\Controllers\Api\V1\Checklist\FormPencatatanPIController::class, 'signatureSend']);
        Route::post('/pencatatan-pi/{id}/approve', [\App\Http\Controllers\Api\V1\Checklist\FormPencatatanPIController::class, 'signatureApprove'])->middleware('api.role:superadmin,supervisor');
    });

    // History
    Route::get('/history', [\App\Http\Controllers\Api\V1\HistoryController::class, 'index']);

    // Master Data
    Route::prefix('master-data')->group(function () {
        Route::get('/equipments', [\App\Http\Controllers\Api\V1\MasterDataController::class, 'getEquipments']);
        Route::get('/locations', [\App\Http\Controllers\Api\V1\MasterDataController::class, 'getLocations']);
        Route::get('/prohibited-items', [\App\Http\Controllers\Api\V1\MasterDataController::class, 'getProhibitedItems']);
        Route::get('/tenants', [\App\Http\Controllers\Api\V1\MasterDataController::class, 'getTenants']);
        Route::get('/officers', [\App\Http\Controllers\Api\V1\MasterDataController::class, 'getOfficers']);
        Route::get('/supervisors', [\App\Http\Controllers\Api\V1\MasterDataController::class, 'getSupervisors']);
    });
});
