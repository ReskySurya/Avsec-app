@extends('layouts.app')

@section('title', 'Pratinjau Dokumen')

@push('styles')
<style>
    /* Custom styles for preview if needed */
    .preview-container {
        max-width: 800px;
        margin: auto;
    }
    @media print {
        .no-print {
            display: none;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="mx-auto p-0 sm:p-6 min-h-screen pt-5 sm:pt-20">
    <div class="preview-container">
        <div class="flex justify-between items-center mb-6 no-print">
            <h1 class="text-2xl font-bold text-gray-800">Pratinjau Dokumen</h1>
            <div>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-8a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Cetak
                </button>
                <button onclick="window.close()" class="ml-2 inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                    Tutup
                </button>
            </div>
        </div>

        <div class="bg-white shadow-xl rounded-lg overflow-hidden card">
            {{-- The dynamic content from the original PDF view will be injected here --}}
            @include($content_view, $data)
        </div>
    </div>
</div>
@endsection
