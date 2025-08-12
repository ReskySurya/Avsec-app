@extends('layouts.app')

@section('title', 'Export PDF Logbook')

@section('content')
<div class="bg-gray-50 lg:my-20 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Export PDF Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Export PDF Logbook</h2>
            </div>

            <!-- Form Content -->
            <div class="p-6">
                <form method="GET" action="{{ route('export.logbook') }}" id="exportForm">
                    <!-- Filter Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Jenis Form -->
                        <div>
                            <label for="form_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Form
                            </label>
                            <select name="form_type" id="form_type"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-100"
                                readonly disabled>
                                <option value="LOGBOOK" selected>Logbook</option>
                            </select>
                            <input type="hidden" name="form_type" value="LOGBOOK">
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi
                            </label>
                            <select name="location" id="location"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @foreach($locations as $loc)
                                <option value="{{ $loc->name }}" {{ $loc->name == 'HBSCP' ? 'selected' : '' }}>
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

                    <!-- Data Table -->
                    <div class="overflow-hidden border border-gray-200 rounded-lg mb-6">
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
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($logbooks as $logbook)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $logbook->date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $logbook->locationArea->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($logbook->status == 'approved')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            approved
                                        </span>
                                        @elseif($logbook->status == 'pending')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            pending
                                        </span>
                                        @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            rejected
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="checkbox" name="selected_reports[]"
                                            value="{{ $logbook->logbookID }}"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

<script>
    function previewData() {
        const formData = new FormData(document.getElementById('exportForm'));
        const selectedReports = formData.getAll('selected_reports[]');

        if (selectedReports.length === 0) {
            alert('Silakan pilih minimal satu data untuk preview');
            return;
        }

        alert(`Preview data untuk ${selectedReports.length} item yang dipilih`);
        // Implementasi preview logic di sini
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

    // Auto-submit form ketika filter berubah
    document.addEventListener('DOMContentLoaded', function() {
        const formType = document.getElementById('form_type');
        const location = document.getElementById('location');
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        [formType, location, startDate, endDate].forEach(element => {
            element.addEventListener('change', function() {
                // Refresh data table berdasarkan filter
                fetchFilteredData();
            });
        });

        function fetchFilteredData() {
            const formData = new FormData(document.getElementById('exportForm'));

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
            .then(response => response.json())
            .then(data => {
                // Update tabel dengan data baru
                updateTable(data.logbooks);
            })
            .catch(error => console.error('Error:', error));
        }

        function updateTable(logbooks) {
            const tbody = document.querySelector('tbody');
            tbody.innerHTML = '';

            logbooks.forEach(logbook => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';

                // Kolom Tanggal
                const dateCell = document.createElement('td');
                dateCell.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900';
                dateCell.textContent = new Date(logbook.date).toLocaleString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                row.appendChild(dateCell);

                // Kolom Lokasi
                const locationCell = document.createElement('td');
                locationCell.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900';
                locationCell.textContent = logbook.location_area ? logbook.location_area.name : 'N/A';
                row.appendChild(locationCell);

                // Kolom Status
                const statusCell = document.createElement('td');
                statusCell.className = 'px-6 py-4 whitespace-nowrap';

                const statusSpan = document.createElement('span');
                statusSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ';

                if (logbook.status === 'approved') {
                    statusSpan.classList.add('bg-green-100', 'text-green-800');
                    statusSpan.textContent = 'approved';
                } else if (logbook.status === 'pending') {
                    statusSpan.classList.add('bg-yellow-100', 'text-yellow-800');
                    statusSpan.textContent = 'pending';
                } else {
                    statusSpan.classList.add('bg-red-100', 'text-red-800');
                    statusSpan.textContent = 'rejected';
                }

                statusCell.appendChild(statusSpan);
                row.appendChild(statusCell);

                // Kolom Aksi (checkbox)
                const actionCell = document.createElement('td');
                actionCell.className = 'px-6 py-4 whitespace-nowrap text-center';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'selected_reports[]';
                checkbox.value = logbook.logbookID;
                checkbox.className = 'h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded';

                actionCell.appendChild(checkbox);
                row.appendChild(actionCell);

                tbody.appendChild(row);
            });
        }
    });
</script>
@endsection
