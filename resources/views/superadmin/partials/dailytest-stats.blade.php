<!-- Daily Test Statistics -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-1">Persentase Penyelesaian Daily Test</h2>
    <p class="text-sm text-gray-500 mb-4 border-b pb-2">Statistik untuk hari ini, {{ now()->translatedFormat('l, d F Y') }}</p>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
        @if(isset($dailyTestStats))
        @foreach($dailyTestStats as $type => $stats)
        <div @click="isModalOpen = true; modalTitle = '{{ $type }}'; modalData = {{ json_encode($stats['breakdown']) }}; modalBreakdownTitle = '{{ $stats['breakdownTitle'] }}'"
            class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200 cursor-pointer hover:bg-gray-100 transition-colors duration-200">
            <h3 class="text-lg font-semibold text-gray-700">{{ $type }}</h3>
            <p class="text-gray-500 text-sm mt-1">
                {{ $stats['approved'] }} dari {{ $stats['total'] }} form telah disetujui.
            </p>
            <div class="relative pt-1 mt-4">
                <div class="gauge-container">
                    <div class="gauge">
                        <div class="gauge-background">
                            <div class="gauge-fill" style="--percentage: {{ ($stats['percentage'] / 100) * 180 }}deg;"></div>
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
                <p class="text-right text-sm font-semibold text-blue-700 mt-1">{{ $stats['percentage'] }}%</p>
            </div>
        </div>
        @endforeach
        @else
        <p class="text-gray-500 col-span-4">Data statistik Daily Test tidak tersedia.</p>
        @endif
    </div>
</div>
