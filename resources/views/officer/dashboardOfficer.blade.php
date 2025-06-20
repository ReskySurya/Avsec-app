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

            {{-- <div class="bg-green-50 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-semibold text-green-800 mb-2">Menu Officer</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="#" class="bg-white p-3 rounded shadow hover:shadow-md transition-shadow">
                        <h4 class="font-semibold text-green-700">Daily Test Formulir</h4>
                        <p class="text-sm text-green-600">Lihat dan verifikasi laporan</p>
                    </a>
                    <a href="#" class="bg-white p-3 rounded shadow hover:shadow-md transition-shadow">
                        <h4 class="font-semibold text-green-700">Logbook Pos Jaga</h4>
                        <p class="text-sm text-green-600">Lihat dan verifikasi laporan</p>
                    </a>
                    <a href="#" class="bg-white p-3 rounded shadow hover:shadow-md transition-shadow">
                        <h4 class="font-semibold text-green-700">Check List CCTV</h4>
                        <p class="text-sm text-green-600">Lihat dan verifikasi laporan</p>
                    </a>
                </div>
            </div> --}}

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
                                    <td class="px-4 py-2">{{ $report->equipment_name }} ({{ $report->location_name }})</td>
                                    <td class="px-4 py-2">{{ $report->approvalNote }}</td>
                                    <td class="px-4 py-2">
                                        @if($report->equipment_name == 'hhmd')
                                           <a href="{{ route('hhmd.get', $report->reportID) }}"
                                                class="text-blue-600 hover:text-blue-800">
                                                Lihat Detail
                                            </a>
                                        @elseif($report->equipment_name == 'wtmd')
                                            <a href="{{ route('daily-test.wtmd', $report->reportID) }}"
                                                class="text-blue-600 hover:text-blue-800">
                                                Lihat Detail
                                            </a>
                                        @elseif($report->equipment_name == 'xraycabin' || $report->equipment_name == 'xraybagasi')
                                            <a href="{{ route('daily-test.xraycabin', $report->reportID) }}"
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