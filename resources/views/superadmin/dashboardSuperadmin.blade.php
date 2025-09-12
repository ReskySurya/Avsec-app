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

    <!-- Daily Test Statistics -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Persentase Penyelesaian Daily Test</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
            @if(isset($dailyTestStats))
                @foreach($dailyTestStats as $type => $stats)
                    <div class="bg-gray-50 p-5 rounded-xl shadow-inner border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-700">{{ $type }}</h3>
                        <p class="text-gray-500 text-sm mt-1">
                            {{ $stats['approved'] }} dari {{ $stats['total'] }} form telah disetujui.
                        </p>
                        <div class="relative pt-1 mt-4">
                            <div class="overflow-hidden h-3 text-xs flex rounded bg-blue-200">
                                <div style="width:{{ $stats['percentage'] }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"></div>
                            </div>
                            <p class="text-right text-sm font-semibold text-blue-700 mt-1">{{ $stats['percentage'] }}%</p>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-gray-500 col-span-4">Data statistik tidak tersedia.</p>
            @endif
        </div>
    </div>

    <!-- Other stats can go here -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                 
            </div>
        </div>
    </div>

</div>
@endsection