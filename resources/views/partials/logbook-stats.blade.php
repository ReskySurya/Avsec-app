<!-- Logbook Statistics -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-1">Persentase Penyelesaian Logbook</h2>
    <p class="text-sm text-gray-500 mb-4 border-b pb-2">Statistik untuk hari ini, {{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
        @if(isset($logbookStats))
        @foreach($logbookStats as $type => $stats)
        <div @click="isModalOpen = true; modalTitle = '{{ $type }}'; modalData = {{ empty($stats['breakdown']) ? '{}' : json_encode($stats['breakdown']) }}; modalBreakdownTitle = '{{ $stats['breakdownTitle'] ?? 'Lokasi' }}'"
            class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200 cursor-pointer hover:bg-gray-100 transition-colors duration-200">
            <h3 class="text-lg font-semibold text-gray-700">{{ $type }}</h3>
            <p class="text-gray-500 text-sm mt-1">
                {{ $stats['approved'] }} dari {{ $stats['total'] }} logbook telah diajukan.
            </p>
            <div class="relative pt-1 mt-4">
                <div class="gauge-container">
                    <div class="gauge">
                        <div class="gauge-background">
                            <div class="gauge-fill" style="--percentage: {{ ($stats['percentage'] / 100) * 360 }}deg;"></div>
                        </div>
                        <div class="gauge-center">
                            <div class="gauge-percentage">{{ $stats['percentage'] }}</div>
                            <div class="gauge-label">PERCENT</div>
                        </div>
                        <div class="gauge-scale">
                            <span>0</span>
                            <span>100</span>
                        </div>
                    </div>
                </div>
                <p class="text-right text-sm font-semibold text-green-700 mt-1">{{ $stats['percentage'] }}%</p>
            </div>
        </div>
        @endforeach
        <div class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Sweeping PI</h3>
            <p class="text-gray-500 text-sm mt-1">
                Logika penyelesaian untuk logbook ini belum dapat ditentukan.
            </p>
            <div class="relative pt-1 mt-4">
                <div class="overflow-hidden h-4 text-xs flex rounded-full bg-gray-200">
                    <div style="width:0%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gray-500 transition-all duration-500"></div>
                </div>
                <p class="text-right text-sm font-semibold text-gray-700 mt-1">N/A</p>
            </div>
        </div>
        @else
        <p class="text-gray-500 col-span-4">Data statistik logbook tidak tersedia.</p>
        @endif
    </div>
</div>