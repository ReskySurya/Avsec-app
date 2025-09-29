@extends('layouts.app')

@section('title', 'Tugas Pengisian Logbook')

@section('content')
<div class="mx-auto p-0 sm:p-6 min-h-screen pt-5 sm:pt-20">

    <!-- Success Message -->
    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
        class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-blue-600">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-6">
            <div class="flex flex-col sm:flex-row justify-between items-start">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-1">Logbook Berhasil Dibuat!</h3>
                    <p class="text-gray-500">Logbook Anda telah disimpan sebagai draft. Silakan lengkapi formulir
                        terkait di bawah ini.</p>
                </div>
            </div>
        </div>

        <!-- Logbook Details -->
        <div class="p-6 border-b border-gray-200">
            <h4 class="text-lg font-semibold text-gray-700 mb-4">Detail Logbook</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3a1 1 0 011-1h2a1 1 0 011 1v4m-5 4h10M3 11h18M4 15h16M4 19h16"></path>
                    </svg>
                    <span class="text-gray-600"><strong class="font-medium text-gray-800">Tanggal:</strong> {{
                        \Carbon\Carbon::parse($logbook->date)->isoFormat('dddd, D MMMM YYYY') }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-gray-600"><strong class="font-medium text-gray-800">Area:</strong> {{
                        $logbook->locationArea->name ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <span class="text-gray-600"><strong class="font-medium text-gray-800">Grup:</strong> {{
                        $logbook->grup ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-600"><strong class="font-medium text-gray-800">Shift:</strong> {{
                        ucfirst($logbook->shift ?? 'N/A') }}</span>
                </div>
            </div>
        </div>

        <!-- Related Forms -->
        <div class="p-6">
            <h4 class="text-lg font-semibold text-gray-700 mb-4">Formulir Terkait</h4>
            <div class="space-y-4">
                <!-- Card for Main Logbook -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <h5 class="font-bold text-gray-800">1. Logbook Pos Jaga</h5>
                        <p class="text-sm text-gray-500">Formulir utama untuk uraian kegiatan, personil, dan fasilitas.
                        </p>
                    </div>
                    <a href="{{ route('logbook.detail', ['id' => $logbook->logbookID]) }}"
                        class="bg-blue-500 text-white hover:bg-blue-600 px-5 py-2 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 text-sm">
                        Lengkapi Data
                    </a>
                </div>

                <!-- Card for Logbook Rotasi -->
                @if($logbookRotasi)
                <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <h5 class="font-bold text-gray-800">2. Logbook Rotasi Personil</h5>
                        <p class="text-sm text-gray-500">Formulir untuk jadwal rotasi personil di area {{
                            $logbook->locationArea->name }}.</p>
                    </div>
                    <a href="{{ route('logbookRotasi.index', ['type' => strtolower($logbook->locationArea->name), 'logbookID' => $logbook->logbookID]) }}"
                        class="bg-teal-500 text-white hover:bg-teal-600 px-5 py-2 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 text-sm">
                        Lengkapi Data
                    </a>
                </div>
                @endif

                <!-- Card for Manual Book -->
                @if($manualBook)
                <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <h5 class="font-bold text-gray-800">3. Buku Pemeriksaan Manual</h5>
                        <p class="text-sm text-gray-500">Formulir untuk jadwal rotasi personil di area {{
                            $logbook->locationArea->name }}.</p>
                    </div>
                    <a href="{{ route('checklist.manualbook.index') }}"
                        class="bg-teal-500 text-white hover:bg-teal-600 px-5 py-2 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 text-sm">
                        Lengkapi Data
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 flex justify-end">
            <a href="{{ route('logbook.index') }}"
                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection
