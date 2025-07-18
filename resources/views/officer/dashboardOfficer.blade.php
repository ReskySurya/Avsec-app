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

        <div class="bg-red-50 p-4 rounded-lg mb-6 shadow-sm">
            <h3 class="text-lg font-semibold text-red-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
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
                            <td class="px-4 py-3">{{ $report->equipmentLocation->equipment->name ?? '-' }} ( {{ $report->equipmentLocation->location->name ?? '-' }} )</td>
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
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 uppercase">{{ $report->equipmentLocation->equipment->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $report->equipmentLocation->location->name ?? '-' }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $report->created_at->format('d/m/Y') }}</span>
                        </div>

                        <div class="mb-3">
                            <p class="text-sm font-medium text-gray-700 mb-1">Alasan Penolakan:</p>
                            <p class="text-sm text-gray-600 bg-red-50 p-2 rounded">{{ $report->approvalNote }}</p>
                        </div>

                        <div class="flex justify-end">
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
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">Tidak ada laporan yang ditolak</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection