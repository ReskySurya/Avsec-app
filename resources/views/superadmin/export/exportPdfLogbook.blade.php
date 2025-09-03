@extends('layouts.app')

@section('title', 'Export PDF Logbook')

@section('content')
<div class="bg-gray-50 lg:my-20 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <a href="javascript:history.back()"
            class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <!-- Export PDF Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-4">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Export PDF Logbook</h2>
            </div>

            <!-- Form Content -->
            <div class="p-6">
                <form method="GET" action="{{ route('export.logbook') }}" id="exportForm">
                    <!-- Filter Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Jenis Logbook -->
                        <div>
                            <label for="form_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Logbook
                            </label>
                            <select name="form_type" id="form_type"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="pos_jaga" {{ ($formType ?? 'pos_jaga') == 'pos_jaga' ? 'selected' : '' }}>
                                    Logbook Pos Jaga
                                </option>
                                <option value="sweeping_pi" {{ ($formType ?? '') == 'sweeping_pi' ? 'selected' : '' }}>
                                    Logbook Sweeping PI
                                </option>
                                <option value="rotasi" {{ ($formType ?? '') == 'rotasi' ? 'selected' : '' }}>
                                    Logbook Rotasi
                                </option>
                                <option value="chief" {{ ($formType ?? '') == 'chief' ? 'selected' : '' }}>
                                    Logbook Chief
                                </option>
                            </select>
                        </div>

                        <!-- Lokasi (Hidden untuk sweeping_pi dan chief) -->
                        <div id="location-filter" class="{{ in_array($formType ?? 'pos_jaga', ['sweeping_pi', 'chief']) ? 'hidden' : '' }}">
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi
                            </label>
                            <select name="location" id="location"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Tanggal Akhir -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Akhir
                            </label>
                            <input type="date" name="end_date" id="end_date" value="{{ date('Y-m-d') }}"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Tabel Logbook Pos Jaga -->
                    <div id="table-pos-jaga" class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'pos_jaga') != 'pos_jaga' ? 'hidden' : '' }}">
                        <div class="bg-blue-50 px-4 py-2 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-blue-800">Data Logbook Pos Jaga</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pengirim
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Penerima
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="pos-jaga-tbody" class="bg-white divide-y divide-gray-200">
                                    <!-- Data akan dimuat via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabel Logbook Sweeping PI -->
                    <div id="table-sweeping-pi" class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'pos_jaga') != 'sweeping_pi' ? 'hidden' : '' }}">
                        <div class="bg-green-50 px-4 py-2 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-green-800">Data Logbook Sweeping PI</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tenant
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Bulan/Tahun
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pemilik
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="sweeping-pi-tbody" class="bg-white divide-y divide-gray-200">
                                    <!-- Data akan dimuat via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabel Logbook Rotasi -->
                    <div id="table-rotasi" class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'pos_jaga') != 'rotasi' ? 'hidden' : '' }}">
                        <div class="bg-purple-50 px-4 py-2 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-purple-800">Data Logbook Rotasi</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipe/Area
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dibuat Oleh
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Disetujui Oleh
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="rotasi-tbody" class="bg-white divide-y divide-gray-200">
                                    <!-- Data akan dimuat via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabel Logbook Chief -->
                    <div id="table-chief" class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'pos_jaga') != 'chief' ? 'hidden' : '' }}">
                        <div class="bg-orange-50 px-4 py-2 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-orange-800">Data Logbook Chief</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aktivitas
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Chief
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="chief-tbody" class="bg-white divide-y divide-gray-200">
                                    <!-- Data akan dimuat via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                        <button type="button" onclick="previewData()"
                            class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Preview Data
                        </button>
                        <button type="button" onclick="exportSelected()"
                            class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Export Selected
                        </button>
                        <button type="button" onclick="exportAll()"
                            class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Export All
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal container -->
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-auto shadow-lg rounded-md bg-white" style="max-width: 850px;">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-3">
            <p class="text-2xl font-bold">Preview Laporan</p>
            <div class="cursor-pointer z-50" onclick="closeModal()">
                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 18 18">
                    <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                </svg>
            </div>
        </div>
        <!-- Modal Content -->
        <div id="modalContent" class="max-h-[85vh] overflow-y-auto">
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
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
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

    function updateTable(formType, logbooks) {
        console.log('Updating table for form type:', formType, 'with data:', logbooks);

        // Mapping yang benar untuk tbody ID
        let tbodyId = '';
        switch (formType) {
            case 'pos_jaga':
                tbodyId = 'pos-jaga-tbody';
                break;
            case 'sweeping_pi':
                tbodyId = 'sweeping-pi-tbody';
                break;
            case 'rotasi':
                tbodyId = 'rotasi-tbody';
                break;
            case 'chief':
                tbodyId = 'chief-tbody';
                break;
        }

        const tbody = document.getElementById(tbodyId);

        if (!tbody) {
            console.error('tbody not found for ID:', tbodyId);
            return;
        }

        tbody.innerHTML = '';

        if (!logbooks || logbooks.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data ditemukan</td></tr>';
            return;
        }

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
                            ${item.aktivitas || 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.lokasi || 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${item.chief || 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${createStatusBadge(item.status)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            ${createCheckbox(item.chiefID)}
                        </td>
                    `;
                    break;
            }

            tbody.appendChild(row);
        });

        console.log('Table updated successfully with', logbooks.length, 'rows');
    }

    const modal = document.getElementById('previewModal');
    const modalContent = document.getElementById('modalContent');

    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeModal() {
        modal.classList.add('hidden');
        modalContent.innerHTML = ''; // Clear content
        document.body.style.overflow = 'auto'; // Restore background scrolling
    }

    // Close modal on escape key press
    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' || event.key === 'Esc') {
            if (!modal.classList.contains('hidden')) {
                closeModal();
            }
        }
    });

    // Close modal on outside click
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    async function previewData() {
        const formData = new FormData(document.getElementById('exportForm'));
        const formType = formData.get('form_type');
        const selectedReports = formData.getAll('selected_reports[]');

        if (selectedReports.length === 0) {
            alert('Silakan pilih minimal satu data untuk preview');
            return;
        }

        if (selectedReports.length > 1) {
            alert('Hanya satu data yang bisa di-review dalam satu waktu. Silakan pilih satu saja.');
            return;
        }

        const reportId = selectedReports[0];
        // Include form_type as query parameter
        const url = `/export/logbook/review/${reportId}?form_type=${formType}`;

        try {
            // Show a loading indicator
            modalContent.innerHTML = '<p class="text-center py-20">Loading preview...</p>';
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
                // Inject the content and scale it to fit better in the modal
                modalContent.innerHTML = `<div style="transform: scale(0.9); transform-origin: center center; width: 100%;">${formContent.innerHTML}</div>`;
            } else {
                modalContent.innerHTML = html; // Fallback
            }

        } catch (error) {
            console.error('Error fetching preview:', error);
            console.log(url);
            modalContent.innerHTML = '<p class="text-center py-20 text-red-500">Gagal memuat preview. Silakan coba lagi.</p>';
        }
    }

    function exportSelected() {
        const formData = new FormData(document.getElementById('exportForm'));
        const selectedReports = formData.getAll('selected_reports[]');


        if (selectedReports.length === 0) {
            alert('Silakan pilih minimal satu data untuk export');
            return;
        }

        // Redirect ke route export dengan parameter selected
        const form = document.getElementById('exportForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'export_type';
        input.value = 'selected';
        form.appendChild(input);

        form.submit();
    }

    function exportAll() {
        // Redirect ke route export dengan parameter all
        const form = document.getElementById('exportForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'export_type';
        input.value = 'all';
        form.appendChild(input);

        form.submit();
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
                // Update tabel dengan data baru
                if (data.logbooks) {
                    updateTable(data.form_type, data.logbooks);
                } else {
                    console.error('No logbooks data in response');
                    alert('Tidak ada data yang ditemukan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat data: ' + error.message);
            });
    }

    // Auto-submit form ketika filter berubah
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

        // Event listeners untuk filter
        [location, startDate, endDate].forEach(element => {
            element.addEventListener('change', function() {
                console.log('Filter changed:', element.id, element.value);
                fetchFilteredData();
            });
        });

        // Special handler untuk form type change
        formType.addEventListener('change', function() {
            console.log('Form type changed to:', this.value);
            showTable(this.value);
            fetchFilteredData();
        });
    });
</script>
@endsection