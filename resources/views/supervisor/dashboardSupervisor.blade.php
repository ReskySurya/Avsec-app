@extends('layouts.app')

@section('title', 'Supervisor Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 lg:mt-20">
    <div class="bg-white rounded-lg shadow-md p-6" x-data="{
        isModalOpen: false,
        modalTitle: '',
        modalData: {},
        modalBreakdownTitle: 'Lokasi'
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
            <h3 class="text-lg font-semibold text-green-800 mb-2">Notifikasi Supervisor</h3>

            @if($pendingHhmdReports->count() > 0)
            <div class="bg-yellow-50 p-4 rounded-lg mb-6 shadow-sm">
                <h3 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Laporan HHMD Menunggu Persetujuan
                </h3>

                <!-- Desktop view -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-sm">
                        <thead>
                            <tr class="bg-yellow-100">
                                <th class="px-4 py-3 text-left text-yellow-700 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 text-left text-yellow-700 font-semibold">Waktu</th>
                                <th class="px-4 py-3 text-left text-yellow-700 font-semibold">Lokasi</th>
                                <th class="px-4 py-3 text-left text-yellow-700 font-semibold">Pengirim</th>
                                <th class="px-4 py-3 text-left text-yellow-700 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingHhmdReports as $report)
                            <tr class="border-b hover:bg-yellow-50 transition-colors cursor-pointer"
                                onclick="window.location.href='{{ route('hhmd.reviewForm', ['id' => $report->reportID]) }}'">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($report->testDate)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($report->testDate)->format('H:i') }}</td>
                                <td class="px-4 py-3">{{ $report->equipmentLocation->location->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $report->submittedBy->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $report->status->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500"> 
                                    <p>Tidak ada Laporan HHMD yang perlu ditinjau.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile view -->
                <div class="md:hidden space-y-4">
                    @forelse($pendingHhmdReports as $report)
                    <div class="bg-white rounded-lg shadow-sm border-l-4 border-yellow-400 overflow-hidden cursor-pointer"
                        onclick="window.location.href='{{ route('hhmd.reviewForm', ['id' => $report->reportID]) }}'">
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($report->testDate)->format('d/m/Y') }}
                                    <span class="text-gray-500 ml-2">{{ \Carbon\Carbon::parse($report->testDate)->format('H:i') }}</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Lokasi:</span>
                                    <span class="text-sm font-medium">{{ $report->equipmentLocation->location->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Pengirim:</span>
                                    <span class="text-sm font-medium">{{ $report->submittedBy->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="text-sm font-medium">{{ $report->status->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <p class="text-gray-500">Tidak ada Laporan HHMD yang perlu ditinjau.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            @if($pendingWtmdReports->count() > 0)
            <div class="bg-orange-50 p-4 rounded-lg mb-6 shadow-sm">
                <h3 class="text-lg font-semibold text-orange-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Laporan WTMD Menunggu Persetujuan
                </h3>

                <!-- Desktop view -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-sm">
                        <thead>
                            <tr class="bg-orange-100">
                                <th class="px-4 py-3 text-left text-orange-700 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 text-left text-orange-700 font-semibold">Waktu</th>
                                <th class="px-4 py-3 text-left text-orange-700 font-semibold">Lokasi</th>
                                <th class="px-4 py-3 text-left text-orange-700 font-semibold">Pengirim</th>
                                <th class="px-4 py-3 text-left text-orange-700 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingWtmdReports as $report)
                            <tr class="border-b hover:bg-orange-50 transition-colors cursor-pointer"
                                onclick="window.location.href='{{ route('wtmd.reviewForm', ['id' => $report->reportID]) }}'">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($report->testDate)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($report->testDate)->format('H:i') }}</td>
                                <td class="px-4 py-3">{{ $report->equipmentLocation->location->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $report->submittedBy->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $report->status->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <p>Tidak ada Laporan WTMD yang perlu ditinjau.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile view -->
                <div class="md:hidden space-y-4">
                    @forelse($pendingWtmdReports as $report)
                    <div class="bg-white rounded-lg shadow-sm border-l-4 border-orange-400 overflow-hidden cursor-pointer"
                        onclick="window.location.href='{{ route('wtmd.reviewForm', ['id' => $report->reportID]) }}'">
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($report->testDate)->format('d/m/Y') }}
                                    <span class="text-gray-500 ml-2">{{ \Carbon\Carbon::parse($report->testDate)->format('H:i') }}</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Lokasi:</span>
                                    <span class="text-sm font-medium">{{ $report->equipmentLocation->location->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Pengirim:</span>
                                    <span class="text-sm font-medium">{{ $report->submittedBy->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="text-sm font-medium">{{ $report->status->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <p class="text-gray-500">Tidak ada Laporan WTMD yang perlu ditinjau.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            @if($pendingXrayCabinReports->count() > 0)
            <div class="bg-purple-50 p-4 rounded-lg mb-6 shadow-sm">
                <h3 class="text-lg font-semibold text-purple-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Laporan X-Ray Cabin Menunggu Persetujuan
                </h3>

                <!-- Desktop view -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-sm">
                        <thead>
                            <tr class="bg-purple-100">
                                <th class="px-4 py-3 text-left text-purple-700 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 text-left text-purple-700 font-semibold">Waktu</th>
                                <th class="px-4 py-3 text-left text-purple-700 font-semibold">Lokasi</th>
                                <th class="px-4 py-3 text-left text-purple-700 font-semibold">Pengirim</th>
                                <th class="px-4 py-3 text-left text-purple-700 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingXrayCabinReports as $report)
                            <tr class="border-b hover:bg-purple-50 transition-colors cursor-pointer"
                                onclick="window.location.href='{{ route('xray.reviewForm', ['id' => $report->reportID]) }}'">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($report->testDate)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($report->testDate)->format('H:i') }}</td>
                                <td class="px-4 py-3">{{ $report->equipmentLocation->location->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $report->submittedBy->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $report->status->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <p>Tidak ada Laporan X-Ray Cabin yang perlu ditinjau.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile view -->
                <div class="md:hidden space-y-4">
                    @forelse($pendingXrayCabinReports as $report)
                    <div class="bg-white rounded-lg shadow-sm border-l-4 border-purple-400 overflow-hidden cursor-pointer"
                        onclick="window.location.href='{{ route('xray.reviewForm', ['id' => $report->reportID]) }}'">
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($report->testDate)->format('d/m/Y') }}
                                    <span class="text-gray-500 ml-2">{{ \Carbon\Carbon::parse($report->testDate)->format('H:i') }}</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Lokasi:</span>
                                    <span class="text-sm font-medium">{{ $report->equipmentLocation->location->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Pengirim:</span>
                                    <span class="text-sm font-medium">{{ $report->submittedBy->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="text-sm font-medium">{{ $report->status->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <p class="text-gray-500">Tidak ada Laporan X-Ray Cabin yang perlu ditinjau.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            @if($pendingXrayBagasiReports->count() > 0)
            <div class="bg-pink-50 p-4 rounded-lg mb-6 shadow-sm">
                <h3 class="text-lg font-semibold text-pink-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Laporan X-Ray Bagasi Menunggu Persetujuan
                </h3>

                <!-- Desktop view -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-sm">
                        <thead>
                            <tr class="bg-pink-100">
                                <th class="px-4 py-3 text-left text-pink-700 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 text-left text-pink-700 font-semibold">Waktu</th>
                                <th class="px-4 py-3 text-left text-pink-700 font-semibold">Lokasi</th>
                                <th class="px-4 py-3 text-left text-pink-700 font-semibold">Pengirim</th>
                                <th class="px-4 py-3 text-left text-pink-700 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingXrayBagasiReports as $report)
                            <tr class="border-b hover:bg-pink-50 transition-colors cursor-pointer"
                                onclick="window.location.href='{{ route('xray.reviewForm', ['id' => $report->reportID]) }}'">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($report->testDate)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($report->testDate)->format('H:i') }}</td>
                                <td class="px-4 py-3">{{ $report->equipmentLocation->location->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $report->submittedBy->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $report->status->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <p>Tidak ada Laporan X-Ray Bagasi yang perlu ditinjau.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile view -->
                <div class="md:hidden space-y-4">
                    @forelse($pendingXrayBagasiReports as $report)
                    <div class="bg-white rounded-lg shadow-sm border-l-4 border-pink-400 overflow-hidden cursor-pointer"
                        onclick="window.location.href='{{ route('xray.reviewForm', ['id' => $report->reportID]) }}'">
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($report->testDate)->format('d/m/Y') }}
                                    <span class="text-gray-500 ml-2">{{ \Carbon\Carbon::parse($report->testDate)->format('H:i') }}</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Lokasi:</span>
                                    <span class="text-sm font-medium">{{ $report->equipmentLocation->location->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Pengirim:</span>
                                    <span class="text-sm font-medium">{{ $report->submittedBy->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="text-sm font-medium">{{ $report->status->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <p class="text-gray-500">Tidak ada Laporan X-Ray Bagasi yang perlu ditinjau.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif
            @if($logbooksChief->count() > 0)
            <div class="bg-green-50 p-4 rounded-lg mb-6 shadow-sm">
                <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Laporan Leader yang Diterima
                </h3>

                <!-- Desktop view -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-sm">
                        <thead>
                            <tr class="bg-green-100">
                                <th class="px-4 py-3 text-left text-green-700 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 text-left text-green-700 font-semibold">Jam</th>
                                <th class="px-4 py-3 text-left text-green-700 font-semibold">Shift</th>
                                <th class="px-4 py-3 text-left text-green-700 font-semibold">Grup</th>
                                <th class="px-4 py-3 text-left text-green-700 font-semibold">Pengirim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logbooksChief as $logbook)
                            <tr class="border-b hover:bg-green-50 transition-colors cursor-pointer"
                                onclick="window.location.href='{{ route('logbook.chief.review.laporan.leader', ['logbookID' => $logbook->logbookID]) }}'">
                                <td class="px-4 py-3">{{ $logbook->date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $logbook->created_at->format('H:i') }}</td>
                                <td class="px-4 py-3">{{ ucfirst($logbook->shift) ?? '-' }}</td>
                                <td class="px-4 py-3">{{ ucfirst($logbook->grup) }}</td>
                                <td class="px-4 py-3">{{ $logbook->createdBy->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <p>Tidak ada Laporan Leader yang perlu ditinjau.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile view -->
                <div class="md:hidden space-y-4">
                    @forelse($logbooksChief as $logbook)
                    <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-400 overflow-hidden cursor-pointer"
                        onclick="window.location.href='{{ route('logbook.chief.review.laporan.leader', ['logbookID' => $logbook->logbookID]) }}'">
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $logbook->date->format('d/m/Y') }}
                                    <span class="text-gray-500 ml-2">{{ $logbook->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Shift:</span>
                                    <span class="text-sm font-medium">{{ ucfirst($logbook->shift) ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Grup:</span>
                                    <span class="text-sm font-medium">{{ ucfirst($logbook->grup) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Pengirim:</span>
                                    <span class="text-sm font-medium">{{ $logbook->createdBy->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <p class="text-gray-500">Tidak ada Laporan Leader yang perlu ditinjau.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            @if($pendingKendaraanChecklists->count() > 0)
            <div class="bg-teal-50 p-4 rounded-lg mb-6 shadow-sm">
                <h3 class="text-lg font-semibold text-teal-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path></svg>
                    Checklist Kendaraan Menunggu Persetujuan
                </h3>

                <!-- Desktop view -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-sm">
                        <thead>
                            <tr class="bg-teal-100">
                                <th class="px-4 py-3 text-left text-teal-700 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 text-left text-teal-700 font-semibold">Jenis</th>
                                <th class="px-4 py-3 text-left text-teal-700 font-semibold">Shift</th>
                                <th class="px-4 py-3 text-left text-teal-700 font-semibold">Pengirim</th>
                                <th class="px-4 py-3 text-left text-teal-700 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingKendaraanChecklists as $checklist)
                            <tr class="border-b hover:bg-teal-50 transition-colors cursor-pointer"
                                onclick="window.location.href='{{ route('supervisor.checklist-kendaraan.detail', $checklist->id) }}'">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($checklist->date)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ ucfirst($checklist->type) }}</td>
                                <td class="px-4 py-3">{{ ucfirst($checklist->shift) }}</td>
                                <td class="px-4 py-3">{{ $checklist->sender->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $checklist->status == 'submitted' ? 'Pending' : ucfirst($checklist->status) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <p>Tidak ada Checklist Kendaraan yang perlu ditinjau.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile view -->
                <div class="md:hidden space-y-4">
                    @forelse($pendingKendaraanChecklists as $checklist)
                    <div class="bg-white rounded-lg shadow-sm border-l-4 border-teal-400 overflow-hidden cursor-pointer"
                        onclick="window.location.href='{{ route('supervisor.checklist-kendaraan.detail', $checklist->id) }}'">
                        <div class="p-4">
                            <div class="text-sm font-medium text-gray-900 mb-2">
                                {{ \Carbon\Carbon::parse($checklist->date)->format('d/m/Y') }} - {{ ucfirst($checklist->type) }}
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Shift:</span>
                                    <span class="text-sm font-medium">{{ ucfirst($checklist->shift) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Pengirim:</span>
                                    <span class="text-sm font-medium">{{ $checklist->sender->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="text-sm font-medium">{{ $checklist->status == 'submitted' ? 'Pending' : ucfirst($checklist->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <p class="text-gray-500">Tidak ada Checklist Kendaraan yang perlu ditinjau.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            @if($pendingPenyisiranChecklists->count() > 0)
            <div class="bg-indigo-50 p-4 rounded-lg mb-6 shadow-sm">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                    Checklist Penyisiran Menunggu Persetujuan
                </h3>

                <!-- Desktop view -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-sm">
                        <thead>
                            <tr class="bg-indigo-100">
                                <th class="px-4 py-3 text-left text-indigo-700 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 text-left text-indigo-700 font-semibold">Jam</th>
                                <th class="px-4 py-3 text-left text-indigo-700 font-semibold">Grup</th>
                                <th class="px-4 py-3 text-left text-indigo-700 font-semibold">Pengirim</th>
                                <th class="px-4 py-3 text-left text-indigo-700 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingPenyisiranChecklists as $checklist)
                            <tr class="border-b hover:bg-indigo-50 transition-colors cursor-pointer"
                                onclick="window.location.href='{{ route('supervisor.checklist-penyisiran.detail', $checklist->id) }}'">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($checklist->date)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($checklist->time)->format('H:i') }}</td>
                                <td class="px-4 py-3">{{ $checklist->grup }}</td>
                                <td class="px-4 py-3">{{ $checklist->sender->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $checklist->status == 'submitted' ? 'Pending' : ucfirst($checklist->status) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <p>Tidak ada Checklist Penyisiran yang perlu ditinjau.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile view -->
                <div class="md:hidden space-y-4">
                    @forelse($pendingPenyisiranChecklists as $checklist)
                    <div class="bg-white rounded-lg shadow-sm border-l-4 border-indigo-400 overflow-hidden cursor-pointer"
                        onclick="window.location.href='{{ route('supervisor.checklist-penyisiran.detail', $checklist->id) }}'">
                        <div class="p-4">
                            <div class="text-sm font-medium text-gray-900 mb-2">
                                {{ \Carbon\Carbon::parse($checklist->date)->format('d/m/Y') }} - Grup {{ $checklist->grup }}
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Jam:</span>
                                    <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($checklist->time)->format('H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Pengirim:</span>
                                    <span class="text-sm font-medium">{{ $checklist->sender->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="text-sm font-medium">{{ $checklist->status == 'submitted' ? 'Pending' : ucfirst($checklist->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <p class="text-gray-500">Tidak ada Checklist Penyisiran yang perlu ditinjau.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            @if($pendingPIChecklists->count() > 0)
            <div class="bg-cyan-50 p-4 rounded-lg mb-6 shadow-sm">
                <h3 class="text-lg font-semibold text-cyan-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                    Form Pencatatan PI Menunggu Persetujuan
                </h3>

                <!-- Desktop view -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-sm">
                        <thead>
                            <tr class="bg-cyan-100">
                                <th class="px-4 py-3 text-left text-cyan-700 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 text-left text-cyan-700 font-semibold">Jam Masuk</th>
                                <th class="px-4 py-3 text-left text-cyan-700 font-semibold">Grup</th>
                                <th class="px-4 py-3 text-left text-cyan-700 font-semibold">Nama Personil</th>
                                <th class="px-4 py-3 text-left text-cyan-700 font-semibold">Pengirim</th>
                                <th class="px-4 py-3 text-left text-cyan-700 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingPIChecklists as $checklist)
                            <tr class="border-b hover:bg-cyan-50 transition-colors cursor-pointer"
                                onclick="window.location.href='{{ route('supervisor.form-pencatatan-pi.detail', ['checklist' => $checklist->id]) }}'">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($checklist->date)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($checklist->in_time)->format('H:i') }}</td>
                                <td class="px-4 py-3">{{ $checklist->grup }}</td>
                                <td class="px-4 py-3">{{ $checklist->name_person }}</td>
                                <td class="px-4 py-3">{{ $checklist->sender->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $checklist->status == 'submitted' ? 'Pending' : ucfirst($checklist->status) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <p>Tidak ada Form Pencatatan PI yang perlu ditinjau.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile view -->
                <div class="md:hidden space-y-4">
                    @forelse($pendingPIChecklists as $checklist)
                    <div class="bg-white rounded-lg shadow-sm border-l-4 border-cyan-400 overflow-hidden cursor-pointer"
                        onclick="window.location.href='{{ route('supervisor.form-pencatatan-pi.detail', ['checklist' => $checklist->id]) }}'">
                        <div class="p-4">
                            <div class="text-sm font-medium text-gray-900 mb-2">
                                {{ \Carbon\Carbon::parse($checklist->date)->format('d/m/Y') }} - Grup {{ $checklist->grup }}
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Nama:</span>
                                    <span class="text-sm font-medium">{{ $checklist->name_person }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Pengirim:</span>
                                    <span class="text-sm font-medium">{{ $checklist->sender->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="text-sm font-medium">{{ $checklist->status == 'submitted' ? 'Pending' : ucfirst($checklist->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <p class="text-gray-500">Tidak ada Form Pencatatan PI yang perlu ditinjau.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

        @include('partials.dailytest-stats')
        @include('partials.logbook-stats')
        @include('partials.checklist-stats')
        <!-- Modal -->
        <div x-show="isModalOpen" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
            <div @click.away="isModalOpen = false" class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white p-6 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold" x-text="`Detail untuk ${modalTitle}`"></h2>
                        <button @click="isModalOpen = false" class="text-blue-100 hover:text-white text-3xl leading-none">&times;</button>
                    </div>
                    <p class="text-blue-100">Rincian jumlah form per <span x-text="modalBreakdownTitle.toLowerCase()"></span> untuk hari ini.</p>
                </div>
                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" x-text="modalBreakdownTitle"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Form Disetujui</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Form</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(data, key) in modalData" :key="key">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="key"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="data.approved"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="data.total"></td>
                                </tr>
                            </template>
                            <template x-if="Object.keys(modalData).length === 0">
                                <tr>
                                    <td colspan="3" class="text-center py-8 text-gray-500">Tidak ada data rincian untuk ditampilkan.</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection