@extends('layouts.app')

@section('title', 'Superadmin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:mt-20">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Superadmin Dashboard
        </h1>
        <p class="text-gray-600 mt-1">
            Selamat datang, {{ Auth::user()->name }}!
        </p>
    </div>

    <!-- Other stats can go here -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Informasi Pengguna</h3>
            <ul class="text-gray-600 space-y-1">
                <li><strong>Nama:</strong> {{ Auth::user()->name }}</li>
                <li><strong>NIP:</strong> {{ Auth::user()->nip ?? '-' }}</li>
                <li><strong>Email:</strong> {{ Auth::user()->email }}</li>
                <li><strong>Role:</strong> {{ Auth::user()->role->name }}</li>
            </ul>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Menu Cepat</h3>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('users-management.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium">Manajemen Pengguna</a>
                <a href="{{ route('equipment-locations.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium">Manajemen Equipment</a>
                <a href="{{ route('tenant-management.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium">Manajemen Tenant</a>
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
            <div class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">{{ $type }}</h3>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $stats['approved'] }} dari {{ $stats['total'] }} form telah disetujui.
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
            <div class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">{{ $type }}</h3>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $stats['approved'] }} dari {{ $stats['total'] }} logbook telah disetujui.
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
                    <p class="text-right text-sm font-semibold text-gray-700 mt-1">N/A</p>
                </div>
            </div>
            @else
            <p class="text-gray-500 col-span-4">Data statistik logbook tidak tersedia.</p>
            @endif
        </div>
    </div>

    <!-- Checklist Statistics -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-1">Persentase Penyelesaian Checklist</h2>
        <p class="text-sm text-gray-500 mb-4 border-b pb-2">Statistik untuk hari ini, {{ now()->translatedFormat('l, d F Y') }}</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 pt-4">
            @if(isset($checklistStats))
            @foreach($checklistStats as $type => $stats)
            <div class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">{{ $type }}</h3>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $stats['approved'] }} dari {{ $stats['total'] }} checklist telah disetujui.
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
            <div class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">Senjata Api</h3>
                <p class="text-gray-500 text-sm mt-1">
                    Logika penyelesaian untuk checklist ini belum dapat ditentukan.
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
                    <p class="text-right text-sm font-semibold text-gray-700 mt-1">N/A</p>
                </div>
            </div>
            @else
            <p class="text-gray-500 col-span-5">Data statistik checklist tidak tersedia.</p>
            @endif
        </div>
    </div>

</div>

<style>
    .gauge-container {
        display: inline-block;
        text-align: center;
        width: 160px;
    }

    .gauge {
        position: relative;
        width: 140px;
        height: 70px;
        margin: 0 auto 10px;
    }

    .gauge-background {
        width: 140px;
        height: 70px;
        background: #e0e0e0;
        border-radius: 70px 70px 0 0;
        position: relative;
        overflow: hidden;
    }

    .gauge-fill {
        position: absolute;
        top: 0;
        left: 0;
        width: 140px;
        height: 70px;
        background: conic-gradient(from 180deg, #4ade80 0deg, #4ade80 var(--percentage), transparent var(--percentage));
        border-radius: 70px 70px 0 0;
        transition: all 0.8s ease-in-out;
    }

    .gauge-center {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 120px;
        height: 60px;
        background: white;
        border-radius: 60px 60px 0 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 2;
    }

    .gauge-percentage {
        font-size: 28px;
        font-weight: bold;
        color: #333;
        line-height: 1;
    }

    .gauge-label {
        font-size: 11px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .gauge-scale {
        bottom: -5px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #999;
    }
</style>
@endsection