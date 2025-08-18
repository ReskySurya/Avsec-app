@extends('layouts.app')

@section('title', 'Export PDF Form Daily Test')

@section('content')
<div class="bg-gray-50 lg:my-20 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Export PDF Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Export PDF Form Daily Test</h2>
            </div>

            <!-- Form Content -->
            <div class="p-6">
                <form method="GET" action="{{ route('export.dailytest') }}" id="exportForm">
                    <!-- Filter Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Jenis Form -->
                        <div>
                            <label for="form_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Form
                            </label>
                            <select name="form_type" id="form_type"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Form</option>
                                @foreach($availableEquipments as $equipment)
                                <option value="{{ $equipment['value'] }}"
                                    {{ request('form_type') == $equipment['value'] ? 'selected' : '' }}>
                                    {{ $equipment['label'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dropdown Lokasi -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi
                            </label>
                            <select name="location" id="location"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Lokasi</option>
                                @foreach($availableLocations as $location)
                                <option value="{{ $location['location_name'] }}"
                                    data-equipment="{{ $location['equipment_name'] ?? '' }}"
                                    {{ request('location') == $location['location_name'] ? 'selected' : '' }}>
                                    {{ $location['location_name'] }}
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
                                value="{{ isset($filters['start_date']) ? $filters['start_date'] : '2025-06-04' }}"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Tanggal Akhir -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Akhir
                            </label>
                            <input type="date" name="end_date" id="end_date"
                                value="{{ isset($filters['end_date']) ? $filters['end_date'] : date('Y-m-d') }}"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Filter Button -->
                    <div class="mb-6">
                        <button type="button" onclick="resetFilters()"
                            class="ml-2 inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Reset Filter
                        </button>
                    </div>

                    <!-- Data Table -->
                    <div class="overflow-hidden border border-gray-200 rounded-lg mb-6">
                        @if(count($reports) > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipe Form
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Lokasi
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pilih
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reports as $report)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report['date'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report['test_type'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report['location'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($report['status'] == 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            approved
                                        </span>
                                        @elseif($report['status'] == 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            pending
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            rejected
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="checkbox" name="selected_reports[]" value="{{ $report['id'] }}"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="p-8 text-center text-gray-500">
                            <p>Tidak ada data yang ditemukan untuk filter yang dipilih.</p>
                        </div>
                        @endif
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
    // Auto-submit saat filter berubah
    document.addEventListener('DOMContentLoaded', function() {
        const filters = ['form_type', 'location', 'start_date', 'end_date'];

        filters.forEach(id => {
            document.getElementById(id).addEventListener('change', function() {
                document.getElementById('exportForm').submit();
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const formTypeSelect = document.getElementById('form_type');
        const locationSelect = document.getElementById('location');

        // Data lokasi berdasarkan equipment dari PHP
         const locationsByEquipment = JSON.parse(`{!! json_encode($locationsByEquipment ?? []) !!}`);

        // Simpan semua opsi lokasi
        const allLocationOptions = Array.from(locationSelect.options).slice(1); // Kecuali "Semua Lokasi"

        function filterLocations() {
            const selectedEquipment = formTypeSelect.value.toLowerCase();

            // Hapus semua opsi kecuali "Semua Lokasi"
            locationSelect.innerHTML = '<option value="">Semua Lokasi</option>';

            if (selectedEquipment === '') {
                // Jika "Semua Form" dipilih, tampilkan semua lokasi
                allLocationOptions.forEach(option => {
                    locationSelect.appendChild(option.cloneNode(true));
                });
            } else {
                // Filter lokasi berdasarkan equipment yang dipilih
                const filteredLocations = locationsByEquipment[selectedEquipment] || [];

                filteredLocations.forEach(location => {
                    const option = document.createElement('option');
                    option.value = location.location_name;
                    option.textContent = location.location_name;

                    // Pertahankan seleksi jika ada
                    if (option.value === '{{ request("location") }}') {
                        option.selected = true;
                    }

                    locationSelect.appendChild(option);
                });
            }
        }

        // Event listener untuk perubahan jenis form
        formTypeSelect.addEventListener('change', filterLocations);

        // Panggil fungsi filter saat halaman dimuat untuk menjaga konsistensi
        // jika ada filter yang sudah dipilih sebelumnya
        if (formTypeSelect.value) {
            filterLocations();
        }
    });

    function resetFilters() {
        document.getElementById('form_type').value = '';
        document.getElementById('location').value = '';
        document.getElementById('start_date').value = '2025-06-04';
        document.getElementById('end_date').value = '{{ date("Y-m-d") }}';

        // Submit form to refresh data
        document.getElementById('exportForm').submit();
    }

    function exportSelected() {
        const formData = new FormData(document.getElementById('exportForm'));
        const selectedReports = formData.getAll('selected_reports[]');

        if (selectedReports.length === 0) {
            alert('Silakan pilih minimal satu data untuk export');
            return;
        }

        // Add export type
        const form = document.getElementById('exportForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'export_type';
        input.value = 'selected';
        form.appendChild(input);

        form.submit();
    }

    function exportAll() {
        // Add export type
        const form = document.getElementById('exportForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'export_type';
        input.value = 'all';
        form.appendChild(input);

        form.submit();
    }
</script>
@endsection
