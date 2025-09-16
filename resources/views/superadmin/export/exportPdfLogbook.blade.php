@extends('layouts.app')

@section('title', 'Export PDF Logbook')

@section('content')
<div class="bg-gray-50 py-4 px-2 sm:px-4 lg:px-8 lg:my-20">
    <div class="max-w-7xl mx-auto">
        <!-- Back Button - Mobile Optimized -->
        <a href="javascript:history.back()"
            class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition mb-4">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>

        <!-- Export PDF Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900">Export PDF Logbook</h2>
            </div>

            <!-- Form Content -->
            <div class="p-4 sm:p-6">
                <form method="GET" action="{{ route('export.logbook') }}" id="exportForm">
                    <!-- Filter Section - Mobile Optimized -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
                        <!-- Jenis Logbook -->
                        <div class="sm:col-span-2 md:col-span-1">
                            <label for="form_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Logbook
                            </label>
                            <select name="form_type" id="form_type"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="pos_jaga" {{ ($formType ?? 'pos_jaga' )=='pos_jaga' ? 'selected' : '' }}>
                                    Logbook Pos Jaga
                                </option>
                                <option value="sweeping_pi" {{ ($formType ?? '' )=='sweeping_pi' ? 'selected' : '' }}>
                                    Logbook Sweeping PI
                                </option>
                                <option value="rotasi" {{ ($formType ?? '' )=='rotasi' ? 'selected' : '' }}>
                                    Logbook Rotasi
                                </option>
                                <option value="chief" {{ ($formType ?? '' )=='chief' ? 'selected' : '' }}>
                                    Logbook Chief
                                </option>
                            </select>
                        </div>

                        <!-- Lokasi (Hidden untuk sweeping_pi dan chief) -->
                        <div id="location-filter"
                            class="sm:col-span-2 md:col-span-1 {{ in_array($formType ?? 'pos_jaga', ['sweeping_pi', 'chief']) ? 'hidden' : '' }}">
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi
                            </label>
                            <select name="location" id="location"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Semua Lokasi</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->name }}">
                                    {{ $loc->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Mulai
                            </label>
                            <input type="date" name="start_date" id="start_date"
                                value="{{ date('Y-m-d', strtotime('-1 month')) }}"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        <!-- Tanggal Akhir -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Akhir
                            </label>
                            <input type="date" name="end_date" id="end_date" value="{{ date('Y-m-d') }}"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    <!-- Mobile-Optimized Tables -->
                    <!-- Tabel Logbook Pos Jaga -->
                    <div id="table-pos-jaga"
                        class="table-container mb-6 {{ ($formType ?? 'pos_jaga') != 'pos_jaga' ? 'hidden' : '' }}">
                        <div class="bg-blue-50 px-4 py-3 rounded-t-lg border-b border-gray-200">
                            <h3 class="text-base sm:text-lg font-semibold text-blue-800">Data Logbook Pos Jaga</h3>
                        </div>

                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-hidden border border-gray-200 rounded-b-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Lokasi
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Pengirim
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Penerima
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Pilih
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="pos-jaga-tbody" class="bg-white divide-y divide-gray-200">
                                        <!-- Data akan dimuat via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Cards -->
                        <div id="pos-jaga-mobile" class="md:hidden bg-white border border-gray-200 rounded-b-lg">
                            <!-- Mobile cards will be populated here -->
                        </div>
                    </div>

                    <!-- Tabel Logbook Sweeping PI -->
                    <div id="table-sweeping-pi"
                        class="table-container mb-6 {{ ($formType ?? 'pos_jaga') != 'sweeping_pi' ? 'hidden' : '' }}">
                        <div class="bg-green-50 px-4 py-3 rounded-t-lg border-b border-gray-200">
                            <h3 class="text-base sm:text-lg font-semibold text-green-800">Data Logbook Sweeping PI</h3>
                        </div>

                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-hidden border border-gray-200 rounded-b-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tenant
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Bulan/Tahun
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Pemilik
                                            </th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Pilih
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="sweeping-pi-tbody" class="bg-white divide-y divide-gray-200">
                                        <!-- Data akan dimuat via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Cards -->
                        <div id="sweeping-pi-mobile" class="md:hidden bg-white border border-gray-200 rounded-b-lg">
                            <!-- Mobile cards will be populated here -->
                        </div>
                    </div>

                    <!-- Tabel Logbook Rotasi -->
                    <div id="table-rotasi"
                        class="table-container mb-6 {{ ($formType ?? 'pos_jaga') != 'rotasi' ? 'hidden' : '' }}">
                        <div class="bg-purple-50 px-4 py-3 rounded-t-lg border-b border-gray-200">
                            <h3 class="text-base sm:text-lg font-semibold text-purple-800">Data Logbook Rotasi</h3>
                        </div>

                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-hidden border border-gray-200 rounded-b-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tipe/Area
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dibuat Oleh
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Disetujui Oleh
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Pilih
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="rotasi-tbody" class="bg-white divide-y divide-gray-200">
                                        <!-- Data akan dimuat via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Cards -->
                        <div id="rotasi-mobile" class="md:hidden bg-white border border-gray-200 rounded-b-lg">
                            <!-- Mobile cards will be populated here -->
                        </div>
                    </div>

                    <!-- Tabel Logbook Chief -->
                    <div id="table-chief"
                        class="table-container mb-6 {{ ($formType ?? 'pos_jaga') != 'chief' ? 'hidden' : '' }}">
                        <div class="bg-orange-50 px-4 py-3 rounded-t-lg border-b border-gray-200">
                            <h3 class="text-base sm:text-lg font-semibold text-orange-800">Data Logbook Chief</h3>
                        </div>

                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-hidden border border-gray-200 rounded-b-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Grup
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Shift
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Team Leader
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Pilih
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="chief-tbody" class="bg-white divide-y divide-gray-200">
                                        <!-- Data akan dimuat via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Cards -->
                        <div id="chief-mobile" class="md:hidden bg-white border border-gray-200 rounded-b-lg">
                            <!-- Mobile cards will be populated here -->
                        </div>
                    </div>

                    <!-- Action Buttons - Mobile Optimized -->
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:justify-end">
                        <button type="button" onclick="previewData()"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-4 h-4 mr-2 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span class="sm:hidden">Preview Data</span>
                            <span class="hidden sm:inline">Preview</span>
                        </button>
                        <button type="button" onclick="exportSelected()"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            <svg class="w-4 h-4 mr-2 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Terpilih
                        </button>
                        <button type="button" onclick="exportAll()"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                            <svg class="w-4 h-4 mr-2 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Semua
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mobile-Optimized Modal -->
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-2 sm:mx-auto sm:top-10 p-4 sm:p-5 border w-full sm:w-11/12 md:w-3/4 lg:w-auto shadow-lg rounded-md bg-white"
        style="max-width: 850px;">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-3 border-b">
            <p class="text-xl sm:text-2xl font-bold">Preview Laporan</p>
            <button type="button" class="cursor-pointer p-2 hover:bg-gray-100 rounded-full" onclick="closeModal()">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <!-- Modal Content -->
        <div id="modalContent" class="max-h-[80vh] sm:max-h-[85vh] overflow-y-auto mt-4">
            <!-- Preview content will be loaded here -->
        </div>
    </div>
</div>

<script>
    function showTable(formType) {
        console.log('Showing table for form type:', formType);

        // Hide semua tabel
        const tables = document.querySelectorAll('.table-container');
        tables.forEach(table => table.classList.add('hidden'));

        // Show tabel yang sesuai dengan mapping yang benar
        let targetTableId = '';
        switch (formType) {
            case 'pos_jaga':
                targetTableId = 'table-pos-jaga';
                break;
            case 'sweeping_pi':
                targetTableId = 'table-sweeping-pi';
                break;
            case 'rotasi':
                targetTableId = 'table-rotasi';
                break;
            case 'chief':
                targetTableId = 'table-chief';
                break;
        }

        const targetTable = document.getElementById(targetTableId);
        if (targetTable) {
            targetTable.classList.remove('hidden');
            console.log('Table shown:', targetTableId);
        } else {
            console.error('Table not found:', targetTableId);
        }

        // Update filter lokasi visibility
        const locationFilter = document.getElementById('location-filter');
        if (formType === 'sweeping_pi' || formType === 'chief') {
            locationFilter.classList.add('hidden');
        } else {
            locationFilter.classList.remove('hidden');
        }
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    function createStatusBadge(status) {
        let badgeClass = '';
        let statusText = '';

        switch (status) {
            case 'approved':
                badgeClass = 'bg-green-100 text-green-800';
                statusText = 'Disetujui';
                break;
            case 'submitted':
                badgeClass = 'bg-yellow-100 text-yellow-800';
                statusText = 'Pending';
                break;
            case 'draft':
                badgeClass = 'bg-blue-100 text-blue-800';
                statusText = 'Draft';
                break;
            default:
                badgeClass = 'bg-red-100 text-red-800';
                statusText = 'Ditolak';
        }

        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${badgeClass}">${statusText}</span>`;
    }

    function createCheckbox(id) {
        return `<input type="checkbox" name="selected_reports[]" value="${id}" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">`;
    }

    function createMobileCard(item, formType) {
        let content = '';
        let id = '';

        switch (formType) {
            case 'pos_jaga':
                id = item.logbookID;
                content = `
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 mb-1">${formatDate(item.date)}</div>
                            <div class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Lokasi:</span> ${item.location_area?.name || 'N/A'}
                            </div>
                            <div class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Pengirim:</span> ${item.sender_by?.name || 'N/A'}
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Penerima:</span> ${item.receiver_by?.name || 'N/A'}
                            </div>
                            ${createStatusBadge(item.status)}
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            ${createCheckbox(id)}
                        </div>
                    </div>
                `;
                break;

            case 'sweeping_pi':
                id = item.sweepingpiID;
                content = `
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 mb-1">${formatDate(item.created_at)}</div>
                            <div class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Tenant:</span> ${item.tenant?.tenant_name || 'N/A'}
                            </div>
                            <div class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Periode:</span> Bulan ${item.bulan}/${item.tahun}
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Pemilik:</span> ${item.tenant?.supervisorName || 'N/A'}
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            ${createCheckbox(id)}
                        </div>
                    </div>
                `;
                break;

            case 'rotasi':
                id = item.id;
                content = `
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 mb-1">${formatDate(item.date)}</div>
                            <div class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Tipe/Area:</span> ${item.type ? 'Area ' + item.type : 'N/A'}
                            </div>
                            <div class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Dibuat Oleh:</span> ${item.creator?.name || 'N/A'}
                            </div>
                            <div class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Disetujui:</span> ${item.approver?.name || 'Belum disetujui'}
                            </div>
                            <div class="mb-2">${createStatusBadge(item.status)}</div>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            ${createCheckbox(id)}
                        </div>
                    </div>
                `;
                break;

            case 'chief':
                id = item.logbookID;
                content = `
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 mb-1">${formatDate(item.date)}</div>
                            <div class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Grup:</span> ${item.grup || 'N/A'}
                            </div>
                            <div class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Shift:</span> ${item.shift || 'N/A'}
                            </div>
                            <div class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Team Leader:</span> ${item.senderName || 'N/A'}
                            </div>
                            <div class="mb-2">${createStatusBadge(item.status)}</div>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            ${createCheckbox(id)}
                        </div>
                    </div>
                `;
                break;
        }

        return `
            <div class="p-4 border-b border-gray-200 last:border-b-0 hover:bg-gray-50">
                ${content}
            </div>
        `;
    }

    function updateTable(formType, logbooks) {
        console.log('Updating table for form type:', formType, 'with data:', logbooks);

        // Mapping untuk tbody dan mobile container ID
        let tbodyId = '';
        let mobileId = '';

        switch (formType) {
            case 'pos_jaga':
                tbodyId = 'pos-jaga-tbody';
                mobileId = 'pos-jaga-mobile';
                break;
            case 'sweeping_pi':
                tbodyId = 'sweeping-pi-tbody';
                mobileId = 'sweeping-pi-mobile';
                break;
            case 'rotasi':
                tbodyId = 'rotasi-tbody';
                mobileId = 'rotasi-mobile';
                break;
            case 'chief':
                tbodyId = 'chief-tbody';
                mobileId = 'chief-mobile';
                break;
        }

        const tbody = document.getElementById(tbodyId);
        const mobileContainer = document.getElementById(mobileId);

        if (!tbody || !mobileContainer) {
            console.error('Container not found for ID:', tbodyId, 'or', mobileId);
            return;
        }

        tbody.innerHTML = '';
        mobileContainer.innerHTML = '';

        if (!logbooks || logbooks.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data ditemukan</td></tr>';
            mobileContainer.innerHTML = '<div class="p-4 text-center text-gray-500">Tidak ada data ditemukan</div>';
            return;
        }

        // Generate desktop table rows
        logbooks.forEach((item, index) => {
            console.log(`Processing item ${index}:`, item);

            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';

            switch (formType) {
                case 'pos_jaga':
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${formatDate(item.date)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.location_area?.name || 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.sender_by?.name || 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.receiver_by?.name || 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${createStatusBadge(item.status)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            ${createCheckbox(item.logbookID)}
                        </td>
                    `;
                    break;

                case 'sweeping_pi':
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${formatDate(item.created_at)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.tenant?.tenant_name || 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Bulan ${item.bulan}/${item.tahun}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.tenant?.supervisorName || 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            ${createCheckbox(item.sweepingpiID)}
                        </td>
                    `;
                    break;

                case 'rotasi':
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${formatDate(item.date)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.type ? 'Area ' + item.type : 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.creator?.name || 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.approver?.name || 'Belum disetujui'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${createStatusBadge(item.status)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            ${createCheckbox(item.id)}
                        </td>
                    `;
                    break;

                case 'chief':
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${formatDate(item.date)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.grup || 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.shift || 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.senderName || 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${createStatusBadge(item.status)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            ${createCheckbox(item.logbookID)}
                        </td>
                    `;
                    break;
            }

            tbody.appendChild(row);
        });

        // Generate mobile cards
        const mobileCardsHtml = logbooks.map(item => createMobileCard(item, formType)).join('');
        mobileContainer.innerHTML = mobileCardsHtml;

        console.log('Table updated successfully with', logbooks.length, 'rows');
    }

    // Modal functions - Mobile optimized
    const modal = document.getElementById('previewModal');
    const modalContent = document.getElementById('modalContent');

    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Add touch event listener for mobile
        if ('ontouchstart' in window) {
            modal.addEventListener('touchmove', function(e) {
                if (e.target === modal) {
                    e.preventDefault();
                }
            });
        }
    }

    function closeModal() {
        modal.classList.add('hidden');
        modalContent.innerHTML = '';
        document.body.style.overflow = 'auto';
    }

    // Enhanced event listeners for mobile
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

    // Touch event for mobile modal closing
    if ('ontouchstart' in window) {
        let touchStartY = 0;
        modal.addEventListener('touchstart', function(event) {
            touchStartY = event.touches[0].clientY;
        });

        modal.addEventListener('touchend', function(event) {
            const touchEndY = event.changedTouches[0].clientY;
            if (event.target === modal && Math.abs(touchEndY - touchStartY) < 10) {
                closeModal();
            }
        });
    }

    async function previewData() {
        const formData = new FormData(document.getElementById('exportForm'));
        const formType = formData.get('form_type');
        const selectedReports = formData.getAll('selected_reports[]');

        if (selectedReports.length === 0) {
            showAlert('Silakan pilih minimal satu data untuk preview', 'warning');
            return;
        }

        if (selectedReports.length > 1) {
            showAlert('Hanya satu data yang bisa di-review dalam satu waktu. Silakan pilih satu saja.', 'warning');
            return;
        }

        const reportId = selectedReports[0];
        const url = `/export/logbook/review/${reportId}?form_type=${formType}`;

        try {
            modalContent.innerHTML = '<div class="flex items-center justify-center py-20"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600">Loading preview...</span></div>';
            openModal();

            console.log('Fetching preview from URL:', url);
            const response = await fetch(url);

            console.log('Received response:', response);
            console.log('Response Status:', response.status);
            console.log('Response OK:', response.ok);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const html = await response.text();
            console.log('Received HTML content length:', html.length);

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const formContent = doc.querySelector('.page-break-after');

            if (formContent) {
                console.log('Element .page-break-after FOUND. Injecting its innerHTML.');
                modalContent.innerHTML = `<div class="transform scale-75 sm:scale-90 origin-top-left" style="width: 133%; transform-origin: top left;">${formContent.innerHTML}</div>`;
            } else {
                console.log('Element .page-break-after NOT FOUND. Injecting full HTML as fallback.');
                modalContent.innerHTML = `<div class="transform scale-75 sm:scale-90 origin-top-left" style="width: 133%;">${html}</div>`;
            }

        } catch (error) {
            console.error('Error fetching preview:', error);
            modalContent.innerHTML = `<div class="text-center py-20"><div class="text-red-500 mb-2"><svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Gagal memuat preview</div><p class="text-gray-600 text-sm">Error: ${error.message}</p></div>`;
        }
    }

    function exportSelected() {
        const formData = new FormData(document.getElementById('exportForm'));
        const selectedReports = formData.getAll('selected_reports[]');

        if (selectedReports.length === 0) {
            showAlert('Silakan pilih minimal satu data untuk export', 'warning');
            return;
        }

        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Export...';
        button.disabled = true;

        const form = document.getElementById('exportForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'export_type';
        input.value = 'selected';
        form.appendChild(input);

        form.submit();

        // Reset button after a delay (in case submission fails)
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 5000);
    }

    function exportAll() {
        // Show confirmation for mobile users
        if (window.innerWidth < 640) {
            if (!confirm('Export semua data? Proses ini mungkin memakan waktu lama.')) {
                return;
            }
        }

        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Export...';
        button.disabled = true;

        const form = document.getElementById('exportForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'export_type';
        input.value = 'all';
        form.appendChild(input);

        form.submit();

        // Reset button after a delay
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 5000);
    }

    // Mobile-friendly alert function
    function showAlert(message, type = 'info') {
        // Create alert element
        const alertDiv = document.createElement('div');
        const bgColor = type === 'warning' ? 'bg-yellow-100 border-yellow-400 text-yellow-700' : 'bg-blue-100 border-blue-400 text-blue-700';

        alertDiv.className = `fixed top-4 left-4 right-4 sm:left-auto sm:right-4 sm:max-w-sm z-50 ${bgColor} border px-4 py-3 rounded shadow-lg transition-all duration-300 transform translate-y-0`;
        alertDiv.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium">${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    function fetchFilteredData() {
        const formData = new FormData(document.getElementById('exportForm'));

        console.log('Fetching filtered data with params:', {
            form_type: formData.get('form_type'),
            location: formData.get('location'),
            start_date: formData.get('start_date'),
            end_date: formData.get('end_date')
        });

        fetch('{{ route("export.logbook.filter") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.logbooks) {
                    updateTable(data.form_type, data.logbooks);
                } else {
                    console.error('No logbooks data in response');
                    showAlert('Tidak ada data yang ditemukan', 'warning');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan saat memuat data: ' + error.message, 'warning');
            });
    }

    // Enhanced initialization for mobile
    document.addEventListener('DOMContentLoaded', function() {
        const formType = document.getElementById('form_type');
        const location = document.getElementById('location');
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        console.log('DOM loaded, initializing...');

        // Show initial table
        showTable(formType.value);

        // Load initial data
        fetchFilteredData();

        // Event listeners untuk filter dengan debouncing untuk mobile
        let timeout;
        [location, startDate, endDate].forEach(element => {
            element.addEventListener('change', function() {
                console.log('Filter changed:', element.id, element.value);

                // Clear previous timeout
                clearTimeout(timeout);

                // Add loading indicator for mobile
                if (window.innerWidth < 640) {
                    const container = element.parentNode;
                    container.classList.add('opacity-50');
                }

                // Debounce the API call
                timeout = setTimeout(() => {
                    fetchFilteredData();

                    // Remove loading indicator
                    if (window.innerWidth < 640) {
                        const container = element.parentNode;
                        container.classList.remove('opacity-50');
                    }
                }, 300);
            });
        });

        // Special handler untuk form type change
        formType.addEventListener('change', function() {
            console.log('Form type changed to:', this.value);
            showTable(this.value);
            fetchFilteredData();
        });

        // Add touch feedback for mobile buttons
        if ('ontouchstart' in window) {
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('touchstart', function() {
                    this.classList.add('opacity-75');
                });
                button.addEventListener('touchend', function() {
                    this.classList.remove('opacity-75');
                });
            });
        }

        // Handle orientation change for mobile
        window.addEventListener('orientationchange', function() {
            setTimeout(() => {
                // Recalculate modal positioning if open
                if (!modal.classList.contains('hidden')) {
                    const modalDialog = modal.querySelector('.relative');
                    if (modalDialog) {
                        modalDialog.style.maxHeight = window.innerHeight - 40 + 'px';
                    }
                }
            }, 100);
        });
    });

    // Performance optimization for mobile scrolling
    let ticking = false;
    function optimizedResize() {
        if (!ticking) {
            requestAnimationFrame(function() {
                // Handle any resize operations here
                ticking = false;
            });
            ticking = true;
        }
    }
    window.addEventListener('resize', optimizedResize);

</script>
@endsection
