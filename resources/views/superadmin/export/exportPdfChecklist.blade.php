@extends('layouts.app')

@section('title', 'Export PDF Checklist')

@section('content')
<div class="bg-gray-50 min-h-screen py-4 px-3 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Back Button - Mobile Optimized -->
        <div class="mb-4">
            <a href="javascript:history.back()"
                class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition touch-manipulation">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <!-- Export PDF Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-4 py-4 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900">Export PDF Checklist</h2>
            </div>

            <!-- Form Content -->
            <div class="p-4 sm:p-6">
                <form method="GET" action="{{ route('export.checklist') }}" id="exportForm">
                    @csrf

                    <!-- Filter Section - Mobile Optimized -->
                    <div class="space-y-4 mb-6">
                        <!-- Jenis Checklist -->
                        <div>
                            <label for="form_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Checklist
                            </label>
                            <select name="form_type" id="form_type"
                                class="block w-full py-3 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base">
                                <option value="kendaraan" {{ ($formType ?? 'kendaraan' )=='kendaraan' ? 'selected' : ''
                                    }}>Checklist Kendaraan Patroli</option>
                                <option value="penyisiran" {{ ($formType ?? '' )=='penyisiran' ? 'selected' : '' }}>
                                    Checklist Penyisiran Ruang Tunggu</option>
                                <option value="senpi" {{ ($formType ?? '' )=='senpi' ? 'selected' : '' }}>Checklist
                                    Senjata Api</option>
                                <option value="pencatatan_pi" {{ ($formType ?? '' )=='pencatatan_pi' ? 'selected' : ''
                                    }}>Form Pencatatan PI</option>
                                <option value="manual_book" {{ ($formType ?? '' )=='manual_book' ? 'selected' : '' }}>
                                    Buku Pemeriksaan Manual</option>
                            </select>
                        </div>

                        <!-- Lokasi -->
                        <div id="location-filter">
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi
                            </label>
                            <select name="location" id="location"
                                class="block w-full py-3 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base">
                                <option value="">Semua Lokasi</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range - Mobile Optimized Layout -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Tanggal Mulai -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Mulai
                                </label>
                                <input type="date" name="start_date" id="start_date"
                                    value="{{ date('Y-m-d', strtotime('-1 month')) }}"
                                    class="block w-full py-3 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base">
                            </div>

                            <!-- Tanggal Akhir -->
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Akhir
                                </label>
                                <input type="date" name="end_date" id="end_date" value="{{ date('Y-m-d') }}"
                                    class="block w-full py-3 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base">
                            </div>
                        </div>
                    </div>

                    <!-- Data Display Section -->
                    <div id="data-section">
                        <!-- Mobile Data Counter -->
                        <div class="bg-blue-50 px-4 py-3 rounded-lg mb-4 border border-blue-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-blue-800" id="data-title">Data Checklist</h3>
                                <span class="text-xs text-blue-600" id="data-count">0 item</span>
                            </div>
                        </div>

                        <!-- Desktop Table View -->
                        <div class="hidden md:block">
                            <!-- Tabel Checklist Kendaraan -->
                            <div id="table-kendaraan"
                                class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'kendaraan') != 'kendaraan' ? 'hidden' : '' }}">
                                <div class="bg-blue-50 px-4 py-2 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-blue-800">Data Checklist Kendaraan Patroli
                                    </h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Tanggal</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Tipe</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Petugas</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="kendaraan-tbody" class="bg-white divide-y divide-gray-200">
                                            <!-- Data akan dimuat via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tabel Checklist Penyisiran -->
                            <div id="table-penyisiran"
                                class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'kendaraan') != 'penyisiran' ? 'hidden' : '' }}">
                                <div class="bg-green-50 px-4 py-2 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-green-800">Data Checklist Penyisiran Ruang
                                        Tunggu</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Tanggal</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Lokasi</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Petugas</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="penyisiran-tbody" class="bg-white divide-y divide-gray-200">
                                            <!-- Data akan dimuat via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tabel Checklist Senpi -->
                            <div id="table-senpi"
                                class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'kendaraan') != 'senpi' ? 'hidden' : '' }}">
                                <div class="bg-purple-50 px-4 py-2 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-purple-800">Data Checklist Senjata Api</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Tanggal</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    No. Senpi</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Petugas</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="senpi-tbody" class="bg-white divide-y divide-gray-200">
                                            <!-- Data akan dimuat via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tabel Form Pencatatan PI -->
                            <div id="table-pencatatan_pi"
                                class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'kendaraan') != 'pencatatan_pi' ? 'hidden' : '' }}">
                                <div class="bg-orange-50 px-4 py-2 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-orange-800">Data Form Pencatatan PI</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Tanggal</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Petugas</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Jenis PI</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Lokasi</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pencatatan_pi-tbody" class="bg-white divide-y divide-gray-200">
                                            <!-- Data akan dimuat via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tabel Buku Pemeriksaan Manual -->
                            <div id="table-manual_book"
                                class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'kendaraan') != 'manual_book' ? 'hidden' : '' }}">
                                <div class="bg-red-50 px-4 py-2 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-red-800">Data Buku Pemeriksaan Manual</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Tanggal</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Petugas</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Lokasi</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="manual_book-tbody" class="bg-white divide-y divide-gray-200">
                                            <!-- Data akan dimuat via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="md:hidden">
                            <div id="mobile-data-container" class="space-y-3">
                                <!-- Mobile cards will be populated by JavaScript -->
                            </div>
                        </div>

                        <!-- Select All Toggle for Mobile -->
                        <div class="md:hidden mb-4">
                            <label class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border">
                                <input type="checkbox" id="select-all-mobile"
                                    class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="text-sm font-medium text-gray-700">Pilih Semua</span>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons - Mobile Optimized -->
                    <div class="flex flex-col gap-3 mt-6">
                        <!-- Primary Actions -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <button type="button" onclick="previewData()"
                                class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 touch-manipulation">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                Preview
                            </button>

                            <button type="button" onclick="exportSelected()"
                                class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 touch-manipulation">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Export Dipilih
                            </button>

                            <button type="button" onclick="exportAll()"
                                class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 touch-manipulation">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                    </path>
                                </svg>
                                Export Semua
                            </button>
                        </div>

                        <!-- Selection Counter for Mobile -->
                        <div class="md:hidden bg-blue-50 px-4 py-3 rounded-lg border border-blue-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-blue-700">Item dipilih:</span>
                                <span class="text-sm font-semibold text-blue-800" id="selected-count-mobile">0</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview - Mobile Optimized -->
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-2 sm:mx-auto sm:top-10 p-4 sm:p-5 border w-auto sm:w-3/4 lg:w-auto shadow-lg rounded-md bg-white"
        style="max-width: 850px;">
        <div class="flex justify-between items-center pb-3 border-b">
            <p class="text-lg sm:text-2xl font-bold">Preview Checklist</p>
            <div class="cursor-pointer z-50 p-1 hover:bg-gray-100 rounded-full" onclick="closeModal()">
                <svg class="fill-current text-black w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18">
                    <path
                        d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z">
                    </path>
                </svg>
            </div>
        </div>
        <div id="modalContent" class="max-h-[70vh] sm:max-h-[85vh] overflow-y-auto mt-4">
            <!-- Preview content will be loaded here -->
        </div>
    </div>
