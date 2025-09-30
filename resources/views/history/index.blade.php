@extends('layouts.app')

@section('title', 'History Center')

@push('styles')
<style>
    body {
        background-color: #f8fafc; /* Light gray background */
    }
</style>
@endpush

@section('content')
<div class="mx-auto p-0 sm:p-6 min-h-screen pt-5 sm:pt-20">
    <div class="text-center mb-12">
        <h1 class="text-2xl sm:text-2xl font-extrabold text-gray-800">Pusat Riwayat</h1>
        <p class="mt-4 text-lg text-gray-600">Akses semua arsip formulir yang telah disetujui.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        
        <!-- Card Daily Test -->
        <a href="{{ route('history.show', ['category' => 'daily-test']) }}" class="group block">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg group-hover:shadow-2xl transform group-hover:-translate-y-2 transition-all duration-300 ease-in-out p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-white bg-opacity-20">
                        <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <h2 class="ml-4 text-2xl font-bold">Daily Test</h2>
                </div>
                <p class="opacity-90">Lihat riwayat formulir Daily Test untuk HHMD, WTMD, dan X-Ray.</p>
            </div>
        </a>

        <!-- Card Logbook -->
        <a href="{{ route('history.show', ['category' => 'logbook']) }}" class="group block">
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg group-hover:shadow-2xl transform group-hover:-translate-y-2 transition-all duration-300 ease-in-out p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-white bg-opacity-20">
                        <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h2 class="ml-4 text-2xl font-bold">Logbook</h2>
                </div>
                <p class="opacity-90">Akses riwayat Logbook untuk Pos Jaga, Chief, Rotasi, dan Sweeping PI.</p>
            </div>
        </a>

        <!-- Card Checklist -->
        <a href="{{ route('history.show', ['category' => 'checklist']) }}" class="group block">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg group-hover:shadow-2xl transform group-hover:-translate-y-2 transition-all duration-300 ease-in-out p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-white bg-opacity-20">
                        <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h2 class="ml-4 text-2xl font-bold">Checklist</h2>
                </div>
                <p class="opacity-90">Jelajahi riwayat Checklist Kendaraan, Senpi, Penyisiran, dan lainnya.</p>
            </div>
        </a>

    </div>
</div>
@endsection