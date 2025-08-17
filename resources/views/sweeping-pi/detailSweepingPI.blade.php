@extends('layouts.app')

@section('title', 'Detail Logbook Sweeping Prohibited Items')
@section('content')

<div class="max-w-6xl mx-auto lg:mt-20 mt-5 p-4">
    {{-- Back Button --}}
    <a href="{{ route('sweepingPI.manage.index',$tenant->tenantID) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition mb-6">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Daftar
    </a>

    {{-- Header Section --}}
    <div class="bg-white p-6 shadow-md border rounded-lg">
        <div class="flex justify-between items-start mb-6">
            <div>
                <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo" class="h-10 mb-2">
                <div class="text-xs text-gray-500 grid grid-cols-3 gap-4">
                    <div class="pr-4">
                        <span class="block text-gray-600">TAHUN</span>
                        <span class="block text-gray-600">BULAN</span>
                        <span class="block text-gray-600">NAMA TENANT</span>
                    </div>
                    <div class="pr-4">
                        <span class="block font-semibold text-gray-800">: {{ $sweepingPI->first()->tahun }}</span>
                        <span class="block font-semibold text-gray-800">: {{ [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember'
                        ][$sweepingPI->first()->bulan] }}</span>
                        <span class="block font-semibold text-gray-800">: {{$tenant->tenant_name}}</span>
                    </div>
                </div>
            </div>
            <div>
                <img src="{{ asset('images/Injourney-API.png') }}" alt="Logo Yogyakarta Airport" class="h-12">
            </div>
        </div>

        {{-- Title --}}
        <div class="text-center mb-6">
            <h1 class="text-xl font-bold mb-2">Checklist Prohibited Items</h1>
            <p class="text-sm text-blue-600 font-semibold">Tenant: {{ $tenant->tenant_name ?? 'MAKE SENTOSA' }}</p>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-blue-600" id="completionRate">0%</div>
                <div class="text-sm text-gray-600">Completion Rate</div>
            </div>
            <div class="bg-green-50 border border-green-200 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-green-600" id="totalChecked">0</div>
                <div class="text-sm text-gray-600">Total Checked</div>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-yellow-600" id="totalPending">0</div>
                <div class="text-sm text-gray-600">Pending</div>
            </div>
            <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-red-600" id="totalMissed">{{ $totalMissed ?? 150 }}</div>
                <div class="text-sm text-gray-600">Missed</div>
            </div>
        </div>

        {{-- Month Header --}}
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 text-center rounded-t-lg">
            <h2 class="text-lg font-bold">{{ $month ?? 'Juni 2025' }}</h2>
        </div>

        {{-- Checklist Table --}}
        <div class="overflow-x-auto border border-gray-300 rounded-b-lg">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold min-w-[200px]">
                            PROHIBITED ITEMS
                        </th>
                        @for ($day = 1; $day <= ($daysInMonth ?? 31); $day++)
                            <th class="border border-gray-300 px-2 py-3 text-center font-semibold min-w-[40px]">
                                {{ $day }}
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach ($prohibitedItems as $itemKey => $item)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="border border-gray-300 px-4 py-3 font-semibold">
                                {{ $item->items_name }}
                            </td>
                            @for ($day = 1; $day <= ($daysInMonth ?? 31); $day++)
                                <td class="border border-gray-300 px-2 py-3 text-center">
                                    <input 
                                        type="checkbox" 
                                        readonly
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" 
                                        data-item="{{ $itemKey }}" 
                                        data-day="{{ $day }}" 
                                        onchange="updateStats()"
                                        {{ isset($checkedItems[$itemKey][$day]) && $checkedItems[$itemKey][$day] ? 'checked' : '' }}
                                    >
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>

                <p>notes belum</p>
            </table>
        </div>

        {{-- Action Buttons --}}
        <!-- <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-end">
            <button type="button" onclick="saveChecklist()" 
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                Simpan Checklist
            </button>
            <button type="button" onclick="printChecklist()" 
                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                Print Checklist
            </button>
            <button type="button" onclick="resetChecklist()" 
                class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition">
                Reset
            </button>
        </div> -->
    </div>
</div>

{{-- JavaScript for functionality --}}
<script>
    // function updateStats() {
    //     const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    //     const totalBoxes = checkboxes.length;
    //     const checkedBoxes = document.querySelectorAll('input[type="checkbox"]:checked').length;
        
    //     const completionRate = totalBoxes > 0 ? Math.round((checkedBoxes / totalBoxes) * 100) : 0;
    //     const totalPending = totalBoxes - checkedBoxes;
        
    //     document.getElementById('completionRate').textContent = completionRate + '%';
    //     document.getElementById('totalChecked').textContent = checkedBoxes;
    //     document.getElementById('totalPending').textContent = totalPending;
    // }

    // function saveChecklist() {
    //     const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
    //     const data = {};
        
    //     checkboxes.forEach(checkbox => {
    //         const item = checkbox.dataset.item;
    //         const day = checkbox.dataset.day;
            
    //         if (!data[item]) {
    //             data[item] = [];
    //         }
    //         data[item].push(day);
    //     });

    //     // AJAX call to save data
    //     fetch('/api/save-checklist', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //         },
    //         body: JSON.stringify({
    //             checklist_data: data,
    //             tenantID: {{ $tenantID ?? 'null' }},
    //             month: '{{ $month ?? "Juni 2025" }}'
    //         })
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.success) {
    //             alert('Checklist berhasil disimpan!');
    //         } else {
    //             alert('Gagal menyimpan checklist.');
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Error:', error);
    //         alert('Terjadi kesalahan saat menyimpan.');
    //     });
    // }

    // function printChecklist() {
    //     window.print();
    // }

    // function resetChecklist() {
    //     if (confirm('Apakah Anda yakin ingin mereset semua checklist?')) {
    //         document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    //             checkbox.checked = false;
    //         });
    //         updateStats();
    //     }
    // }

    // // Initialize stats on page load
    // document.addEventListener('DOMContentLoaded', function() {
    //     updateStats();
    // });
</script>

{{-- Print Styles --}}
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            font-size: 12px;
        }
        
        .max-w-6xl {
            max-width: none;
        }
        
        table {
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
</style>

@endsection