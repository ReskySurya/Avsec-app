<?php

use App\Http\Controllers\Checklist\ChecklistSenpiController;
use App\Http\Controllers\Checklist\ManualBookController;
use App\Http\Controllers\Checklist\ChecklistPenyisiranController;
use App\Http\Controllers\DailyTest\HhmdController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportPdfController;
use App\Http\Controllers\Logbook\LogbookChiefController;
use App\Http\Controllers\Checklist\FormPencatatanPIController;
use App\Http\Controllers\LogBook\LogbookSweppingPIController;
use App\Http\Controllers\MasterDataController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Checklist\ChecklistKendaraanController;
use App\Http\Controllers\DailyTest\WtmdController;
use App\Http\Controllers\DailyTest\XrayController;
use App\Http\Controllers\LogBook\LogbookPosJagaController;
use App\Http\Controllers\LogBook\LogbookRotasiController;
use App\Http\Controllers\PMIKController;

Route::get('/', function () {
    return view('auth.login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Change Password Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.update');
});

// Admin Routes
Route::middleware(['auth', 'role:superadmin', 'password.changed'])->group(function () {
    Route::get('/dashboard/superadmin', [DashboardController::class, 'indexSuperadmin'])->name('dashboard.superadmin');

    Route::get('/export', [ExportPdfController::class, 'index'])->name('export.index');
    Route::get('/export/dailytest', [ExportPdfController::class, 'exportPdfDailyTest'])->name('export.dailytest');
    Route::get('/export/daily-test/review/{report}', [ExportPdfController::class, 'reviewDailyTest'])->name('export.dailytest.review');

    Route::get('/export/logbook', [ExportPdfController::class, 'exportPdfLogbook'])->name('export.logbook');
    Route::post('/export/logbook/filter', [ExportPdfController::class, 'filterLogbook'])->name('export.logbook.filter');
    Route::get('/export/logbook/review/{id}', [ExportPdfController::class, 'reviewLogbook'])->name('export.logbook.review');

    Route::get('/export/checklist', [ExportPdfController::class, 'exportPdfChecklist'])->name('export.checklist');
    Route::post('/export/checklist/filter', [ExportPdfController::class, 'filterChecklist'])->name('export.checklist.filter');
    Route::get('/export/checklist/review/{report}', [ExportPdfController::class, 'reviewChecklist'])->name('export.checklist.review');

    // Rute untuk Folder
    Route::get('/folders/create', [PMIKController::class, 'create'])->name('folders.create');
    Route::post('/folders', [PMIKController::class, 'store'])->name('folders.store');
    Route::get('/folders/{folder}/edit', [PMIKController::class, 'edit'])->name('folders.edit');
    Route::put('/folders/{folder}', [PMIKController::class, 'update'])->name('folders.update');
    Route::delete('/folders/{folder}', [PMIKController::class, 'destroy'])->name('folders.destroy');

    // Rute untuk Dokumen
    Route::get('/folders/{folder}/documents/create', [PMIKController::class, 'createDocument'])->name('documents.create');
    Route::post('/documents', [PMIKController::class, 'storeDocument'])->name('documents.store');
    Route::get('/documents/{document}', [PMIKController::class, 'showDocument'])->name('documents.show');
    Route::get('/documents/{document}/download', [PMIKController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [PMIKController::class, 'destroyDocument'])->name('documents.destroy');
});

// Supervisor Routes
Route::middleware(['auth', 'role:supervisor', 'password.changed'])->group(function () {
    Route::get('/dashboard/supervisor', [DashboardController::class, 'indexSupervisor'])->name('dashboard.supervisor');

    Route::post('/logbook/signature/approve/{logbookID}', [LogbookPosJagaController::class, 'signatureApprove'])->name('supervisor.logbook.signature');

    // Review Logbook Rotasi
    Route::post('/logbook-rotasi/approved/{id}', [LogbookRotasiController::class, 'approvedForm'])->name('supervisor.logbook-rotasi.approved');

    // Logbook Chief
    Route::post('/logbook/chief/store', [LogbookChiefController::class, 'store'])->name('logbook.chief.store');
    Route::put('/logbook/chief/{logbook}', [LogbookChiefController::class, 'update'])->name('logbook.chief.update');
    Route::delete('/logbook/chief/delete/{logbook}', [LogbookChiefController::class, 'destory'])->name('logbook.chief.delete');

    Route::post('/logbook/chief/add-kemajuan', [LogbookChiefController::class, 'storeKemajuan'])->name('logbook.chief.addKemajuan');
    Route::put('/logbook/chief/update-kemajuan/{id}', [LogbookChiefController::class, 'updateKemajuan'])->name('logbook.chief.updateKemajuan');
    Route::delete('/logbook/chief/delete-kemajuan/{id}', [LogbookChiefController::class, 'destroyKemajuan'])->name('logbook.chief.deleteKemajuan');

    Route::post('/logbook/chief/add-personil', [LogbookChiefController::class, 'storePersonil'])->name('logbook.chief.addPersonil');
    Route::put('/logbook/chief/update-personil/{id}', [LogbookChiefController::class, 'updatePersonil'])->name('logbook.chief.updatePersonil');
    Route::delete('/logbook/chief/delete-personil/{id}', [LogbookChiefController::class, 'destroyPersonil'])->name('logbook.chief.deletePersonil');

    Route::post('/logbook/chief/add-facility', [LogbookChiefController::class, 'storeFacility'])->name('logbook.chief.addFacility');
    Route::put('/logbook/chief/update-facility/{id}', [LogbookChiefController::class, 'updateFacility'])->name('logbook.chief.updateFacility');
    Route::delete('/logbook/chief/delete-facility/{id}', [LogbookChiefController::class, 'destroyFacility'])->name('logbook.chief.deleteFacility');

    Route::post('/logbook/chief/add-uraian', [LogbookChiefController::class, 'storeUraian'])->name('logbook.chief.addUraian');
    Route::put('/logbook/chief/update-uraian/{id}', [LogbookChiefController::class, 'updateUraian'])->name('logbook.chief.updateUraian');
    Route::delete('/logbook/chief/delete-uraian/{id}', [LogbookChiefController::class, 'destroyUraian'])->name('logbook.chief.deleteUraian');

    Route::post('/logbook/chief/signature/send/{logbookID}', [LogbookChiefController::class, 'signatureSend'])->name('logbook.chief.signature.send');
    Route::post('/logbook/chief/review/{logbookID}/receive', [LogbookChiefController::class, 'signatureReceive'])->name('logbook.chief.signature.receive');

    // Review Checklist Kendaraan Motor Patroli
    Route::post('/checklist-kendaraan-patroli/approve/{checklist}', [ChecklistKendaraanController::class, 'storeSignatureApproved'])->name('supervisor.checklist-kendaraan.signature');

    // Review Checklist Penyisiran Ruang Tunggu
    Route::post('/checklist-penyisiran-ruang-tunggu/approve/{checklist}', [ChecklistPenyisiranController::class, 'storeSignatureApproved'])->name('supervisor.checklist-penyisiran.signature');

    // Review Form Pencatatan PI
    Route::post('/form-pencatatan-pi/approve/{checklist}', [FormPencatatanPIController::class, 'storeSignatureApproved'])->name('supervisor.form-pencatatan-pi.signature');

    // Review Manual Book
    Route::patch('/checklist-manual-book/approve/{manualBook}', [ManualBookController::class, 'approveSignature'])->name('supervisor.checklist-manualbook.signature');
});

// Officer Routes
Route::middleware(['auth', 'role:officer', 'password.changed'])->group(function () {

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
Route::middleware(['auth', 'password.changed'])->group(function () {
    //Tampilan Superadmin dan Supervisor Logbook Chief
    Route::get('/logbook/chief', [LogbookChiefController::class, 'index'])->name('logbook.chief.index');
    Route::get('/logbook/chief/detail/{id}', [LogbookChiefController::class, 'detail'])->name('logbook.chief.detail');
    Route::get('/logbook/chief/review-laporan-leader/{logbookID}', [LogbookChiefController::class, 'reviewLogbook'])->name('logbook.chief.review.laporan.leader');

    //Tampilan Superadmin dan Supervisor
    Route::get('/supervisor/dailytest-form',  [DashboardController::class, 'showDataDailyTest'])->name('supervisor.dailytest-form');
    Route::get('/logbook/logbook-form',  [DashboardController::class, 'showDataLogbook'])->name('supervisor.logbook-form');

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

    Route::get('/pmik', [PMIKController::class, 'index'])->name('pmik.index');
    Route::get('/folders/{folder}', [PMIKController::class, 'show'])->name('folders.show');
    Route::get('/documents/{document}/view', [PMIKController::class, 'view'])->name('documents.view');
    Route::get('/documents/{document}/viewer', [PMIKController::class, 'showViewer'])->name('documents.viewer');
});

// Master Data Routes
Route::middleware(['auth', 'role:superadmin', 'password.changed'])->group(function () {
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
    Route::get('/master-data/users-management', [MasterDataController::class, 'indexUserManagement'])->name('users-management.index');
    Route::post('/users-management/tambah', [MasterDataController::class, 'storeUserManagement'])->name('users-management.store');
    Route::get('/users-management/update/{id}', [MasterDataController::class, 'getUserManagement'])->name('users-management.get');
    Route::put('/users-management/update/{id}', [MasterDataController::class, 'updateUserManagement'])->name('users-management.update');
    Route::delete('/users-management/hapus/{id}', [MasterDataController::class, 'destroyUserManagement'])->name('users-management.destroy');
    Route::post('/users-management/reset/{id}', [MasterDataController::class, 'resetPassword'])->name('users-management.reset');

    // Route untuk Tenant Management
    Route::get('/master-data/tenant-management', [MasterDataController::class, 'indexTenantManagement'])->name('tenant-management.index');
    Route::post('/tenant-management/store', [MasterDataController::class, 'storeTenant'])->name('tenant.store');
    Route::post('/tenant-management/update/{tenantID}', [MasterDataController::class, 'updateTenant'])->name('tenant.update');
    Route::delete('/tenant-management/destroy/{id}', [MasterDataController::class, 'destroyTenant'])->name('tenant.destroy');

    // Route untuk Tenant Items
    Route::get('/tenant-management/items/{tenantID}', [MasterDataController::class, 'indexProhibitedItem'])->name('tenant.items');
    Route::post('/tenant-management/items/store', [MasterDataController::class, 'storeProhibitedItem'])->name('prohibited-items.store');
    Route::post('/tenant-management/items/update/{id}', [MasterDataController::class, 'updateProhibitedItem'])->name('prohibited-items.update');
    Route::delete('/tenant-management/items/destroy/{id}', [MasterDataController::class, 'destroyProhibitedItem'])->name('prohibited-items.destroy');

    //Route untuk Checklist Items
    //Kendaraan
    Route::get('/master-data/checklist-items', [MasterDataController::class, 'indexChecklistItems'])->name('checklist-items.index');
    Route::post('/checklist-items/store', [MasterDataController::class, 'storeChecklistItems'])->name('checklist-items.store');
    Route::post('/checklist-items/update/{id}', [MasterDataController::class, 'updateChecklistItems'])->name('checklist-items.update');
    Route::delete('/checklist-items/destroy/{id}', [MasterDataController::class, 'destroyChecklistItems'])->name('checklist-items.destroy');
});

// Logbook Routes
Route::middleware(['auth', 'password.changed'])->group(function () {
    // Logbook Supervisor & Superadmin
    Route::get('/logbook-rotasi/list', [DashboardController::class, 'showDataLogbookRotasi'])->name('supervisor.logbook-rotasi.list');
    Route::get('/logbook-rotasi/detail/{logbook}', [LogbookRotasiController::class, 'show'])->name('supervisor.logbook-rotasi.detail');

    Route::get('/logbook/sweepingpi', [LogbookSweppingPIController::class, 'indexSweepingPI'])->name('sweepingPI.index');

    Route::get('/sweepingpi/manage/{tenantID}', [LogbookSweppingPIController::class, 'indexSweepingPIManage'])->name('sweepingPI.manage.index');
    Route::post('/sweepingpi/manage/store', [LogbookSweppingPIController::class, 'storeSweepingPI'])->name('sweepingPI.manage.store');
    Route::delete('/sweepingpi/manage/destroy/{sweepingpiID}', [LogbookSweppingPIController::class, 'deleteSweepingPI'])->name('sweepingPI.manage.destroy');

    Route::get('/sweepingpi/manage/detail/{tenantID}/{month}', [LogbookSweppingPIController::class, 'indexSweepingPIDetail'])->name('sweepingPI.detail.index');

    // Logbook Rotasi
    Route::get('/logbook-rotasi', [LogbookRotasiController::class, 'index'])->name('logbookRotasi.index');
    Route::post('/logbook-rotasi/store', [LogbookRotasiController::class, 'store'])->name('logbookRotasi.store');
    Route::put('/logbook-rotasi/{id}/submit', [LogbookRotasiController::class, 'submitForm'])->name('logbookRotasi.submit');

    // Logbook Sweeping PI
    Route::get('/logbook-sweppingpi', [LogbookSweppingPIController::class, 'index'])->name('logbookSweppingPI.index');
    Route::get('/logbook/sweepingpi/detail/{tenantID}', [LogbookSweppingPIController::class, 'indexLogbookSweepingPIDetail'])->name('logbookSweppingPI.detail.index');
    Route::post('/logbook/sweepingpi/store', [LogbookSweppingPIController::class, 'saveProgressSweepingPI'])->name('logbookSweppingPI.store');

    // Logbook Pos Jaga
    Route::get('/logbook/posjaga', [LogbookPosJagaController::class, 'index'])->name('logbook.index');
    Route::post('/logbook/posjaga', [LogbookPosJagaController::class, 'store'])->name('logbook.store');
    Route::get('/logbook/posjaga/list/{id}', [LogbookPosJagaController::class, 'showListPosJaga'])->name('logbook.posjaga.list');
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
    Route::get('/officer/received-logbook/{location}/{logbookID}', [LogbookPosJagaController::class, 'showReceivedLogbook'])->name('officer.received.show');
    Route::get('/supervisor/posjaga/detail/{logbookID}', [LogbookPosJagaController::class, 'supervisorReviewLogbook'])->name('supervisor.logbook.detail');
    Route::post('/supervisor/posjaga/detail/reject/{logbookID}', [LogbookPosJagaController::class, 'rejectLogbook'])->name('supervisor.logbook.reject');
    Route::get('/officer/posjaga/detail/{logbookID}', [LogbookPosJagaController::class, 'officerReviewLogbook'])->name('officer.logbook.detail');
});

// Checklist Routes
Route::middleware(['auth', 'password.changed'])->group(function () {
    //Tampilan Superadmin dan Supervisor
    Route::get('/checklist-kendaraan-patroli/list', [DashboardController::class, 'showDataChecklistKendaraan'])->name('supervisor.checklist-kendaraan.list');
    Route::get('/checklist-kendaraan-patroli/detail/{checklist}', [ChecklistKendaraanController::class, 'show'])->name('supervisor.checklist-kendaraan.detail');
    Route::get('/checklist-penyisiran-ruang-tunggu/list', [DashboardController::class, 'showDataChecklistPenyisiran'])->name('supervisor.checklist-penyisiran.list');
    Route::get('/checklist-penyisiran-ruang-tunggu/detail/{checklist}', [ChecklistPenyisiranController::class, 'showDetailPenyisiran'])->name('supervisor.checklist-penyisiran.detail');
    Route::get('/checklist/form-pencatatan-pi/list', [DashboardController::class, 'showDataFormPencatatanPI'])->name('supervisor.form-pencatatan-pi.list');
    Route::get('/form-pencatatan-pi/detail/{checklist}', [FormPencatatanPIController::class, 'showDetailPencatatanPI'])->name('supervisor.form-pencatatan-pi.detail');
    Route::get('/checklist-manual-book/list', [DashboardController::class, 'showDataManualBook'])->name('supervisor.checklist-manualbook.list');
    Route::get('/checklist-manual-book/detail/{manualBook}', [ManualBookController::class, 'show'])->name('supervisor.checklist-manualbook.detail');

    // Checklist Kendaraan Patroli
    Route::get('/checklist-harian-kendaraan', [ChecklistKendaraanController::class, 'indexChecklistKendaraan'])->name('checklist.kendaraan.index');
    Route::post('/checklist-kendaraan/store', [ChecklistKendaraanController::class, 'store'])->name('checklist.kendaraan.store');
    Route::get('/officer/received-checklist-kendaraan/{type}/{id}', [ChecklistKendaraanController::class, 'showReceivedChecklist'])->name('officer.receivedChecklistKendaraan.show');
    Route::post('/checklist/received-signature/kendaraan/{checklist}', [ChecklistKendaraanController::class, 'storeSignatureReceived'])->name('checklist.receivedSignature');

    // Checklist Penyisiran Ruang Tunggu
    Route::get('/checklist-harian-penyisiran-ruang-tunggu', [ChecklistPenyisiranController::class, 'indexChecklistPenyisiran'])->name('checklist.penyisiran.index');
    Route::post('/checklist-penyisiran-ruang-tunggu/store', [ChecklistPenyisiranController::class, 'storeChecklistPenyisiran'])->name('checklist.penyisiran.store');
    Route::get('/officer/received-checklist-penyisiran/{id}', [ChecklistPenyisiranController::class, 'showReceivedChecklistPenyisiran'])->name('officer.receivedChecklistPenyisiran.show');
    Route::post('/checklist/received-signature/penyisiran/{checklist}', [ChecklistPenyisiranController::class, 'storeReceivedSignaturePenyisiran'])->name('checklist.receivedSignature.penyisiran');

    // Manual Book Routes
    Route::get('/checklist-manual-book', [ManualBookController::class, 'index'])->name('checklist.manualbook.index');
    Route::post('/checklist-manual-book/store', [ManualBookController::class, 'store'])->name('checklist.manualbook.store');
    Route::patch('/checklist-manual-book/add-details/{id}', [ManualBookController::class, 'addDetails'])->name('checklist.manualbook.addDetails');
    Route::patch('/checklist-manual-book/finish/{id}', [ManualBookController::class, 'finish'])->name('checklist.manualbook.finish');


    // Checklist Senpi Routes
    Route::get('/checklist-senpi', [ChecklistSenpiController::class, 'indexChecklistSenpi'])->name('checklist.senpi.index');
    Route::post('/checklist-senpi/store', [ChecklistSenpiController::class, 'storeChecklistSenpi'])->name('checklist.senpi.store');
    Route::post('/checklist-senpi/update/{id}', [ChecklistSenpiController::class, 'updateChecklistSenpi'])->name('checklist.senpi.update');
    Route::delete('/checklist-senpi/destroy/{id}', [ChecklistSenpiController::class, 'destroyChecklistSenpi'])->name('checklist.senpi.destroy');

    // Checklist Pencatatan PI Routes
    Route::get('/checklist/form-pencatatan-pi', [FormPencatatanPIController::class, 'indexChecklistPencatatanPI'])->name('checklist.pencatatanpi.index');
    Route::get('/checklist/form-pencatatan-pi/create', [FormPencatatanPIController::class, 'create'])->name('checklist.pencatatanpi.create');
    Route::post('/form-pencatatan-pi/store', [FormPencatatanPIController::class, 'storeChecklistPencatatanPI'])->name('checklist.pencatatanpi.store');
    Route::get('/form-pencatatan-pi/{id}/edit', [FormPencatatanPIController::class, 'editChecklistPencatatanPI'])->name('checklist.pencatatanpi.edit');
    Route::put('/form-pencatatan-pi/{id}', [FormPencatatanPIController::class, 'updateChecklistPencatatanPI'])->name('checklist.pencatatanpi.update');
    Route::delete('/form-pencatatan-pi/{id}', [FormPencatatanPIController::class, 'destroyChecklistPencatatanPI'])->name('checklist.pencatatanpi.destroy');
});
