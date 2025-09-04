@extends('layouts.app')

@section('title', 'Export PDF Checklist')

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
                <h2 class="text-xl font-bold text-gray-900">Export PDF Checklist</h2>
            </div>

            <!-- Form Content -->
            <div class="p-6">
                <form method="GET" action="{{ route('export.checklist') }}" id="exportForm">
                    @csrf
                    <!-- Filter Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Jenis Checklist -->
                        <div>
                            <label for="form_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Checklist
                            </label>
                            <select name="form_type" id="form_type"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="kendaraan" {{ ($formType ?? 'kendaraan') == 'kendaraan' ? 'selected' : '' }}>Checklist Kendaraan Patroli</option>
                                <option value="penyisiran" {{ ($formType ?? '') == 'penyisiran' ? 'selected' : '' }}>Checklist Penyisiran Ruang Tunggu</option>
                                <option value="senpi" {{ ($formType ?? '') == 'senpi' ? 'selected' : '' }}>Checklist Senjata Api</option>
                                <option value="pencatatan_pi" {{ ($formType ?? '') == 'pencatatan_pi' ? 'selected' : '' }}>Form Pencatatan PI</option>
                                <option value="manual_book" {{ ($formType ?? '') == 'manual_book' ? 'selected' : '' }}>Buku Pemeriksaan Manual</option>
                            </select>
                        </div>

                        <!-- Lokasi -->
                        <div id="location-filter">
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi
                            </label>
                            <select name="location" id="location"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Lokasi</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->name }}">{{ $loc->name }}</option>
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

                    <!-- Tabel Checklist Kendaraan -->
                    <div id="table-kendaraan" class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'kendaraan') != 'kendaraan' ? 'hidden' : '' }}">
                        <div class="bg-blue-50 px-4 py-2 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-blue-800">Data Checklist Kendaraan Patroli</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Polisi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="kendaraan-tbody" class="bg-white divide-y divide-gray-200">
                                    <!-- Data akan dimuat via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabel Checklist Penyisiran -->
                    <div id="table-penyisiran" class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'kendaraan') != 'penyisiran' ? 'hidden' : '' }}">
                        <div class="bg-green-50 px-4 py-2 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-green-800">Data Checklist Penyisiran Ruang Tunggu</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="penyisiran-tbody" class="bg-white divide-y divide-gray-200">
                                    <!-- Data akan dimuat via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabel Checklist Senpi -->
                    <div id="table-senpi" class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'kendaraan') != 'senpi' ? 'hidden' : '' }}">
                        <div class="bg-purple-50 px-4 py-2 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-purple-800">Data Checklist Senjata Api</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Senpi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="senpi-tbody" class="bg-white divide-y divide-gray-200">
                                    <!-- Data akan dimuat via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabel Form Pencatatan PI -->
                    <div id="table-pencatatan_pi" class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'kendaraan') != 'pencatatan_pi' ? 'hidden' : '' }}">
                        <div class="bg-orange-50 px-4 py-2 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-orange-800">Data Form Pencatatan PI</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis PI</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="pencatatan_pi-tbody" class="bg-white divide-y divide-gray-200">
                                    <!-- Data akan dimuat via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabel Buku Pemeriksaan Manual -->
                    <div id="table-manual_book" class="table-container overflow-hidden border border-gray-200 rounded-lg mb-6 {{ ($formType ?? 'kendaraan') != 'manual_book' ? 'hidden' : '' }}">
                        <div class="bg-red-50 px-4 py-2 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-red-800">Data Buku Pemeriksaan Manual</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="manual_book-tbody" class="bg-white divide-y divide-gray-200">
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

<!-- Modal Preview -->
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-auto shadow-lg rounded-md bg-white" style="max-width: 850px;">
        <div class="flex justify-between items-center pb-3">
            <p class="text-2xl font-bold">Preview Checklist</p>
            <div class="cursor-pointer z-50" onclick="closeModal()">
                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 18 18">
                    <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                </svg>
            </div>
        </div>
        <div id="modalContent" class="max-h-[85vh] overflow-y-auto">
            <!-- Preview content will be loaded here -->
        </div>
    </div>
</div>

<script>
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
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric', month: '2-digit', day: '2-digit',
                hour: '2-digit', minute: '2-digit'
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
        return `<input type="checkbox" name="selected_ids[]" value="${id}" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">`;
    }

    function updateTable(formType, checklists) {
        console.log('Updating table for form type:', formType, 'with data:', checklists);
        
        const tbody = document.getElementById(`${formType}-tbody`);
        if (!tbody) {
            console.error('tbody not found for ID:', `${formType}-tbody`);
            return;
        }

        tbody.innerHTML = '';

        if (!checklists || checklists.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data ditemukan</td></tr>';
            return;
        }

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
        const selectedIds = formData.getAll('selected_ids[]');

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
                modalContent.innerHTML = `<div style="transform: scale(0.9); transform-origin: center center; width: 100%;">${formContent.innerHTML}</div>`;
            } else {
                modalContent.innerHTML = html;
            }

        } catch (error) {
            console.error('Error fetching preview:', error);
            console.log(url);
            modalContent.innerHTML = '<p class="text-center py-20 text-red-500">Gagal memuat preview. Silakan coba lagi.</p>';
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
        if (form.querySelectorAll('input[name="selected_ids[]"]:checked').length === 0) {
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
    });
</script>
@endsection