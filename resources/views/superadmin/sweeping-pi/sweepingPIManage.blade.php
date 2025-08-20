@extends('layouts.app')

@section('title', 'Logbook Sweeping Prohibited Items')
@section('content')
<div class="container mx-auto p-4 max-w-full overflow-x-auto mt-20">
    <!-- Header -->
    <a href="{{ route('sweepingPI.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition mb-6">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali
    </a>
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">

            <div>
                <div class="flex items-center mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Logbook Sweeping Prohibited Items</h1>
                        <p class="text-gray-600">Data Sweeping Barang Terlarang Bulanan</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Action Buttons -->
                <button onclick="showAddModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Data
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div id="successAlert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 duration-200">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 duration-200">
        {{ session('error') }}
    </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-200 border-b border-gray-300">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sweepingPI as $index => $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tenant->tenant_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @php
                            $bulanArray = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            echo $bulanArray[$item->bulan - 1];
                            @endphp
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->tahun }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('sweepingPI.detail.index', ['tenantID' => $tenant->tenantID,'month' => $item->bulan]) }}"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                                Detail
                            </a>
                            <form action="{{ route('sweepingPI.manage.destroy', $item->sweepingpiID) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded ml-2">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="hideAddModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Tambah Data Baru</h3>

                <form action="{{ route('sweepingPI.manage.store') }}" method="POST">
                    @csrf
                    <!-- Tenant Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ID Tenant</label>
                        <input type="text" name="tenantID" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" readonly
                            placeholder="Masukkan ID Tenant" value="{{ $tenant->tenantID ?? '' }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Tenant</label>
                        <input type="text" name="tenant_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" readonly
                            placeholder="Masukkan Nama Tenant" value="{{ $tenant->tenant_name  ?? '' }}">
                    </div>

                    <!-- Year Input - Moved before Month for better UX -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <input type="number" id="tahunInput" name="tahun" required min="2020" max="2030" value="{{ date('Y') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            onchange="filterAvailableMonths()">
                    </div>

                    <!-- Month Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select id="bulanSelect" name="bulan" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Bulan</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                        <p id="noAvailableMonths" class="text-sm text-red-600 mt-2 hidden">Semua bulan untuk tahun ini sudah memiliki data.</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideAddModal()"
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" id="submitBtn"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    if (document.getElementById('successAlert')) {
        setTimeout(function() {
            document.getElementById('successAlert').style.display = 'none';
        }, 3000);
    }
    if (document.getElementById('errorAlert')) {
        setTimeout(function() {
            document.getElementById('errorAlert').style.display = 'none';
        }, 3000);
    }

    // Data bulan yang sudah ada untuk setiap tahun
    const existingData = JSON.parse(`{!! json_encode(
        $sweepingPI->groupBy('tahun')->map(function($items) {
            return $items->pluck('bulan')->toArray();
        })
    ) !!}`);

    function showAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        // Filter months when modal opens
        filterAvailableMonths();
    }

    function hideAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        // Reset form when closing
        document.getElementById('bulanSelect').selectedIndex = 0;
        resetMonthOptions();
    }

    function filterAvailableMonths() {
        const tahun = document.getElementById('tahunInput').value;
        const bulanSelect = document.getElementById('bulanSelect');
        const noAvailableMonths = document.getElementById('noAvailableMonths');
        const submitBtn = document.getElementById('submitBtn');

        // Reset all options to visible first
        resetMonthOptions();

        if (tahun && existingData[tahun]) {
            const existingMonths = existingData[tahun];
            const options = bulanSelect.querySelectorAll('option');
            let availableCount = 0;

            // Hide options for months that already have data
            options.forEach(option => {
                if (option.value && existingMonths.includes(parseInt(option.value))) {
                    option.style.display = 'none';
                } else if (option.value) {
                    availableCount++;
                }
            });

            // Show/hide warning message and disable submit button if no months available
            if (availableCount === 0) {
                noAvailableMonths.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                bulanSelect.disabled = true;
            } else {
                noAvailableMonths.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                bulanSelect.disabled = false;
            }
        } else {
            // If no existing data for this year, show all months
            noAvailableMonths.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            bulanSelect.disabled = false;
        }

        // Reset selection if current selection is now hidden
        const currentSelection = bulanSelect.value;
        if (currentSelection && tahun && existingData[tahun] && existingData[tahun].includes(parseInt(currentSelection))) {
            bulanSelect.selectedIndex = 0;
        }
    }

    function resetMonthOptions() {
        const bulanSelect = document.getElementById('bulanSelect');
        const options = bulanSelect.querySelectorAll('option');

        options.forEach(option => {
            option.style.display = '';
        });

        bulanSelect.disabled = false;
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

        document.getElementById('noAvailableMonths').classList.add('hidden');
    }
</script>
@endsection