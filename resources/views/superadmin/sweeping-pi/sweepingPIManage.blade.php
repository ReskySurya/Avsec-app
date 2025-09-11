@extends('layouts.app')

@section('title', 'Logbook Sweeping Prohibited Items')
@section('content')
<div class="container mx-auto p-3 sm:p-4 lg:p-6 max-w-full overflow-x-auto lg:mt-16">
    <!-- Header -->
    <a href="{{ route('sweepingPI.index') }}" class="inline-flex items-center px-3 sm:px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs sm:text-sm font-semibold rounded-lg shadow transition mb-4 sm:mb-6">
        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="hidden sm:inline">Kembali</span>
        <span class="sm:hidden">Back</span>
    </a>

    <!-- Header Card -->
    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 mb-4 sm:mb-6 text-white">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 sm:gap-4">
                <div class="flex-1 min-w-0">
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-bold leading-tight">
                        <span class="hidden sm:inline">Logbook Sweeping Prohibited Items</span>
                        <span class="sm:hidden">Logbook Sweeping</span>
                    </h1>
                    <p class="text-blue-100 mt-1 text-sm sm:text-base leading-snug break-words">
                        <span class="hidden sm:inline">Data Sweeping Barang Terlarang Bulanan untuk Tenant</span>
                        <span class="sm:hidden">Data Sweeping untuk</span>
                        <span class="font-semibold block sm:inline">{{ $tenant->tenant_name }}</span>
                    </p>
                </div>

                <!-- Action Button - Mobile optimized -->
                <div class="flex-shrink-0">
                    <button onclick="showAddModal()" class="w-full sm:w-auto bg-white hover:bg-blue-50 text-blue-600 px-4 sm:px-6 py-2 rounded-lg font-semibold transition-all duration-200 shadow hover:shadow-md flex items-center justify-center text-sm sm:text-base">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="hidden sm:inline">Tambah Data</span>
                        <span class="sm:hidden">Tambah</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div id="successAlert" class="bg-green-100 border border-green-400 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded mb-4 sm:mb-6 duration-200 text-sm sm:text-base">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded mb-4 sm:mb-6 duration-200 text-sm sm:text-base">
        {{ session('error') }}
    </div>
    @endif

    <!-- Data Table - Responsive -->
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl overflow-hidden mb-4 sm:mb-6">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-blue-50 border-b border-blue-200">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">No</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Tenant</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Bulan</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Tahun</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sweepingPI as $item)
                    <tr class="hover:bg-blue-50/50 transition-colors duration-150">
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                        <td class="px-4 lg:px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $tenant->tenant_name }}</td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @php
                            $bulanArray = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            echo $bulanArray[$item->bulan - 1];
                            @endphp
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->tahun }}</td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            <a href="{{ route('sweepingPI.detail.index', ['tenantID' => $tenant->tenantID,'month' => $item->bulan]) }}"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-xs font-semibold shadow-sm transition inline-block">
                                Detail
                            </a>
                            <form action="{{ route('sweepingPI.manage.destroy', $item->sweepingpiID) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs font-semibold shadow-sm transition">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-16 px-6">
                            <div class="max-w-sm mx-auto">
                                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-700 mb-1">Belum ada Logbook</h3>
                                <p class="text-gray-500 text-sm">Tambahkan data logbook baru untuk memulai.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($sweepingPI as $item)
            <div class="p-4 hover:bg-blue-50/50 transition-colors duration-150">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">{{ $loop->iteration }}</span>
                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $tenant->tenant_name }}</h3>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Bulan:</span>
                                <span class="font-medium">
                                    @php
                                    $bulanArray = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    echo $bulanArray[$item->bulan - 1];
                                    @endphp
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tahun:</span>
                                <span class="font-medium">{{ $item->tahun }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('sweepingPI.detail.index', ['tenantID' => $tenant->tenantID,'month' => $item->bulan]) }}"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-md text-xs font-semibold shadow-sm transition text-center">
                        Detail
                    </a>
                    <form action="{{ route('sweepingPI.manage.destroy', $item->sweepingpiID) }}" method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-md text-xs font-semibold shadow-sm transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-12 px-4">
                <div class="max-w-sm mx-auto">
                    <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-base font-medium text-gray-700 mb-1">Belum ada Logbook</h3>
                    <p class="text-gray-500 text-sm">Tambahkan data logbook baru untuk memulai.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Modal - Responsive -->
<div id="addModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-3 sm:px-4 py-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="hideAddModal()"></div>

        <div class="relative bg-white rounded-xl sm:rounded-2xl shadow-xl max-w-md w-full mx-auto transform transition-all">
            <div class="p-4 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800">Tambah Data Baru</h3>
                    <button onclick="hideAddModal()" class="text-gray-400 hover:text-gray-600 transition-colors md:hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('sweepingPI.manage.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ID Tenant</label>
                            <input type="text" name="tenantID" required class="w-full px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded-lg" readonly
                                value="{{ $tenant->tenantID ?? '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tenant</label>
                            <input type="text" name="tenant_name" required class="w-full px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded-lg" readonly
                                value="{{ $tenant->tenant_name  ?? '' }}">
                        </div>

                        <div>
                            <label for="tahunInput" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                            <input type="number" id="tahunInput" name="tahun" required min="2020" max="2030" value="{{ date('Y') }}"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                onchange="filterAvailableMonths()">
                        </div>

                        <div>
                            <label for="bulanSelect" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                            <select id="bulanSelect" name="bulan" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                    </div>

                    <!-- Action Buttons - Responsive -->
                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6 pt-4 border-t">
                        <button type="button" onclick="hideAddModal()"
                            class="w-full sm:w-auto px-4 py-2 text-gray-600 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors order-2 sm:order-1">
                            Batal
                        </button>
                        <button type="submit" id="submitBtn"
                            class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm transition-colors order-1 sm:order-2">
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
        // Prevent body scroll on mobile
        document.body.style.overflow = 'hidden';
    }

    function hideAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        // Reset form when closing
        document.getElementById('bulanSelect').selectedIndex = 0;
        resetMonthOptions();
        // Restore body scroll
        document.body.style.overflow = '';
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

    // Handle escape key for modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !document.getElementById('addModal').classList.contains('hidden')) {
            hideAddModal();
        }
    });
</script>
@endsection