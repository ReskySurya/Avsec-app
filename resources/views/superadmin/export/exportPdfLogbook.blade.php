@extends('layouts.app')

@section('title', 'Export PDF Form')

@section('content')
<div class="bg-gray-50 lg:my-20 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Export PDF Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Export PDF Form</h2>
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
                                <option value="HHMD">Form HHMD</option>
                                <option value="WTMD">Form WTMD</option>
                                <option value="XRAYCABIN">Form X-Ray Cabin</option>
                                <option value="XRAYBAGASI">Form X-Ray Bagasi</option>
                            </select>
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi
                            </label>
                            <select name="location" id="location"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="HBSCP">HBSCP</option>
                                <option value="Terminal 1">Terminal 1</option>
                                <option value="Terminal 2">Terminal 2</option>
                                <option value="Terminal 3">Terminal 3</option>
                            </select>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Mulai
                            </label>
                            <input type="date" name="start_date" id="start_date" value="2025-06-04"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Tanggal Akhir -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Akhir
                            </label>
                            <input type="date" name="end_date" id="end_date" value="2025-08-12"
                                class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="overflow-hidden border border-gray-200 rounded-lg mb-6">
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
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                $sampleData = [
                                [
                                'date' => '07/06/2025 03:05',
                                'location' => 'HBSCP',
                                'status' => 'approved',
                                'id' => 1
                                ],
                                [
                                'date' => '07/06/2025 03:05',
                                'location' => 'HBSCP',
                                'status' => 'approved',
                                'id' => 2
                                ]
                                ];
                                @endphp

                                @foreach($sampleData as $data)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $data['date'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $data['location'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($data['status'] == 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            approved
                                        </span>
                                        @elseif($data['status'] == 'pending')
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
                                        <input type="checkbox" name="selected_reports[]" value="{{ $data['id'] }}"
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

        alert(Preview data untuk ${selectedReports.length} item yang dipilih);
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
                // Implementasi AJAX call untuk update table
            });
        });
    });
</script>
@endsection
