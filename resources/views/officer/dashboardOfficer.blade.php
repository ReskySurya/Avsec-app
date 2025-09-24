@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 lg:mt-20">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            Dashboard
        </h1>
        <p class="text-gray-600 mb-6">
            Selamat datang, {{ Auth::user()->name }}!
        </p>

        <div class="bg-blue-50 p-4 rounded-lg mb-4">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Informasi Pengguna</h3>
            <ul class="text-blue-600">
                <li><strong>Nama:</strong> {{ Auth::user()->name }}</li>
                <li><strong>NIP:</strong> {{ Auth::user()->nip }}</li>
            </ul>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Pengisian Form Harian (Daily Test)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Belum Diisi --}}
                <div>
                    <h4 class="font-semibold text-orange-700 mb-2">Belum Diisi</h4>
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        @if(count($dailyTestStatuses['not_submitted']) > 0)
                            <ul class="space-y-2">
                                @foreach($dailyTestStatuses['not_submitted'] as $item)
                                    <li>
                                        <a href="{{ $item['form_link'] }}" class="flex items-center justify-between p-2 rounded-md hover:bg-gray-100 transition-colors">
                                            <span class="text-sm text-gray-700">
                                                {{ strtoupper($item['equipment_name']) }} - {{ $item['location_name'] }}
                                            </span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <svg class="w-12 h-12 mx-auto text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-sm text-gray-500 mt-2">Semua form sudah diisi. Kerja bagus!</p>
                            </div>
                        @endif
                    </div>
                </div>
        
                {{-- Sudah Diisi --}}
                <div>
                    <h4 class="font-semibold text-green-700 mb-2">Sudah Diisi</h4>
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        @if(count($dailyTestStatuses['submitted']) > 0)
                            <ul class="space-y-2">
                                @foreach($dailyTestStatuses['submitted'] as $item)
                                    <li class="flex items-center p-2">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <span class="text-sm text-gray-500 line-through">
                                            {{ strtoupper($item['equipment_name']) }} - {{ $item['location_name'] }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <p class="text-sm text-gray-500 mt-2">Belum ada form yang diisi hari ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
        
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Pengisian Form Checklist</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Belum Diisi --}}
                <div>
                    <h4 class="font-semibold text-orange-700 mb-2">Belum Diisi</h4>
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        @if(count($checklistStatuses['not_submitted']) > 0)
                            <ul class="space-y-2">
                                @foreach($checklistStatuses['not_submitted'] as $item)
                                    <li>
                                        <a href="{{ $item['form_link'] }}" class="flex items-center justify-between p-2 rounded-md hover:bg-gray-100 transition-colors">
                                            <span class="text-sm text-gray-700">{{ $item['name'] }}</span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <svg class="w-12 h-12 mx-auto text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-sm text-gray-500 mt-2">Semua form checklist sudah diisi. Kerja bagus!</p>
                            </div>
                        @endif
                    </div>
                </div>
        
                {{-- Sudah Diisi --}}
                <div>
                    <h4 class="font-semibold text-green-700 mb-2">Sudah Diisi</h4>
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        @if(count($checklistStatuses['submitted']) > 0)
                            <ul class="space-y-2">
                                @foreach($checklistStatuses['submitted'] as $item)
                                    <li class="flex items-center p-2">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <span class="text-sm text-gray-500 line-through">{{ $item['name'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <p class="text-sm text-gray-500 mt-2">Belum ada form checklist yang diisi hari ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
        
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Logbook Sweeping PI</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Belum Diisi --}}
                <div>
                    <h4 class="font-semibold text-orange-700 mb-2">Belum Diisi</h4>
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        @if(count($sweepingStatuses['not_submitted']) > 0)
                            <ul class="space-y-2">
                                @foreach($sweepingStatuses['not_submitted'] as $item)
                                    <li>
                                        <a href="{{ $item['form_link'] }}" class="flex items-center justify-between p-2 rounded-md hover:bg-gray-100 transition-colors">
                                            <span class="text-sm text-gray-700">{{ $item['name'] }}</span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <svg class="w-12 h-12 mx-auto text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-sm text-gray-500 mt-2">Semua logbook sweeping sudah diisi. Kerja bagus!</p>
                            </div>
                        @endif
                    </div>
                </div>
        
                {{-- Sudah Diisi --}}
                <div>
                    <h4 class="font-semibold text-green-700 mb-2">Sudah Diisi</h4>
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        @if(count($sweepingStatuses['submitted']) > 0)
                            <ul class="space-y-2">
                                @foreach($sweepingStatuses['submitted'] as $item)
                                    <li class="flex items-center p-2">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <span class="text-sm text-gray-500 line-through">{{ $item['name'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <p class="text-sm text-gray-500 mt-2">Belum ada logbook sweeping yang diisi hari ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
        
            </div>
        </div>

        @if($rejectedReports->count() > 0)
        <div class="bg-red-50 p-4 rounded-lg mb-6 shadow-sm">
            <h3 class="text-lg font-semibo</div>ld text-red-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="e</svg>venodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                List Laporan Ditolak
            </h3>

            <!-- Desktop Table View (hidden on mobile) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow-sm">
                    <thead>
                        <tr class="bg-red-100">
                            <th class="px-4 py-3 text-left text-red-700 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 text-left text-red-700 font-semibold">Jenis Laporan</th>
                            <th class="px-4 py-3 text-left text-red-700 font-semibold">Alasan Penolakan</th>
                            <th class="px-4 py-3 text-left text-red-700 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rejectedReports as $report)
                        <tr class="border-b hover:bg-red-50 transition-colors">
                            <td class="px-4 py-3">{{ $report->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $report->equipmentLocation->equipment->name ?? '-' }} ( {{
                                $report->equipmentLocation->location->name ?? '-' }} )</td>
                            <td class="px-4 py-3">{{ $report->approvalNote }}</td>
                            <td class="px-4 py-3">
                                @if($report->equipmentLocation->equipment->name == 'hhmd')
                                <a href="{{ route('officer.hhmd.editRejectedReport', $report->reportID) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    Lihat Detail
                                </a>
                                @elseif($report->equipmentLocation->equipment->name == 'wtmd')
                                <a href="{{ route('officer.wtmd.editRejectedReport', $report->reportID) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    Lihat Detail
                                </a>
                                @elseif($report->equipmentLocation->equipment->name == 'xraycabin')
                                <a href="{{ route('officer.xraycabin.editRejectedReport', $report->reportID) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    Lihat Detail
                                </a>
                                @elseif($report->equipmentLocation->equipment->name == 'xraybagasi')
                                <a href="{{ route('officer.xraybagasi.editRejectedReport', $report->reportID) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    Lihat Detail
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <p>Tidak ada laporan yang ditolak</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (visible on mobile only) -->
            <div class="md:hidden space-y-4">
                @forelse($rejectedReports as $report)
                <div class="bg-white rounded-lg shadow-sm border-l-4 border-red-400 overflow-hidden">
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 uppercase">{{
                                        $report->equipmentLocation->equipment->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $report->equipmentLocation->location->name ??
                                        '-' }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{
                                $report->created_at->format('d/m/Y') }}</span>
                        </div>

                        <div class="mb-3">
                            <p class="text-sm font-medium text-gray-700 mb-1">Alasan Penolakan:</p>
                            <p class="text-sm text-gray-600 bg-red-50 p-2 rounded">{{ $report->approvalNote }}</p>
                        </div>

                        <div class="flex space-x-2">

                            @if($report->equipmentLocation->equipment->name == 'hhmd')
                            <a href="{{ route('officer.hhmd.editRejectedReport', $report->reportID) }}"
                                class="flex-1 text-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                                Lihat Detail
                            </a>
                            @elseif($report->equipmentLocation->equipment->name == 'wtmd')
                            <a href="{{ route('officer.wtmd.editRejectedReport', $report->reportID) }}"
                                class="flex-1 text-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                                Lihat Detail
                            </a>
                            @elseif($report->equipmentLocation->equipment->name == 'xraycabin')
                            <a href="{{ route('officer.xraycabin.editRejectedReport', $report->reportID) }}"
                                class="flex-1 text-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                                Lihat Detail
                            </a>
                            @elseif($report->equipmentLocation->equipment->name == 'xraybagasi')
                            <a href="{{ route('officer.xraybagasi.editRejectedReport', $report->reportID) }}"
                                class="flex-1 text-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                                Lihat Detail
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <p class="text-gray-500 text-sm">Tidak ada laporan yang ditolak</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif

        {{-- Section Logbook yang Ditolak --}}
        @if($rejectedlogbooks->count() > 0)
        <div class="bg-red-50 p-4 rounded-lg mb-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-red-800 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                    Logbook yang Ditolak
                </h2>
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-sm">
                        <thead class="bg-red-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-red-700 font-semibold">
                                    Lokasi & Tanggal
                                </th>
                                <th class="px-4 py-3 text-left text-red-700 font-semibold">
                                    Shift
                                </th>
                                <th class="px-4 py-3 text-left text-red-700 font-semibold">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-left text-red-700 font-semibold">
                                    Alasan Penolakan
                                </th>
                                <th class="px-4 py-3 text-left text-red-700 font-semibold">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rejectedlogbooks as $logbook)
                            <tr class="hover:bg-red-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-red-500 rounded-full mr-3"></div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $logbook->locationArea->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($logbook->date)->format('d M Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($logbook->shift == 'pagi') bg-yellow-100 text-yellow-800
                                    @elseif($logbook->shift == 'siang') bg-orange-100 text-orange-800
                                    @else bg-purple-100 text-purple-800 @endif">
                                        {{ ucfirst($logbook->shift) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Ditolak
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($logbook->rejected_reason)
                                    <div class="max-w-xs">
                                        <p class="text-sm text-gray-900 truncate"
                                            title="{{ $logbook->rejected_reason }}">
                                            {{ $logbook->rejected_reason }}
                                        </p>
                                    </div>
                                    @else
                                    <span class="text-sm text-gray-400 italic">Tidak ada alasan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('logbook.index',  $logbook->logbookID) }}"
                                        class="text-blue-600 hover:text-blue-800">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile Card View --}}
            <div class="block md:hidden space-y-4">
                @foreach($rejectedlogbooks as $logbook)
                <div class="border-l-4 border-red-400 bg-white p-4 rounded-lg shadow-sm">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h3 class="text-base font-medium text-gray-900">
                                {{ $logbook->locationArea->name }}
                            </h3>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($logbook->date)->format('d M Y')
                                }}</p>
                        </div>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Ditolak
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 mb-3">
                        <div><strong>Shift:</strong> {{ ucfirst($logbook->shift) }}</div>
                    </div>

                    @if($logbook->rejected_reason)
                    <div class="bg-red-100 border border-red-200 rounded-lg p-2 mb-3">
                        <p class="text-xs text-red-800"><strong>Alasan:</strong> {{ $logbook->rejected_reason }}</p>
                    </div>
                    @endif

                    <div class="flex space-x-2">
                        <a href="{{ route('logbook.index',  $logbook->logbookID) }}"
                            class="flex-1 text-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                            Lihat Detail
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $rejectedlogbooks->links() }}
            </div>
        </div>
        @endif

        @if($logbookEntries->count() > 0)
        <div class="bg-green-50 p-4 rounded-lg mb-6 shadow-sm">
            <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                List Logbook yang Diterima
            </h3>

            <!-- Desktop Table View (hidden on mobile) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow-sm">
                    <thead>
                        <tr class="bg-green-100">
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Area</th>
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Grup</th>
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Shift</th>
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Pengirim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logbookEntries as $logbook)
                        <tr class="border-b hover:bg-green-50 transition-colors cursor-pointer"
                            onclick="window.location.href='{{ route('officer.received.show', ['location' => $logbook->locationArea->name, 'logbookID' => $logbook->logbookID]) }}'">
                            <td class="px-4 py-3">{{ $logbook->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $logbook->locationArea->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $logbook->grup }}</td>
                            <td class="px-4 py-3">{{ $logbook->shift }}</td>
                            <td class="px-4 py-3">{{ $logbook->senderBy->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <p>Tidak ada Logbook yang Diterima</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (visible on mobile only) -->
            <div class="md:hidden space-y-4">
                @forelse($logbookEntries as $logbook)
                <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-400 overflow-hidden cursor-pointer"
                    onclick="window.location.href='{{ route('officer.received.show', ['location' => $logbook->locationArea->name, 'logbookID' => $logbook->logbookID]) }}'">
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 uppercase">{{
                                        $logbook->locationArea->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">Grup: {{ $logbook->grup }} | Shift: {{
                                        $logbook->shift }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{
                                $logbook->created_at->format('d/m/Y') }}</span>
                        </div>

                        <div class="mb-3">
                            <p class="text-sm font-medium text-gray-700 mb-1">Pengirim:</p>
                            <p class="text-sm text-gray-600 bg-green-50 p-2 rounded">{{ $logbook->senderBy->name ?? '-'
                                }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <p class="text-gray-500 text-sm">Tidak ada Logbook yang Diterima</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif

        @if($checklistKendaraan->count() > 0)
        <div class="bg-green-50 p-4 rounded-lg mb-6 shadow-sm">
            <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                List Checklist Kendaraan yang Diterima
            </h3>

            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow-sm">
                    <thead>
                        <tr class="bg-green-100">
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Jenis Kendaraan</th>
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Shift</th>
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Pengirim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($checklistKendaraan as $checklist)
                        {{-- PERBAIKAN 1: Gunakan $checklist->id untuk route --}}
                        <tr class="border-b hover:bg-green-50 transition-colors cursor-pointer"
                            onclick="window.location.href='{{ route('officer.receivedChecklistKendaraan.show', ['type' => $checklist->type, 'id' => $checklist->id]) }}'">
                            <td class="px-4 py-3">{{ $checklist->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ ucfirst($checklist->type) ?? '-' }}</td>
                            <td class="px-4 py-3">{{ ucfirst($checklist->shift) }}</td>
                            {{-- PERBAIKAN 2: Panggil nama pengirim melalui relasi 'sender' --}}
                            <td class="px-4 py-3">{{ $checklist->sender->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                <p>Tidak ada Checklist Kendaraan yang perlu ditinjau.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="md:hidden space-y-4">
                @forelse($checklistKendaraan as $checklist)
                {{-- PERBAIKAN 3: Gunakan $checklist->id untuk route di mobile view juga --}}
                <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-400 overflow-hidden cursor-pointer"
                    onclick="window.location.href='{{ route('officer.receivedChecklistKendaraan.show', ['type' => $checklist->type, 'id' => $checklist->id]) }}'">
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    {{-- Icon bisa disesuaikan berdasarkan tipe kendaraan --}}
                                    @if($checklist->type == 'mobil')
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V8.618a1 1 0 01.553-.894L9 5l6 2.724a1 1 0 01.447.894v7.764a1 1 0 01-.553.894L9 20z">
                                        </path>
                                    </svg>
                                    @else
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m12 0a2 2 0 100-4m0 4a2 2 0 110-4M6 12a2 2 0 100-4m0 4a2 2 0 110-4m6 0a2 2 0 100-4m0 4a2 2 0 110-4m6 0a2 2 0 100-4m0 4a2 2 0 110-4">
                                        </path>
                                    </svg>
                                    @endif
                                </div>
                                <div>
                                    {{-- Sesuaikan dengan data yang ada di $checklist --}}
                                    <p class="text-sm font-medium text-gray-900 uppercase">{{ $checklist->type }}
                                        Patroli</p>
                                    <p class="text-xs text-gray-500">Shift: {{ $checklist->shift }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{
                                $checklist->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="mb-3">
                            <p class="text-sm font-medium text-gray-700 mb-1">Pengirim:</p>
                            {{-- PERBAIKAN 4: Gunakan relasi 'sender' juga di mobile view --}}
                            <p class="text-sm text-gray-600 bg-green-50 p-2 rounded">{{ $checklist->sender->name ?? '-'
                                }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <p class="text-gray-500 text-sm">Tidak ada Checklist Kendaraan yang Diterima</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif

        
        @if($checklistPenyisiran->count() > 0)
        <div class="bg-green-50 p-4 rounded-lg mb-6 shadow-sm">
            <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                List Checklist Penyisiran yang Diterima
            </h3>

            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow-sm">
                    <thead>
                        <tr class="bg-green-100">
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Jam</th>
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Tipe Pengecekan</th>
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Grup</th>
                            <th class="px-4 py-3 text-left text-green-700 font-semibold">Pengirim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($checklistPenyisiran as $checklist)
                        {{-- PERBAIKAN 1: Gunakan $checklist->id untuk route --}}
                        <tr class="border-b hover:bg-green-50 transition-colors cursor-pointer"
                            onclick="window.location.href='{{ route('officer.receivedChecklistPenyisiran.show', ['id' => $checklist->id]) }}'">
                            <td class="px-4 py-3">{{ $checklist->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $checklist->created_at->format('H:i') }}</td>
                            <td class="px-4 py-3">{{ ucfirst($checklist->type) ?? '-' }}</td>
                            <td class="px-4 py-3">{{ ucfirst($checklist->grup) }}</td>
                            {{-- PERBAIKAN 2: Panggil nama pengirim melalui relasi 'sender' --}}
                            <td class="px-4 py-3">{{ $checklist->sender->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                <p>Tidak ada Checklist Penyisiran yang perlu ditinjau.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="md:hidden space-y-4">
                @forelse($checklistPenyisiran as $checklist)
                {{-- PERBAIKAN 3: Gunakan $checklist->id untuk route di mobile view juga --}}
                <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-400 overflow-hidden cursor-pointer"
                    onclick="window.location.href='{{ route('officer.receivedChecklistPenyisiran.show', ['id' => $checklist->id]) }}'">
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    {{-- Icon bisa disesuaikan berdasarkan tipe kendaraan --}}
                                    @if($checklist->type == 'mobil')
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V8.618a1 1 0 01.553-.894L9 5l6 2.724a1 1 0 01.447.894v7.764a1 1 0 01-.553.894L9 20z">
                                        </path>
                                    </svg>
                                    @else
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m12 0a2 2 0 100-4m0 4a2 2 0 110-4M6 12a2 2 0 100-4m0 4a2 2 0 110-4m6 0a2 2 0 100-4m0 4a2 2 0 110-4m6 0a2 2 0 100-4m0 4a2 2 0 110-4">
                                        </path>
                                    </svg>
                                    @endif
                                </div>
                                <div>
                                    {{-- Sesuaikan dengan data yang ada di $checklist --}}
                                    <p class="text-sm font-medium text-gray-900 uppercase">{{ $checklist->type }}
                                        Ruang Tunggu</p>
                                    <p class="text-xs text-gray-500">Grup: {{ $checklist->grup }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{
                                $checklist->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="mb-3">
                            <p class="text-sm font-medium text-gray-700 mb-1">Pengirim:</p>
                            {{-- PERBAIKAN 4: Gunakan relasi 'sender' juga di mobile view --}}
                            <p class="text-sm text-gray-600 bg-green-50 p-2 rounded">{{ $checklist->sender->name ?? '-'
                                }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <p class="text-gray-500 text-sm">Tidak ada Checklist Kendaraan yang Diterima</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
