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

            <div class="bg-red-50 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-semibold text-red-800 mb-2">List Laporan di Tolak</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-red-100">
                                <th class="px-4 py-2 text-left text-red-700">Tanggal</th>
                                <th class="px-4 py-2 text-left text-red-700">Jenis Laporan</th>
                                <th class="px-4 py-2 text-left text-red-700">Alasan Penolakan</th>
                                <th class="px-4 py-2 text-left text-red-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rejectedReports as $report)
                                <tr class="border-b hover:bg-red-50">
                                    <td class="px-4 py-2">{{ $report->created_at->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2">{{ $report->equipmentLocation->location->name ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $report->approvalNote }}</td>
                                    <td class="px-4 py-2">
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
                                            <a href="{{ route('officer.xraycabin.editRejectedForm', $report->reportID) }}"
                                                class="text-blue-600 hover:text-blue-800">
                                                Lihat Detail
                                            </a>
                                         @elseif($report->equipmentLocation->equipment->name == 'xraybagasi')
                                            <a href="{{ route('officer.xraybagasi.editRejectedForm', $report->reportID) }}"
                                                class="text-blue-600 hover:text-blue-800">
                                                Lihat Detail
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">
                                        Tidak ada laporan yang ditolak
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
