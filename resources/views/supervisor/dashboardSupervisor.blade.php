@extends('layouts.app')

@section('title', 'Supervisor Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 lg:mt-20">
    <div class="bg-white rounded-lg shadow-md p-6">
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
        </div>

        @include('partials.dailytest-stats')
        @include('partials.logbook-stats')
        @include('partials.checklist-stats')
    </div>  
</div>
@endsection
