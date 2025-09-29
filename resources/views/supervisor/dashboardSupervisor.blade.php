@extends('layouts.app')

@section('title', 'Supervisor Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 lg:mt-20">
    <div class="bg-white rounded-lg shadow-md p-6" x-data="{
        isStatsModalOpen: false,
        statsModalTitle: '',
        statsModalData: {},
        statsModalBreakdownTitle: 'Lokasi',
        isFormTypeModalOpen: false,
        formTypeModalTitle: '',
        formTypeModalData: {},
        getLinkForType(category, type) {
            const baseRoutes = {
                'Logbook': {
                    'Pos Jaga': '{{ route("supervisor.logbook-form") }}',
                    'Laporan Chief': '{{ route("logbook.chief.index") }}',
                    'Rotasi': '{{ route("supervisor.logbook-rotasi.list") }}',
                    'Manual Book': '{{ route("supervisor.checklist-manualbook.list") }}'
                },
                'Checklist': {
                    'Kendaraan': '{{ route("supervisor.checklist-kendaraan.list") }}',
                    'Penyisiran': '{{ route("supervisor.checklist-penyisiran.list") }}',
                    'Pencatatan PI': '{{ route("supervisor.form-pencatatan-pi.list") }}'
                }
            };
            return baseRoutes[category] ? (baseRoutes[category][type] || '#') : '#';
        }
    }">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            Supervisor Dashboard
        </h1>
        <p class="text-gray-600 mb-6">
            Selamat datang, {{ Auth::user()->name }}! Anda login sebagai Supervisor.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Informasi Pengguna</h3>
                <ul class="text-blue-600">
                    <li><strong>Nama:</strong> {{ Auth::user()->name }}</li>
                    <li><strong>NIP:</strong> {{ Auth::user()->nip }}</li>
                    <li><strong>Email:</strong> {{ Auth::user()->email }}</li>
                    <li><strong>Role:</strong> {{ Auth::user()->role->name }}</li>
                    @if(Auth::user()->lisensi)
                    <li><strong>Lisensi:</strong> {{ Auth::user()->lisensi }}</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="bg-green-50 p-4 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-green-800 mb-4">Notifikasi Supervisor</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Daily Test Card -->
                <a href="{{ route('supervisor.dailytest-form') }}" class="bg-yellow-100 hover:bg-yellow-200 transition-colors p-6 rounded-lg shadow-sm flex items-center space-x-4">
                    <div>
                        <div class="text-yellow-800 text-4xl font-bold">{{ $pendingDailyTestCount }}</div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-lg text-yellow-900">Daily Test</h4>
                        <p class="text-yellow-800">Laporan menunggu persetujuan</p>
                    </div>
                </a>

                <!-- Logbook Card -->
                <div @click="isFormTypeModalOpen = true; formTypeModalTitle = 'Logbook'; formTypeModalData = {{ json_encode($pendingLogbookCounts) }}" class="bg-blue-100 hover:bg-blue-200 transition-colors p-6 rounded-lg shadow-sm flex items-center space-x-4 cursor-pointer">
                    <div>
                        <div class="text-blue-800 text-4xl font-bold">{{ $totalPendingLogbooks }}</div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-lg text-blue-900">Logbook</h4>
                        <p class="text-blue-800">Laporan menunggu persetujuan</p>
                    </div>
                </div>

                <!-- Checklist Card -->
                <div @click="isFormTypeModalOpen = true; formTypeModalTitle = 'Checklist'; formTypeModalData = {{ json_encode($pendingChecklistCounts) }}" class="bg-teal-100 hover:bg-teal-200 transition-colors p-6 rounded-lg shadow-sm flex items-center space-x-4 cursor-pointer">
                    <div>
                        <div class="text-teal-800 text-4xl font-bold">{{ $totalPendingChecklists }}</div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-lg text-teal-900">Checklist</h4>
                        <p class="text-teal-800">Laporan menunggu persetujuan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Test Statistics -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Persentase Penyelesaian Daily Test</h2>
            <p class="text-sm text-gray-500 mb-4 border-b pb-2">Statistik untuk hari ini, {{ now()->translatedFormat('l, d F Y') }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
                @if(isset($dailyTestStats))
                @foreach($dailyTestStats as $type => $stats)
                <div @click="isStatsModalOpen = true; statsModalTitle = '{{ $type }}'; statsModalData = {{ json_encode($stats['breakdown']) }}; statsModalBreakdownTitle = '{{ $stats['breakdownTitle'] }}'"
                    class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200 cursor-pointer hover:bg-gray-100 transition-colors duration-200">
                    <h3 class="text-lg font-semibold text-gray-700">{{ $type }}</h3>
                    <p class="text-gray-500 text-sm mt-1">
                        {{ $stats['approved'] }} dari {{ $stats['total'] }} form telah diajukan.
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
                        <p class="text-right text-sm font-semibold text-blue-700 mt-1">{{ $stats['percentage'] }}%</p>
                    </div>
                </div>
                @endforeach
                @else
                <p class="text-gray-500 col-span-4">Data statistik Daily Test tidak tersedia.</p>
                @endif
            </div>
        </div>
        <!-- Logbook Statistics -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Persentase Penyelesaian Logbook</h2>
            <p class="text-sm text-gray-500 mb-4 border-b pb-2">Statistik untuk hari ini, {{ now()->translatedFormat('l, d F Y') }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
                @if(isset($logbookStats))
                @foreach($logbookStats as $type => $stats)
                <div @if(!empty($stats['breakdown']))
                    @click="isStatsModalOpen = true; statsModalTitle = '{{ $type }}'; statsModalData = {{ json_encode($stats['breakdown']) }}; statsModalBreakdownTitle = '{{ $stats['breakdownTitle'] }}'"
                    class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200 cursor-pointer hover:bg-gray-100 transition-colors duration-200"
                    @else
                    class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200"
                    @endif>
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
                @else
                <p class="text-gray-500 col-span-4">Data statistik logbook tidak tersedia.</p>
                @endif
            </div>
        </div>
        <!-- Checklist Statistics -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Persentase Penyelesaian Checklist</h2>
            <p class="text-sm text-gray-500 mb-4 border-b pb-2">Statistik untuk hari ini, {{ now()->translatedFormat('l, d F Y') }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
                @if(isset($checklistStats))
                @foreach($checklistStats as $type => $stats)
                <div @if(!empty($stats['breakdown']))
                    @click="isStatsModalOpen = true; statsModalTitle = '{{ $type }}'; statsModalData = {{ json_encode($stats['breakdown']) }}; statsModalBreakdownTitle = '{{ $stats['breakdownTitle'] }}'"
                    class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200 cursor-pointer hover:bg-gray-100 transition-colors duration-200"
                    @else
                    class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200"
                    @endif>
                    <h3 class="text-lg font-semibold text-gray-700">{{ $type }}</h3>
                    <p class="text-gray-500 text-sm mt-1">
                        {{ $stats['approved'] }} dari {{ $stats['total'] }} checklist telah diajukan.
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
                        <p class="text-right text-sm font-semibold text-purple-700 mt-1">{{ $stats['percentage'] }}%</p>
                    </div>
                </div>
                @endforeach
                @else
                <p class="text-gray-500 col-span-5">Data statistik checklist tidak tersedia.</p>
                @endif
            </div>
        </div>

        <!-- Stats Modal -->
        <div x-show="isStatsModalOpen" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
            <div @click.away="isStatsModalOpen = false" class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white p-6 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold" x-text="`Detail untuk ${statsModalTitle}`"></h2>
                        <button @click="isStatsModalOpen = false" class="text-blue-100 hover:text-white text-3xl leading-none">&times;</button>
                    </div>
                    <p class="text-blue-100">Rincian jumlah form per <span x-text="statsModalBreakdownTitle.toLowerCase()"></span> untuk hari ini.</p>
                </div>
                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" x-text="statsModalBreakdownTitle"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Form Disetujui</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Form</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(data, key) in statsModalData" :key="key">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="key"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="data.approved"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="data.total"></td>
                                </tr>
                            </template>
                            <template x-if="Object.keys(statsModalData).length === 0">
                                <tr>
                                    <td colspan="3" class="text-center py-8 text-gray-500">Tidak ada data rincian untuk ditampilkan.</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form Type Modal -->
        <div x-show="isFormTypeModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4" style="display: none;">
            <div @click.away="isFormTypeModalOpen = false" class="bg-white w-full max-w-md rounded-xl shadow-2xl transform transition-all">
                <div class="flex justify-between items-center p-4 border-b bg-gray-50 rounded-t-xl">
                    <h2 class="text-xl font-bold text-gray-800" x-text="`Pilih Tipe Form ` + formTypeModalTitle"></h2>
                    <button @click="isFormTypeModalOpen = false" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        <template x-for="(count, type) in formTypeModalData" :key="type">
                            <li x-show="count > 0">
                                <a :href="getLinkForType(formTypeModalTitle, type)" class="flex justify-between items-center p-4 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                    <span class="font-semibold text-gray-700" x-text="type"></span>
                                    <span class="bg-red-600 text-white text-sm font-bold px-3 py-1 rounded-full" x-text="count"></span>
                                </a>
                            </li>
                        </template>
                        <template x-if="Object.values(formTypeModalData).every(c => c === 0)">
                            <li class="text-center py-8 text-gray-500">
                                Tidak ada laporan yang menunggu persetujuan untuk kategori ini.
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
