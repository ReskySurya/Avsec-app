@extends('layouts.app')

@section('title', 'Logbook Pos Jaga')
@section('content')

<div class="flex justify-content-between ps-8 mb-8 mt-20">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Attendance Card --}}
        <div class="document-card group border-2 border-gray-200 rounded-lg p-5 cursor-pointer transition-all duration-300 hover:border-blue-500 hover:-translate-y-1 hover:shadow-lg bg-white"
             onclick="">
            <div class="text-center">
                <span class="text-3xl mb-3 block">⏰</span>
                <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">
                    Dokumen Absensi
                </h3>
                <p class="text-sm text-gray-600 group-hover:text-gray-700">
                    Data kehadiran dan ketidakhadiran
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS untuk mobile responsiveness --}}
<style>
    @media (max-width: 640px) {
        .document-card {
            padding: 1rem;
        }

        .document-card span {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .document-card h3 {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .document-card p {
            font-size: 0.75rem;
        }
    }
</style>

@endsection