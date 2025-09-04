@extends('layouts.app')

@section('title', 'Export Dokumen')
@section('content')

{{-- Container dengan padding yang sesuai untuk mobile --}}
<div class="container mx-auto px-4 py-6 mt-8 md:mt-20">

    {{-- Header Section --}}
    <div class="mb-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Export Dokumen</h1>
        <p class="text-sm md:text-base text-gray-600">Pilih jenis dokumen yang ingin diekspor</p>
    </div>

    {{-- Document Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">

        {{-- Daily Test Card --}}
        <div class="document-card group border-2 border-gray-200 rounded-xl p-4 md:p-5 cursor-pointer transition-all duration-300 hover:border-blue-500 hover:-translate-y-1 hover:shadow-lg bg-white active:scale-95"
             onclick="window.location.href='{{ route('export.dailytest') }}'">
            <div class="flex flex-col items-center justify-center text-center h-full">
                <div class="w-12 h-12 md:w-16 md:h-16 mb-3 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 md:w-8 md:h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1 md:mb-2 text-sm md:text-base group-hover:text-blue-600 transition-colors text-center">
                    Daily Test
                </h3>
                <p class="text-xs md:text-sm text-gray-600 group-hover:text-gray-700 text-center">
                    Dokumen hasil tes harian
                </p>
            </div>
        </div>

        {{-- Logbook Card --}}
        <div class="document-card group border-2 border-gray-200 rounded-xl p-4 md:p-5 cursor-pointer transition-all duration-300 hover:border-blue-500 hover:-translate-y-1 hover:shadow-lg bg-white active:scale-95"
             onclick="window.location.href='{{ route('export.logbook') }}'">
            <div class="flex flex-col items-center justify-center text-center h-full">
                <div class="w-12 h-12 md:w-16 md:h-16 mb-3 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 md:w-8 md:h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1 md:mb-2 text-sm md:text-base group-hover:text-blue-600 transition-colors text-center">
                    Logbook Harian
                </h3>
                <p class="text-xs md:text-sm text-gray-600 group-hover:text-gray-700 text-center">
                    Catatan aktivitas harian
                </p>
            </div>
        </div>

        {{-- Vehicle Checklist Card --}}
        <div class="document-card group border-2 border-gray-200 rounded-xl p-4 md:p-5 cursor-pointer transition-all duration-300 hover:border-blue-500 hover:-translate-y-1 hover:shadow-lg bg-white active:scale-95"
             onclick="window.location.href='{{ route('export.checklist') }}'">
            <div class="flex flex-col items-center justify-center text-center h-full">
                <div class="w-12 h-12 md:w-16 md:h-16 mb-3 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 md:w-8 md:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1 md:mb-2 text-sm md:text-base group-hover:text-blue-600 transition-colors text-center">
                    Checklist
                </h3>
                <p class="text-xs md:text-sm text-gray-600 group-hover:text-gray-700 text-center">
                    Pemeriksaan Checklist
                </p>
            </div>
        </div>
    </div>

    <!-- {{-- Info Section untuk mobile --}}
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg md:hidden">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-800 font-medium">Tips Export</p>
                <p class="text-xs text-blue-700 mt-1">Tap pada kartu dokumen untuk memulai proses export. Pastikan koneksi internet stabil.</p>
            </div>
        </div>
    </div> -->
</div>

{{-- Touch Feedback Styles --}}
<style>
    /* Tambahan untuk mobile touch feedback */
    @media (max-width: 640px) {
        .document-card {
            min-height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .document-card:active {
            background-color: #f8fafc;
            border-color: #3b82f6;
        }

        /* Spacing untuk mobile */
        .container {
            max-width: 100%;
        }
    }

    /* Untuk tablet */
    @media (min-width: 641px) and (max-width: 1024px) {
        .document-card {
            min-height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }

    /* Desktop */
    @media (min-width: 1025px) {
        .document-card {
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }

    /* Animasi touch untuk mobile */
    @media (hover: none) and (pointer: coarse) {
        .document-card:hover {
            transform: none;
            box-shadow: none;
        }

        .document-card:active {
            transform: scale(0.98);
            transition: transform 0.1s;
        }
    }
</style>

@endsection