</div>

<script>
    let currentData = [];

    // Mobile card template functions
    function createMobileCard(item, formType) {
        const id = item.id;
        let cardContent = '';

        switch (formType) {
            case 'kendaraan':
                cardContent = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-2">
                                <input type="checkbox" name="selected_reports[]" value="${id}"
                                    class="mobile-checkbox h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <div class="text-sm font-medium text-gray-900 truncate">${item.nomor_polisi || 'N/A'}</div>
                            </div>
                            <div class="text-sm text-gray-500 mb-1">
                                <span class="font-medium">Tanggal:</span> ${formatDate(item.date)}
                            </div>
                            <div class="text-sm text-gray-500 mb-2">
                                <span class="font-medium">Petugas:</span> ${item.petugas?.name || 'N/A'}
                            </div>
                        </div>
                        <div class="ml-3 flex-shrink-0">
                            ${getStatusBadge(item.status)}
                        </div>
                    </div>
                `;
                break;
            case 'penyisiran':
                cardContent = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-2">
                                <input type="checkbox" name="selected_reports[]" value="${id}"
                                    class="mobile-checkbox h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <div class="text-sm font-medium text-gray-900 truncate">${item.lokasi?.name || 'N/A'}</div>
                            </div>
                            <div class="text-sm text-gray-500 mb-1">
                                <span class="font-medium">Tanggal:</span> ${formatDate(item.date)}
                            </div>
                            <div class="text-sm text-gray-500 mb-2">
                                <span class="font-medium">Petugas:</span> ${item.petugas?.name || 'N/A'}
                            </div>
                        </div>
                        <div class="ml-3 flex-shrink-0">
                            ${getStatusBadge(item.status)}
                        </div>
                    </div>
                `;
                break;
            case 'senpi':
                cardContent = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-2">
                                <input type="checkbox" name="selected_reports[]" value="${id}"
                                    class="mobile-checkbox h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <div class="text-sm font-medium text-gray-900 truncate">${item.nomor_senpi || 'N/A'}</div>
                            </div>
                            <div class="text-sm text-gray-500 mb-1">
                                <span class="font-medium">Tanggal:</span> ${formatDate(item.date)}
                            </div>
                            <div class="text-sm text-gray-500 mb-2">
                                <span class="font-medium">Petugas:</span> ${item.petugas?.name || 'N/A'}
                            </div>
                        </div>
                        <div class="ml-3 flex-shrink-0">
                            ${getStatusBadge(item.status)}
                        </div>
                    </div>
                `;
                break;
            case 'pencatatan_pi':
                cardContent = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-2">
                                <input type="checkbox" name="selected_reports[]" value="${id}"
                                    class="mobile-checkbox h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <div class="text-sm font-medium text-gray-900 truncate">${item.jenis_pi || 'N/A'}</div>
                            </div>
                            <div class="text-sm text-gray-500 mb-1">
                                <span class="font-medium">Tanggal:</span> ${formatDate(item.date)}
                            </div>
                            <div class="text-sm text-gray-500 mb-1">
                                <span class="font-medium">Petugas:</span> ${item.petugas?.name || 'N/A'}
                            </div>
                            <div class="text-sm text-gray-500 mb-2">
                                <span class="font-medium">Lokasi:</span> ${item.lokasi?.name || 'N/A'}
                            </div>
                        </div>
                        <div class="ml-3 flex-shrink-0">
                            ${getStatusBadge(item.status)}
                        </div>
                    </div>
                `;
                break;
            case 'manual_book':
                cardContent = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-2">
                                <input type="checkbox" name="selected_reports[]" value="${id}"
                                    class="mobile-checkbox h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <div class="text-sm font-medium text-gray-900 truncate">${item.petugas?.name || 'N/A'}</div>
                            </div>
                            <div class="text-sm text-gray-500 mb-1">
                                <span class="font-medium">Tanggal:</span> ${formatDate(item.date)}
                            </div>
                            <div class="text-sm text-gray-500 mb-2">
                                <span class="font-medium">Lokasi:</span> ${item.lokasi?.name || 'N/A'}
                            </div>
                        </div>
                        <div class="ml-3 flex-shrink-0">
                            ${getStatusBadge(item.status)}
                        </div>
                    </div>
                `;
                break;
        }

        return `
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                ${cardContent}
            </div>
        `;
    }

    function updateMobileView(formType, checklists) {
        const container = document.getElementById('mobile-data-container');
        if (!container) return;

        container.innerHTML = '';

        if (!checklists || checklists.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm">Tidak ada data ditemukan</p>
                </div>
            `;
            return;
        }

        checklists.forEach(item => {
            container.innerHTML += createMobileCard(item, formType);
        });

        // Update selection counter
        updateSelectionCounter();
    }

    function updateSelectionCounter() {
        const checkboxes = document.querySelectorAll('input[name="selected_reports[]"]:checked');
        const counter = document.getElementById('selected-count-mobile');
        if (counter) {
            counter.textContent = checkboxes.length;
        }
    }

    function updateDataTitle(formType) {
        const titles = {
            'kendaraan': 'Checklist Kendaraan Patroli',
            'penyisiran': 'Checklist Penyisiran Ruang Tunggu',
            'senpi': 'Checklist Senjata Api',
            'pencatatan_pi': 'Form Pencatatan PI',
            'manual_book': 'Buku Pemeriksaan Manual'
        };

        const titleElement = document.getElementById('data-title');
        if (titleElement) {
            titleElement.textContent = titles[formType] || 'Data Checklist';
        }
    }

    function updateDataCount(count) {
        const countElement = document.getElementById('data-count');
        if (countElement) {
            countElement.textContent = `${count} item`;
        }
    }

    // Fungsi untuk show/hide tabel berdasarkan form type
    function showTable(formType) {
        console.log('Showing table for form type:', formType);

        // Hide semua tabel
        const tables = document.querySelectorAll('.table-container');
        tables.forEach(table => table.classList.add('hidden'));

        // Show tabel yang sesuai
        const targetTable = document.getElementById(`table-${formType}`);
        if (targetTable) {
            targetTable.classList.remove('hidden');
            console.log('Table shown:', `table-${formType}`);
        } else {
            console.error('Table not found:', `table-${formType}`);
        }

        const locationFilter = document.getElementById('location-filter');
        if (formType === 'kendaraan'|| formType === 'senpi' || formType === 'pencatatan_pi' || formType === 'penyisiran') {
            locationFilter.classList.add('hidden');
        } else {
            locationFilter.classList.remove('hidden');
        }

        // Update mobile title
        updateDataTitle(formType);
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric', month: '2-digit', day: '2-digit',
            });
        } catch (e) {
            return dateString;
        }
    }

    function getStatusBadge(status) {
        const statusText = status ? status.toLowerCase() : 'pending';
        const statusClasses = {
            'approved': 'bg-green-100 text-green-800',
            'submitted': 'bg-yellow-100 text-yellow-800',
            'pending': 'bg-yellow-100 text-yellow-800',
            'draft': 'bg-gray-100 text-gray-800',
            'rejected': 'bg-red-100 text-red-800'
        };
        const className = statusClasses[statusText] || 'bg-gray-100 text-gray-800';
        const displayText = status === 'approved' ? 'Disetujui' :
                           status === 'submitted' ? 'Pending' :
                           status === 'draft' ? 'Draft' :
                           status === 'rejected' ? 'Ditolak' : 'Pending';
        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${className}">${displayText}</span>`;
    }

    function getCheckbox(id) {
        return `<input type="checkbox" name="selected_reports[]" value="${id}" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">`;
    }

    function updateTable(formType, checklists) {
        console.log('Updating table for form type:', formType, 'with data:', checklists);

        // Store current data
        currentData = checklists;

        // Update desktop table
        const tbody = document.getElementById(`${formType}-tbody`);
        if (!tbody) {
            console.error('tbody not found for ID:', `${formType}-tbody`);
            return;
        }

        tbody.innerHTML = '';

        if (!checklists || checklists.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data ditemukan</td></tr>';
        } else {
            checklists.forEach(item => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                let cells = '';
                const id = item.id;

                switch (formType) {
                    case 'kendaraan':
                        cells = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(item.date)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.nomor_polisi || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.petugas?.name || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${getStatusBadge(item.status)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">${getCheckbox(id)}</td>
                        `;
                        break;
                    case 'penyisiran':
                        cells = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(item.date)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.lokasi?.name || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.petugas?.name || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${getStatusBadge(item.status)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">${getCheckbox(id)}</td>
                        `;
                        break;
                    case 'senpi':
                        cells = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(item.date)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.nomor_senpi || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.petugas?.name || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${getStatusBadge(item.status)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">${getCheckbox(id)}</td>
                        `;
                        break;
                    case 'pencatatan_pi':
                        cells = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(item.date)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.petugas?.name || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.jenis_pi || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.lokasi?.name || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${getStatusBadge(item.status)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">${getCheckbox(id)}</td>
                        `;
                        break;
                    case 'manual_book':
                        cells = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(item.date)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.petugas?.name || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.lokasi?.name || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${getStatusBadge(item.status)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">${getCheckbox(id)}</td>
                        `;
                        break;
                }

                row.innerHTML = cells;
                tbody.appendChild(row);
            });
        }

        // Update mobile view
        updateMobileView(formType, checklists);
        updateDataCount(checklists.length);

        console.log('Table updated successfully with', checklists.length, 'rows');
    }

    // Modal functions
    const modal = document.getElementById('previewModal');
    const modalContent = document.getElementById('modalContent');

    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        modalContent.innerHTML = '';
        document.body.style.overflow = 'auto';
    }

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' || event.key === 'Esc') {
            if (!modal.classList.contains('hidden')) {
                closeModal();
            }
        }
    });

    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    async function previewData() {
        const formData = new FormData(document.getElementById('exportForm'));
        const formType = formData.get('form_type');
        const selectedIds = formData.getAll('selected_reports[]');

        if (selectedIds.length === 0) {
            alert('Silakan pilih minimal satu data untuk preview');
            return;
        }
        if (selectedIds.length > 1) {
            alert('Hanya satu data yang bisa di-review dalam satu waktu. Silakan pilih satu saja.');
            return;
        }

        const reportId = selectedIds[0];
        const url = `/export/checklist/review/${reportId}?form_type=${formType}`;

        try {
            modalContent.innerHTML = '<div class="text-center py-20"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div><p class="mt-4 text-gray-500">Loading preview...</p></div>';
            openModal();

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const html = await response.text();

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const formContent = doc.querySelector('.page-break-after');

            if (formContent) {
                modalContent.innerHTML = `<div class="transform scale-90 sm:scale-100 origin-top">${formContent.innerHTML}</div>`;
            } else {
                modalContent.innerHTML = html;
            }

        } catch (error) {
            console.error('Error fetching preview:', error);
            console.log(url);
            modalContent.innerHTML = '<div class="text-center py-20"><p class="text-red-500">Gagal memuat preview. Silakan coba lagi.</p></div>';
        }
    }

    function fetchFilteredData() {
        const formData = new FormData(document.getElementById('exportForm'));

        console.log('Fetching filtered data with params:', {
            form_type: formData.get('form_type'),
            location: formData.get('location'),
            start_date: formData.get('start_date'),
            end_date: formData.get('end_date')
        });

        fetch('{{ route("export.checklist.filter") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                form_type: formData.get('form_type'),
                location: formData.get('location'),
                start_date: formData.get('start_date'),
                end_date: formData.get('end_date')
            })
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.checklists) {
                updateTable(data.form_type, data.checklists);
            } else {
                console.error('No checklists data in response');
                alert('Tidak ada data yang ditemukan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data: ' + error.message);
        });
    }

    function exportSelected() {
        const form = document.getElementById('exportForm');
        if (form.querySelectorAll('input[name="selected_reports[]"]:checked').length === 0) {
            alert('Pilih minimal satu data untuk diekspor!');
            return;
        }
        const input = form.querySelector('input[name="export_type"]') || document.createElement('input');
        input.type = 'hidden';
        input.name = 'export_type';
        input.value = 'selected';
        if (!input.parentNode) form.appendChild(input);
        form.submit();
    }

    function exportAll() {
        const form = document.getElementById('exportForm');
        const input = form.querySelector('input[name="export_type"]') || document.createElement('input');
        input.type = 'hidden';
        input.name = 'export_type';
        input.value = 'all';
        if (!input.parentNode) form.appendChild(input);
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const formTypeSelect = document.getElementById('form_type');
        const locationSelect = document.getElementById('location');
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const selectAllMobile = document.getElementById('select-all-mobile');

        console.log('DOM loaded, initializing...');

        // Show initial table
        showTable(formTypeSelect.value);

        // Load initial data
        fetchFilteredData();

        // Event listeners untuk filter
        [locationSelect, startDate, endDate].forEach(element => {
            element.addEventListener('change', function() {
                console.log('Filter changed:', element.id, element.value);
                fetchFilteredData();
            });
        });

        // Special handler untuk form type change
        formTypeSelect.addEventListener('change', function() {
            console.log('Form type changed to:', this.value);
            showTable(this.value);
            fetchFilteredData();
        });

        // Mobile select all functionality
        if (selectAllMobile) {
            selectAllMobile.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.mobile-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectionCounter();
            });
        }

        // Update selection counter when individual checkboxes change
        document.addEventListener('change', function(e) {
            if (e.target.name === 'selected_reports[]') {
                updateSelectionCounter();

                // Update select all checkbox state
                const allCheckboxes = document.querySelectorAll('input[name="selected_reports[]"]');
                const checkedCheckboxes = document.querySelectorAll('input[name="selected_reports[]"]:checked');

                if (selectAllMobile) {
                    if (checkedCheckboxes.length === 0) {
                        selectAllMobile.indeterminate = false;
                        selectAllMobile.checked = false;
                    } else if (checkedCheckboxes.length === allCheckboxes.length) {
                        selectAllMobile.indeterminate = false;
                        selectAllMobile.checked = true;
                    } else {
                        selectAllMobile.indeterminate = true;
                    }
                }
            }
        });

        // Touch-friendly interactions
        document.addEventListener('touchstart', function() {}, {passive: true});
    });
</script>
@endsection
