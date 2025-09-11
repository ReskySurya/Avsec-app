@extends('layouts.app')

@section('title', 'Logbook Sweeping Prohibited Items')

@section('content')
<div class="container w-full pt-4 ps-4 pb-4 overflow-x-auto lg:mt-16">
    <!-- Header -->
    <!-- Enhanced Header with Gradient -->
    <div class="bg-gradient-to-br from-blue-500 to-teal-600 rounded-lg sm:rounded-2xl shadow-xl p-4 sm:p-6 mb-4 sm:mb-6 text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-white bg-opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 20px 20px;"></div>
        </div>

        <div class="relative z-10">
            <div class="flex items-center mb-4">
                <button onclick="history.back()" class="text-white hover:text-blue-200 mr-4 p-2 rounded-full bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-200 hover:scale-105">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </button>
                <div class="flex-1">
                    <h1 class="text-lg sm:text-2xl font-bold text-white mb-1">Checklist Prohibited Items</h1>
                    <p class="text-indigo-100 text-sm sm:text-base">Tenant: <span class="font-semibold text-white" id="tenant-name">{{ $tenant->tenant_name }}</span></p>
                    <p class="text-indigo-200 text-xs sm:text-sm">ID: {{ $logbook->sweepingpiID }}</p>
                </div>
            </div>

            <!-- Enhanced Progress Cards with Animation -->
            <div class="grid grid-cols-3 gap-2 sm:gap-4">
                <div class="bg-white bg-opacity-15 backdrop-blur-sm rounded-xl p-3 sm:p-4 text-center transform hover:scale-105 transition-all duration-200 border border-white border-opacity-20">
                    <div class="text-xl sm:text-3xl font-bold text-white mb-1" id="completion-rate">0%</div>
                    <div class="text-indigo-100 text-xs sm:text-sm font-medium">Selesai</div>
                    <div class="w-8 h-1 bg-white bg-opacity-30 rounded-full mx-auto mt-2">
                        <div class="h-full bg-white rounded-full transition-all duration-500" style="width: 0%" id="progress-bar"></div>
                    </div>
                </div>
                <div class="bg-white bg-opacity-15 backdrop-blur-sm rounded-xl p-3 sm:p-4 text-center transform hover:scale-105 transition-all duration-200 border border-white border-opacity-20">
                    <div class="text-xl sm:text-3xl font-bold text-white mb-1" id="total-checked">0</div>
                    <div class="text-indigo-100 text-xs sm:text-sm font-medium">Dicek</div>
                    <div class="flex justify-center mt-1">
                        <svg class="w-4 h-4 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <div class="bg-white bg-opacity-15 backdrop-blur-sm rounded-xl p-3 sm:p-4 text-center transform hover:scale-105 transition-all duration-200 border border-white border-opacity-20">
                    <div class="text-xl sm:text-3xl font-bold text-white mb-1" id="total-pending">0</div>
                    <div class="text-indigo-100 text-xs sm:text-sm font-medium">Pending</div>
                    <div class="flex justify-center mt-1">
                        <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Header dengan Month/Year -->
        <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white p-4 sm:p-6 relative">
            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
            <h3 class="text-lg sm:text-2xl font-bold text-center relative z-10" id="current-month-year"></h3>
            <div class="absolute top-2 right-4 opacity-20">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>



        <!-- Table Container -->
        <div id="table-view" class="overflow-x-auto hidden lg:block">
            <table class="min-w-full checklist-table border-collapse">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-200">
                    <tr>
                        <th class="sticky left-0 bg-gradient-to-r from-gray-100 to-gray-200 px-2 sm:px-4 py-3 sm:py-4 text-left text-xs sm:text-sm font-bold text-gray-800 border-r-2 border-gray-300 z-20 item-header-cell shadow-sm">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>PROHIBITED ITEMS</span>
                            </div>
                        </th>
                        <!-- Days headers will be added here by JavaScript -->
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100" id="checklist-tbody">
                    <!-- Checklist items will be populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Enhanced Mobile Card View -->
        <div id="cards-view" class="block lg:hidden p-4 space-y-4">
            <div id="cards-container">
                <!-- Cards will be populated by JavaScript -->
            </div>
        </div>

        <!-- Enhanced Note Modal -->
        <div id="note-modal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md mx-auto mt-20 sm:mt-32 p-6 max-h-[80vh] overflow-y-auto border border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                        </svg>
                        <h2 class="text-lg font-bold text-gray-800">Catatan</h2>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full" id="modal-info"></span>
                        <button onclick="closeNoteModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <textarea id="note-textarea" rows="4" class="w-full border-2 border-gray-200 rounded-xl p-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all duration-200 resize-none" placeholder="Masukkan catatan untuk hari ini..."></textarea>
                <div class="mt-6 flex justify-end space-x-3">
                    <button onclick="closeNoteModal()" class="px-6 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 transition-colors text-sm font-medium text-gray-700">Batal</button>
                    <button onclick="saveNote()" class="px-6 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 text-sm font-medium shadow-lg hover:shadow-xl">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Legend -->
    <div class="mt-6 bg-white rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100">
        <h4 class="text-base sm:text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            Keterangan:
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 text-xs sm:text-sm">
            <div class="flex items-center p-3 bg-green-50 rounded-xl border border-green-200">
                <div class="w-6 h-6 bg-green-500 rounded-full mr-3 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="leading-tight font-medium text-green-800">Sudah dicek hari ini</span>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                <div class="w-6 h-6 bg-gray-300 rounded-full mr-3 flex-shrink-0"></div>
                <span class="leading-tight font-medium text-gray-700">Belum dicek</span>
            </div>
            <div class="flex items-center p-3 bg-yellow-50 rounded-xl border border-yellow-200">
                <div class="w-6 h-6 bg-yellow-500 rounded-full mr-3 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="leading-tight font-medium text-yellow-800">Terlewat (hari sebelumnya)</span>
            </div>
            <div class="flex items-center p-3 bg-blue-50 rounded-xl border border-blue-200">
                <div class="w-6 h-6 bg-blue-600 rounded-full mr-3 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <span class="leading-tight font-medium text-blue-800">Ada catatan harian</span>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                <div class="w-6 h-6 border-2 border-gray-400 rounded-full mr-3 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <span class="leading-tight font-medium text-gray-800">Tombol catatan harian</span>
            </div>
        </div>
    </div>

    <!-- Enhanced Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-40 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl p-6 flex items-center space-x-4 max-w-sm shadow-2xl border border-gray-200">
                <div class="relative">
                    <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-200"></div>
                    <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent absolute top-0"></div>
                </div>
                <div class="text-gray-700">
                    <div class="font-semibold">Menyimpan data...</div>
                    <div class="text-sm text-gray-500">Mohon tunggu sebentar</div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    /* Hapus CSS display: none, ganti dengan: */

    .checklist-table {
        min-width: 600px;
        max-width: none;
        /* Hilangkan max-width */
        width: max-content;
        /* Biarkan tabel selebar kontennya */
    }

    #table-view {
        max-width: 72vw;
        /* Batasi container */
        overflow-x: auto;
        /* Enable horizontal scroll */
        overflow-y: visible;
    }

    /* Optional: Tambahkan indikator scroll */
    #table-view::after {
        /* content: "← Geser untuk melihat hari lainnya →"; */
        display: block;
        text-align: center;
        color: #6b7280;
        font-size: 12px;
        padding: 4px;
        background: #f9fafb;
    }

    .checklist-cell {
        width: calc((100vw - 180px - 32px) / 7);
        /* Dinamis berdasarkan viewport */
        min-width: 40px;
        /* Minimal width untuk mobile */
        max-width: 65px;
        /* Maksimal width untuk desktop */
        padding: 6px 2px;
        text-align: center;
        border-right: 1px solid #e5e7eb;
        background-color: white;
        vertical-align: middle;
        position: relative;
        font-size: 10px;
    }

    @media (min-width: 480px) {
        .checklist-cell {
            width: calc((100vw - 220px - 32px) / 7);
            min-width: 45px;
            padding: 8px 3px;
            font-size: 11px;
        }
    }

    @media (min-width: 640px) {
        .checklist-cell {
            width: 65px;
            min-width: 65px;
            padding: 10px 6px;
            font-size: 12px;
        }
    }

    .item-name-cell {
        width: 180px;
        /* Fixed width untuk mobile */
        min-width: 180px;
        padding: 8px 12px;
        word-wrap: break-word;
        white-space: normal;
        line-height: 1.3;
        vertical-align: middle;
        font-size: 11px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-right: 2px solid #e2e8f0 !important;
    }

    @media (min-width: 480px) {
        .item-name-cell {
            width: 220px;
            min-width: 220px;
            padding: 10px 14px;
            font-size: 12px;
            line-height: 1.4;
        }
    }

    @media (min-width: 640px) {
        .item-name-cell {
            width: 280px;
            min-width: 280px;
            padding: 16px 20px;
            font-size: 14px;
            line-height: 1.5;
        }
    }

    .item-header-cell {
        width: 180px;
        min-width: 180px;
        font-size: 10px;
        padding: 8px 12px;
    }

    @media (min-width: 480px) {
        .item-header-cell {
            width: 220px;
            min-width: 220px;
            font-size: 11px;
        }
    }

    @media (min-width: 640px) {
        .item-header-cell {
            width: 280px;
            min-width: 280px;
            font-size: 14px;
            padding: 12px 20px;
        }
    }

    .note-button {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 2px solid #d1d5db;
        background: white;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 8px;
        cursor: pointer;
    }

    @media (min-width: 480px) {
        .note-button {
            width: 20px;
            height: 20px;
            font-size: 9px;
        }
    }

    @media (min-width: 640px) {
        .note-button {
            width: 24px;
            height: 24px;
            font-size: 12px;
            border-radius: 6px;
        }
    }

    .note-button:hover {
        border-color: #3b82f6;
        color: #3b82f6;
        background: #eff6ff;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .note-button.has-note {
        background: linear-gradient(45deg, #3b82f6, #1d4ed8);
        color: white;
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    /* Enhanced Mobile view buttons */
    .mobile-view-btn {
        color: #6b7280;
        background: transparent;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .mobile-view-btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .mobile-view-btn:hover:before {
        left: 100%;
    }

    .mobile-view-btn.active {
        background: linear-gradient(45deg, #3b82f6, #1d4ed8);
        color: white;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    /* Enhanced Mobile Card Styles */
    .item-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .item-card:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
    }

    .item-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border-color: #3b82f6;
    }

    .item-card h4 {
        font-size: 14px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 12px;
        line-height: 1.4;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    @media (min-width: 480px) {
        .item-card h4 {
            font-size: 16px;
        }
    }

    .days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        margin-bottom: 16px;
        padding: 8px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    @media (min-width: 480px) {
        .days-grid {
            gap: 6px;
            padding: 12px;
        }
    }

    .day-cell {
        text-align: center;
        padding: 6px 2px;
        border-radius: 8px;
        min-height: 45px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: all 0.2s ease;
        background: white;
        border: 2px solid #e5e7eb;
    }

    @media (min-width: 480px) {
        .day-cell {
            padding: 8px 4px;
            min-height: 50px;
            border-radius: 10px;
        }
    }

    .day-cell:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .day-number {
        font-size: 10px;
        color: #6b7280;
        margin-bottom: 3px;
        font-weight: 600;
    }

    @media (min-width: 480px) {
        .day-number {
            font-size: 11px;
            margin-bottom: 4px;
        }
    }

    /* Enhanced Checkbox styles */
    .mobile-checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid;
        border-radius: 50%;
        transition: all 0.4s ease;
        cursor: pointer;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (min-width: 480px) {
        .mobile-checkbox {
            width: 22px;
            height: 22px;
            border-width: 3px;
        }
    }

    @media (min-width: 640px) {
        .mobile-checkbox {
            width: 24px;
            height: 24px;
        }
    }

    .mobile-checkbox.checked {
        border-color: #10b981;
        background: linear-gradient(135deg, #10b981, #059669);
        animation: pulse-success 0.6s ease-out;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
    }

    .mobile-checkbox.unchecked {
        border-color: #d1d5db;
        background-color: white;
    }

    .mobile-checkbox.unchecked:hover {
        border-color: #3b82f6;
        background-color: #eff6ff;
        transform: scale(1.1);
    }

    .mobile-checkbox.missed {
        border-color: #f59e0b;
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        cursor: not-allowed;
        opacity: 0.8;
        box-shadow: 0 0 20px rgba(245, 158, 11, 0.3);
    }

    /* Table container untuk memastikan no horizontal scroll */
    .table-container {
        width: 100%;
        max-width: 100vw;
        overflow: visible;
        /* Ubah dari overflow-x-auto */
    }

    /* Ensure sticky positioning works properly */
    .sticky {
        position: -webkit-sticky;
        position: sticky;
        z-index: 10;
    }

    /* Enhanced table hover effects */
    .checklist-table td {
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s ease;
    }

    .checklist-table tr:hover td {
        background-color: #f8fafc;
    }

    .checklist-table tr:hover .sticky {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }


    .cell-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
    }

    @media (min-width: 480px) {
        .cell-content {
            gap: 4px;
        }
    }

    @media (min-width: 640px) {
        .cell-content {
            gap: 6px;
        }
    }

    /* Responsive improvements untuk header */
    .header-icon {
        width: 12px;
        height: 12px;
    }

    @media (min-width: 480px) {
        .header-icon {
            width: 14px;
            height: 14px;
        }
    }

    @media (min-width: 640px) {
        .header-icon {
            width: 16px;
            height: 16px;
        }
    }

    /* Checkbox di dalam cell */
    .table-checkbox {
        width: 16px;
        height: 16px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
    }

    @media (min-width: 480px) {
        .table-checkbox {
            width: 18px;
            height: 18px;
        }
    }

    @media (min-width: 640px) {
        .table-checkbox {
            width: 20px;
            height: 20px;
        }
    }

    .table-checkbox.checked {
        background: linear-gradient(135deg, #10b981, #059669);
        border-color: #10b981;
        color: white;
    }

    .table-checkbox.missed {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-color: #f59e0b;
    }

    /* Animation classes */
    @keyframes pulse-success {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        50% {
            transform: scale(1.05);
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }
    }

    /* Notification styles */
    .notification {
        animation: slideInFromRight 0.3s ease-out;
    }

    @keyframes slideInFromRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Progress bar animation */
    .progress-bar-animated {
        transition: width 1s ease-in-out;
    }

    /* Card entrance animation */
    .card-animate {
        animation: fadeInUp 0.5s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Container utama */
    .main-container {
        max-width: 100vw;
        overflow-x: hidden;
        padding: 8px;
    }

    @media (min-width: 480px) {
        .main-container {
            padding: 12px;
        }
    }

    @media (min-width: 640px) {
        .main-container {
            padding: 16px;
        }
    }

    /* Responsive text untuk legend */
    .legend-text {
        font-size: 10px;
        line-height: 1.3;
    }

    @media (min-width: 480px) {
        .legend-text {
            font-size: 11px;
            line-height: 1.4;
        }
    }

    @media (min-width: 640px) {
        .legend-text {
            font-size: 12px;
            line-height: 1.5;
        }
    }

    /* Responsive grid untuk legend */
    .legend-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
    }

    @media (min-width: 480px) {
        .legend-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
    }

    @media (min-width: 768px) {
        .legend-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .legend-grid {
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
        }
    }
</style>

<script>
    // Global variables
    let tenant = @json($tenant);
    let logbookId = '{{ $logbook->sweepingpiID }}';
    let currentMonth = '{{ $month }}';
    let currentYear = '{{ $year }}';
    let prohibitedItems = @json($prohibitedItems);
    let checklist = @json($checklistData ?? []);
    let dailyNotes = @json($dailyNotes ?? []); // Notes per hari saja

    let saveTimeout = null;
    let currentModalItem = null;
    let currentModalDay = null;
    let currentMobileView = 'table'; // 'table' or 'cards'

    let statistics = {
        completion_rate: 0,
        total_checked: 0,
        total_pending: 0,
        total_violations: 0
    };

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeApp();
    });

    function initializeApp() {
        console.log('=== DEBUG INFO ===');
        console.log('Prohibited Items:', prohibitedItems);
        console.log('Prohibited Items Length:', prohibitedItems?.length);
        console.log('Checklist Data:', checklist);
        console.log('Daily Notes:', dailyNotes);
        console.log('Current Month:', currentMonth);
        console.log('Current Year:', currentYear);
        console.log('Days in Month:', getDaysInMonth());

        // Check if data exists
        if (!prohibitedItems || prohibitedItems.length === 0) {
            console.error('❌ PROHIBITED ITEMS IS EMPTY OR NULL!');
            document.getElementById('checklist-tbody').innerHTML = `
            <tr>
                <td colspan="32" class="text-center py-8 text-gray-500">
                    tidak ada prohibited items yang ditemukan. Tolong cek kembali data.
                </td>
            </tr>
        `;
            return;
        }

        // Set month/year
        const months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        document.getElementById('current-month-year').textContent = `${months[currentMonth - 1]} ${currentYear}`;

        // Initialize checklist and notes
        initializeChecklist();
        initializeNotes();

        // Render table
        renderTable();
        renderCards();

        // Update statistics
        updateStats();
    }

    function getDaysInMonth() {
        return new Date(currentYear, currentMonth, 0).getDate();
    }

    function initializeChecklist() {
        if (!prohibitedItems || prohibitedItems.length === 0) {
            console.error('No prohibited items found!');
            return;
        }

        const daysInMonth = getDaysInMonth();

        prohibitedItems.forEach((item, itemIndex) => {
            if (!checklist[itemIndex]) {
                checklist[itemIndex] = {};
            }

            for (let day = 0; day < daysInMonth; day++) {
                if (checklist[itemIndex][day] === undefined) {
                    checklist[itemIndex][day] = false;
                }
            }
        });

        console.log('Checklist initialized:', checklist);
    }

    function initializeNotes() {
        console.log('🔄 Initializing notes...');
        console.log('📝 dailyNotes from server:', dailyNotes);

        if (!dailyNotes) {
            console.log('📝 dailyNotes is null/undefined, creating empty object');
            dailyNotes = {};
        }

        const daysInMonth = getDaysInMonth();

        for (let day = 0; day < daysInMonth; day++) {
            if (dailyNotes[day] === undefined) {
                dailyNotes[day] = '';
            } else if (dailyNotes[day]) {
                console.log(`📝 Found existing note for day ${day + 1}:`, dailyNotes[day]);
            }
        }

        console.log('📝 Daily notes after initialization:', dailyNotes);
    }

    function toggleMobileView(view) {
        currentMobileView = view;

        // Update button styles
        document.getElementById('btn-table').classList.toggle('active', view === 'table');
        document.getElementById('btn-cards').classList.toggle('active', view === 'cards');

        // Show/hide views
        const tableView = document.getElementById('table-view');
        const cardsView = document.getElementById('cards-view');

        if (view === 'table') {
            tableView.style.display = 'block';
            cardsView.style.display = 'none';
        } else {
            tableView.style.display = 'none';
            cardsView.style.display = 'block';
            cardsView.classList.remove('hidden');
        }
    }

    function renderTable() {
        console.log('🔄 Starting renderTable...');

        const daysInMonth = getDaysInMonth();
        console.log('Days in month:', daysInMonth);

        // First, add day headers to the existing header row
        const headerRow = document.querySelector('thead tr');
        if (!headerRow) {
            console.error('❌ Header row not found!');
            return;
        }

        // Remove existing day headers (keep only the first th)
        while (headerRow.children.length > 1) {
            headerRow.removeChild(headerRow.lastChild);
        }

        // Add day headers
        for (let day = 1; day <= daysInMonth; day++) {
            const th = document.createElement('th');
            th.className = 'checklist-cell px-1 sm:px-2 py-2 sm:py-3 text-center text-xs sm:text-sm font-semibold text-gray-700 border-r border-gray-200 bg-gray-100';
            th.textContent = day;
            th.style.width = window.innerWidth < 640 ? '45px' : '60px';
            th.style.minWidth = window.innerWidth < 640 ? '45px' : '60px';
            headerRow.appendChild(th);
        }

        // Render checklist items
        const tbody = document.getElementById('checklist-tbody');
        if (!tbody) {
            console.error('❌ Tbody not found!');
            return;
        }

        let tbodyHtml = '';

        console.log('📝 Rendering', prohibitedItems.length, 'items...');

        prohibitedItems.forEach((item, itemIndex) => {
            console.log(`Rendering item ${itemIndex}:`, item.items_name);

            tbodyHtml += `
            <tr class="hover:bg-gray-50 transition-colors duration-150">
                <td class="sticky left-0 bg-white item-name-cell text-xs sm:text-sm font-medium text-gray-900 border-r-2 border-gray-300 z-10">
                    <div class="leading-tight">
                        ${item.items_name}
                        <div class="text-xs text-gray-500 mt-1">(${item.quantity} item)</div>
                    </div>
                </td>
        `;

            for (let day = 0; day < daysInMonth; day++) {
                const isChecked = checklist[itemIndex] && checklist[itemIndex][day];
                const hasNote = dailyNotes[day] && dailyNotes[day].trim() !== '';

                // Calculate isMissed for this specific day and item
                const checkDate = new Date(currentYear, currentMonth - 1, day + 1);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                checkDate.setHours(0, 0, 0, 0);
                const isMissed = checkDate < today && !isChecked;

                // Get checkbox class
                let checkboxClass;
                if (isChecked) {
                    checkboxClass = 'border-green-500 bg-green-500';
                } else if (isMissed) {
                    checkboxClass = 'border-yellow-500 bg-yellow-100 opacity-60';
                } else {
                    checkboxClass = 'border-gray-300 bg-white hover:border-blue-400';
                }

                const checkboxSize = window.innerWidth < 640 ? 'w-5 h-5' : 'w-6 h-6';
                const iconSize = window.innerWidth < 640 ? 'w-3 h-3' : 'w-4 h-4';

                tbodyHtml += `
                <td class="checklist-cell">
                    <div class="cell-content">
                        <button
                            type="button"
                            class="${checkboxSize} border-2 rounded transition-all duration-200 relative ${checkboxClass}"
                            onclick="${`toggleCheck(${itemIndex}, ${day})`}"
                            title="${isMissed ? `Terlewat - ${item.items_name} tanggal ${day + 1}` : `Toggle checklist ${item.items_name} tanggal ${day + 1}`}">
                        
                            ${isChecked ? `
                            <svg class="${iconSize} text-white absolute inset-0 m-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            ` : ''}
                        </button>
                        
                        ${itemIndex === prohibitedItems.length - 1 ? `
                        <button
                            type="button"
                            class="note-button mt-1 sm:mt-2 ${hasNote ? 'has-note' : ''}"
                            onclick="openNoteModal(${day})"
                            title="Tambah catatan untuk tanggal ${day + 1}${hasNote ? ' - Ada catatan' : ''}">
                            <svg class="w-2 h-2 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        ` : ''
                        }
                    </div>
                </td>
            `;
            }

            tbodyHtml += '</tr>';
        });

        console.log('📄 Generated HTML length:', tbodyHtml.length);
        tbody.innerHTML = tbodyHtml;
        console.log('✅ Table rendered successfully');
    }

    function renderCards() {
        const container = document.getElementById('cards-container');
        if (!container) return;

        const daysInMonth = getDaysInMonth();
        let cardsHtml = '';

        prohibitedItems.forEach((item, itemIndex) => {
            cardsHtml += `
                <div class="item-card">
                    <h4>${item.items_name}</h4>
                    <p class="text-xs text-gray-500 mb-3">${item.quantity} item</p>
                    
                    <div class="days-grid">
            `;

            for (let day = 0; day < daysInMonth; day++) {
                const isChecked = checklist[itemIndex] && checklist[itemIndex][day];
                const hasNote = dailyNotes[day] && dailyNotes[day].trim() !== '';

                // Calculate isMissed
                const checkDate = new Date(currentYear, currentMonth - 1, day + 1);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                checkDate.setHours(0, 0, 0, 0);
                const isMissed = checkDate < today && !isChecked;

                let checkboxClass;
                if (isChecked) {
                    checkboxClass = 'checked';
                } else if (isMissed) {
                    checkboxClass = 'missed';
                } else {
                    checkboxClass = 'unchecked';
                }

                cardsHtml += `
                    <div class="day-cell">
                        <div class="day-number">${day + 1}</div>
                        <button
                            type="button"
                            class="mobile-checkbox ${checkboxClass}"
                            
                            onclick="toggleCheck(${itemIndex}, ${day})"
                            title="${isMissed ? `Terlewat - ${item.items_name} tanggal ${day + 1}` : `Toggle checklist ${item.items_name} tanggal ${day + 1}`}">
                            ${isChecked ? `
                            <svg class="w-3 h-3 text-white absolute inset-0 m-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            ` : ''}
                        </button>
                    </div>
                `;
            }

            cardsHtml += `
                    </div>
            `;

            // Add notes section for the last item
            if (itemIndex === prohibitedItems.length - 1) {
                cardsHtml += `
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <h5 class="text-sm font-semibold text-gray-700 mb-2">Catatan Harian:</h5>
                        <div class="grid grid-cols-7 gap-2">
                `;

                for (let day = 0; day < daysInMonth; day++) {
                    const hasNote = dailyNotes[day] && dailyNotes[day].trim() !== '';

                    cardsHtml += `
                        <button
                            type="button"
                            class="w-8 h-8 rounded border ${hasNote ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-600 border-gray-300'} text-xs font-medium hover:bg-blue-100 transition-colors"
                            onclick="openNoteModal(${day})"
                            title="Catatan tanggal ${day + 1}${hasNote ? ' - Ada catatan' : ''}">
                            ${day + 1}
                        </button>
                    `;
                }

                cardsHtml += `
                        </div>
                    </div>
                `;
            }

            cardsHtml += '</div>';
        });

        container.innerHTML = cardsHtml;
    }

    function toggleCheck(itemIndex, dayIndex) {
        if (!checklist[itemIndex]) {
            checklist[itemIndex] = {};
        }

        checklist[itemIndex][dayIndex] = !checklist[itemIndex][dayIndex];
        updateStats();
        renderTable(); // Re-render to update checkbox styles
        renderCards(); // Re-render cards view as well
        autoSave();
    }

    function openNoteModal(dayIndex) {
        console.log(`🔄 Opening note modal for day ${dayIndex + 1}`);
        console.log(`📝 Existing note:`, dailyNotes[dayIndex]);

        currentModalDay = dayIndex;
        currentModalItem = null;

        const dayNumber = dayIndex + 1;

        document.getElementById('modal-info').textContent = `Catatan Tanggal ${dayNumber}`;
        document.getElementById('note-textarea').value = dailyNotes[dayIndex] ? dailyNotes[dayIndex] : '';
        document.getElementById('note-modal').classList.remove('hidden');

        // Focus on textarea
        setTimeout(() => {
            document.getElementById('note-textarea').focus();
        }, 100);
    }

    function closeNoteModal() {
        document.getElementById('note-modal').classList.add('hidden');
        currentModalItem = null;
        currentModalDay = null;
    }

    function saveNote() {
        if (currentModalDay === null) return;

        const noteText = document.getElementById('note-textarea').value;
        dailyNotes[currentModalDay] = noteText;

        closeNoteModal();
        renderTable(); // Re-render to update note button styles
        renderCards(); // Re-render cards view as well
        autoSave();

        showNotification('Catatan berhasil disimpan', 'success');
    }

    function updateStats() {
        let totalChecked = 0;
        const daysInMonth = getDaysInMonth();
        const totalCells = prohibitedItems.length * daysInMonth;

        prohibitedItems.forEach((item, itemIndex) => {
            for (let day = 0; day < daysInMonth; day++) {
                if (checklist[itemIndex] && checklist[itemIndex][day]) {
                    totalChecked++;
                }
            }
        });

        statistics.completion_rate = totalCells > 0 ? Math.round((totalChecked / totalCells) * 100) : 0;
        statistics.total_checked = totalChecked;
        statistics.total_pending = totalCells - totalChecked;

        // Update DOM
        document.getElementById('completion-rate').textContent = statistics.completion_rate + '%';
        document.getElementById('total-checked').textContent = statistics.total_checked;
        document.getElementById('total-pending').textContent = statistics.total_pending;
    }

    function autoSave() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            saveProgress();
        }, 2000); // Auto save after 2 seconds of inactivity
    }

    async function saveProgress() {
        const loadingOverlay = document.getElementById('loading-overlay');

        try {
            loadingOverlay.classList.remove('hidden');
            console.log('Saving progress...');

            const saveUrl = `{{ route('logbookSweppingPI.store') }}`;

            const response = await fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    logbook_id: logbookId,
                    items_name: prohibitedItems.map(item => item.items_name),
                    quantity: prohibitedItems.map(item => item.quantity),
                    checklist_data: checklist,
                    daily_notes: formatDailyNotesForBackend(dailyNotes), // Fungsi baru
                })
            });

            const data = await response.json();
            if (data.success) {
                console.log('Data saved successfully');
                showNotification('Data berhasil disimpan', 'success');
            } else {
                console.error('Error: ' + data.message);
                showNotification('Error: ' + data.message, 'error');
            }
        } catch (error) {
            console.error('Error saving progress:', error);
            showNotification('Terjadi kesalahan saat menyimpan data', 'error');
        } finally {
            loadingOverlay.classList.add('hidden');
        }
    }

    function showNotification(message, type = 'info') {
        // Simple notification - you can replace with a proper notification library
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-3 sm:p-4 rounded-lg shadow-lg z-50 text-white text-sm sm:text-base max-w-xs sm:max-w-sm ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);

        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        // Re-render table to adjust cell sizes
        renderTable();
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Escape key to close modal
        if (e.key === 'Escape') {
            closeNoteModal();
        }
        // Ctrl+S to save
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            saveProgress();
        }
        // Enter key to save note when modal is open
        if (e.key === 'Enter' && e.ctrlKey && currentModalDay !== null) {
            e.preventDefault();
            saveNote();
        }
    });

    function formatDailyNotesForBackend(dailyNotes) {
        console.log('🐛 formatDailyNotesForBackend input:', dailyNotes);
        console.log('🐛 currentYear:', currentYear, 'currentMonth:', currentMonth);

        const formattedNotes = [];
        const daysInMonth = getDaysInMonth();

        for (let day = 0; day < daysInMonth; day++) {
            if (dailyNotes[day] && dailyNotes[day].trim() !== '') {
                // PERBAIKAN: Gunakan format tanggal yang lebih explicit
                const dayNumber = day + 1; // Convert 0-based to 1-based
                const dateString = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(dayNumber).padStart(2, '0')}`;

                const noteItem = {
                    tanggal: dateString,
                    notes: dailyNotes[day]
                };

                console.log(`🐛 Day ${dayNumber} (index ${day}) - Adding note:`, noteItem);
                formattedNotes.push(noteItem);
            }
        }

        console.log('🐛 formatDailyNotesForBackend output:', formattedNotes);
        return formattedNotes;
    }
</script>
@endsection