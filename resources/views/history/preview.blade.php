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
