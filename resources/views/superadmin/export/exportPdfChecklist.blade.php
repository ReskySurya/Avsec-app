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

                    <!-- Data Table -->
                    <div class="overflow-hidden border border-gray-200 rounded-lg mb-6">
                        <table id="checklist-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <!-- Headers will be dynamically inserted by JS -->
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Data will be loaded via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                        <button type="button" onclick="fetchFilteredData()"
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

<script>
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
        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${className}">${statusText}</span>`;
    }

    function getCheckbox(id) {
        return `<input type="checkbox" name="selected_ids[]" value="${id}" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">`;
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

    function updateTable(checklists, formType) {
        const table = document.getElementById('checklist-table');
        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');
        
        thead.innerHTML = '';
        tbody.innerHTML = '';

        let headers = [];
        const headerConfig = {
            kendaraan: ['Tanggal', 'No. Polisi', 'Petugas', 'Status', 'Aksi'],
            penyisiran: ['Tanggal', 'Lokasi', 'Petugas', 'Status', 'Aksi'],
            senpi: ['Tanggal', 'No. Senpi', 'Petugas', 'Status', 'Aksi'],
            pencatatan_pi: ['Tanggal', 'Petugas', 'Jenis PI', 'Lokasi', 'Status', 'Aksi'],
            manual_book: ['Tanggal', 'Petugas', 'Lokasi', 'Status', 'Aksi']
        };

        headers = headerConfig[formType] || [];

        const headerRow = document.createElement('tr');
        headers.forEach(headerText => {
            const th = document.createElement('th');
            th.className = 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider';
            if (headerText === 'Aksi') th.classList.add('text-center');
            th.textContent = headerText;
            headerRow.appendChild(th);
        });
        thead.appendChild(headerRow);

        if (!checklists || checklists.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${headers.length}" class="text-center p-8 text-gray-500">Tidak ada data yang ditemukan.</td></tr>`;
            return;
        }

        checklists.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            let cells = '';
            const id = item.id; // Assuming a generic 'id' field

            switch (formType) {
                case 'kendaraan':
                    cells = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(item.date)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.nomor_polisi || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.petugas?.name || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${getStatusBadge(item.status)}</td>
                    `;
                    break;
                case 'penyisiran':
                    cells = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(item.date)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.lokasi?.name || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.petugas?.name || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${getStatusBadge(item.status)}</td>
                    `;
                    break;
                case 'senpi':
                    cells = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(item.date)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.nomor_senpi || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.petugas?.name || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${getStatusBadge(item.status)}</td>
                    `;
                    break;
                case 'pencatatan_pi':
                    cells = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(item.date)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.petugas?.name || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.jenis_pi || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.lokasi?.name || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${getStatusBadge(item.status)}</td>
                    `;
                    break;
                case 'manual_book':
                    cells = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(item.date)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.petugas?.name || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.lokasi?.name || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${getStatusBadge(item.status)}</td>
                    `;
                    break;
            }
            
            cells += `<td class="px-6 py-4 whitespace-nowrap text-center">${getCheckbox(id)}</td>`;
            row.innerHTML = cells;
            tbody.appendChild(row);
        });
    }

    function fetchFilteredData() {
        const formData = new FormData(document.getElementById('exportForm'));
        const tbody = document.querySelector('#checklist-table tbody');
        const headers = document.querySelector('#checklist-table thead tr');
        const colspan = headers ? headers.children.length : 5;

        tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center p-8 text-gray-500">Loading...</td></tr>`;
        
        fetch('{{ route("export.checklist.filter") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData.entries()))
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            updateTable(data.checklists, data.form_type);
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center p-8 text-red-500">Error loading data. Please try again.</td></tr>`;
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

        function setupEventListeners() {
            formTypeSelect.addEventListener('change', fetchFilteredData);
            locationSelect.addEventListener('change', fetchFilteredData);
            startDate.addEventListener('change', fetchFilteredData);
            endDate.addEventListener('change', fetchFilteredData);
        }

        // Initial data load
        updateTable(@json($checklists), @json($formType));
        fetchFilteredData();
        setupEventListeners();
    });
</script>
@endsection
