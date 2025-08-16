@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:pt-20">

    {{-- Logbook Rotasi PSCP --}}
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Logbook Rotasi PSCP</h1>
            <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                {{ $logbooksPSCP->count() }} Records
            </span>
        </div>

        <div class="overflow-x-auto bg-white shadow-md rounded-lg mb-4">
            <table class="min-w-full border-collapse border border-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                                ID Logbook
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                Tanggal
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Status
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                Aksi
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logbooksPSCP as $logbook)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-3 border-b text-sm font-medium text-gray-900">
                            <div class="flex items-center">
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                                    {{ $logbook->id }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 border-b text-sm text-gray-700">
                            <div class="flex flex-col">
                                <span class="font-medium">{{ $logbook->date->format('d M Y') }}</span>
                                <span class="text-xs text-gray-500">{{ $logbook->date->format('l') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 border-b text-sm">
                            @if($logbook->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Disetujui
                                </span>
                            @elseif($logbook->status === 'submitted')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Menunggu Persetujuan
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 border-b text-sm">
                            <a href="{{ route('supervisor.logbook-rotasipscp.detail', $logbook->id) }}"
                               class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">Belum ada data logbook PSCP</p>
                                <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah logbook dibuat</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination untuk PSCP jika ada --}}
        @if(method_exists($logbooksPSCP, 'links'))
            <div class="flex justify-center">
                {{ $logbooksPSCP->links() }}
            </div>
        @endif
    </div>

    {{-- Separator --}}
    <div class="relative mb-8">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-6 bg-gray-50 text-gray-500 font-medium">•••</span>
        </div>
    </div>

    {{-- Logbook Rotasi HBSCP --}}
    <div>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Logbook Rotasi HBSCP</h1>
            <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                {{ $logbooksHBSCP->count() }} Records
            </span>
        </div>

        <div class="overflow-x-auto bg-white shadow-md rounded-lg mb-4">
            <table class="min-w-full border-collapse border border-gray-200">
                <thead class="bg-green-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                                ID Logbook
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                Tanggal
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Status
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                Aksi
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logbooksHBSCP as $logbook)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-3 border-b text-sm font-medium text-gray-900">
                            <div class="flex items-center">
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">
                                    {{ $logbook->id }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 border-b text-sm text-gray-700">
                            <div class="flex flex-col">
                                <span class="font-medium">{{ $logbook->date->format('d M Y') }}</span>
                                <span class="text-xs text-gray-500">{{ $logbook->date->format('l') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 border-b text-sm">
                            @if($logbook->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Disetujui
                                </span>
                            @elseif($logbook->status === 'submitted')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Menunggu Persetujuan
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 border-b text-sm">
                            <a href="{{ route('supervisor.logbook-rotasihbscp.detail', $logbook->id) }}"
                               class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">Belum ada data logbook HBSCP</p>
                                <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah logbook dibuat</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination untuk HBSCP jika ada --}}
        @if(method_exists($logbooksHBSCP, 'links'))
            <div class="flex justify-center">
                {{ $logbooksHBSCP->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .max-w-7xl {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }

        table {
            font-size: 0.875rem;
        }

        .px-4 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .py-3 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        /* Hide less important columns on mobile */
        @media (max-width: 640px) {
            .hidden-on-mobile {
                display: none;
            }
        }
    }

    /* Smooth animations */
    .transition-colors {
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }

    /* Custom scrollbar for tables */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endsection
