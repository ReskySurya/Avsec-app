@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 lg:pt-20">

    {{-- Buku Pemeriksaan Manual --}}
    <div class="mb-10">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-white mb-1">Buku Pemeriksaan Manual HBSCP</h2>
                    </div>
                    <div class="bg-blue-500 text-white px-4 py-2 rounded-full font-semibold text-sm">
                        {{ $manualBooksHBSCP->count() }} Data
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-blue-50 border-b border-blue-200">
                        <tr>
                            <th class="px-4 py-4 text-left text-sm font-semibold text-blue-800">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                        <path fill-rule="evenodd"
                                            d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    ID Checklist
                                </div>
                            </th>
                            <th class="px-4 py-4 text-left text-sm font-semibold text-blue-800">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Tanggal
                                </div>
                            </th>
                            <th class="px-4 py-4 text-left text-sm font-semibold text-blue-800">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Shift
                                </div>
                            </th>
                            <th class="px-4 py-4 text-left text-sm font-semibold text-blue-800">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Status
                                </div>
                            </th>
                            <th class="px-4 py-4 text-center text-sm font-semibold text-blue-800">
                                <div class="flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Aksi
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($manualBooksHBSCP as $HBSCP)
                        <tr class="hover:bg-blue-50 transition-colors duration-200">
                            <td class="px-4 py-4">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-semibold text-sm">
                                    {{ $HBSCP->id }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900 text-base">{{ \Carbon\Carbon::parse($HBSCP->date)->translatedFormat('l') }}</span>
                                    <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($HBSCP->date)->translatedFormat('d F Y') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-semibold text-sm">
                                    {{ ucfirst($HBSCP->shift) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @if($HBSCP->status === 'approved')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Selesai
                                </span>
                                @elseif($HBSCP->status === 'submitted')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 001.414-1.414L11 9.586V5z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Menunggu Persetujuan
                                </span>
                                @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Draft
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('supervisor.checklist-manualbook.detail', $HBSCP->id) }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-semibold text-sm shadow-sm">
                                    <svg class="w-3 h-3 mr-1 md:mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Detail</span>
                                    <span class="sm:hidden">Detail</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center">
                                <svg class="mx-auto w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-500 mb-2">Belum Ada Data</h3>
                                <p class="text-gray-400">Buku Pemeriksaan Manual HBSCP akan muncul di sini setelah dibuat</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($manualBooksHBSCP, 'links'))
            <div class="mt-6 px-4 flex justify-center">
                {{ $manualBooksHBSCP->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Separator --}}
    <div class="flex items-center my-8">
        <div class="flex-grow border-t border-gray-300"></div>
        <div class="mx-6">
            <div class="flex space-x-1">
                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
            </div>
        </div>
        <div class="flex-grow border-t border-gray-300"></div>
    </div>

    {{-- Buku Pemeriksaan Manual PSCP --}}
    <div class="mb-10">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-5">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-white mb-1">Buku Pemeriksaan Manual PSCP</h2>
                    </div>
                    <div class="bg-green-500 text-white px-4 py-2 rounded-full font-semibold text-sm">
                        {{ $manualBooksPSCP->count() }} Data
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-green-50 border-b border-green-200">
                        <tr>
                            <th class="px-4 py-4 text-left text-sm font-semibold text-green-800">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                        <path fill-rule="evenodd"
                                            d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    ID Checklist
                                </div>
                            </th>
                            <th class="px-4 py-4 text-left text-sm font-semibold text-green-800">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Tanggal
                                </div>
                            </th>
                            <th class="px-4 py-4 text-left text-sm font-semibold text-green-800">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Shift
                                </div>
                            </th>
                            <th class="px-4 py-4 text-left text-sm font-semibold text-green-800">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Status
                                </div>
                            </th>
                            <th class="px-4 py-4 text-center text-sm font-semibold text-green-800">
                                <div class="flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Aksi
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($manualBooksPSCP as $PSCP)
                        <tr class="hover:bg-green-50 transition-colors duration-200">
                            <td class="px-4 py-4">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-semibold text-sm">
                                    {{ $PSCP->id }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900 text-base">{{\Carbon\Carbon::parse($PSCP->date)->translatedFormat('l') }}</span>
                                    <span class="text-sm text-gray-500">{{\Carbon\Carbon::parse($PSCP->date)->translatedFormat('d F Y') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-semibold text-sm">
                                    {{ ucfirst($PSCP->shift) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @if($PSCP->status === 'approved')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Selesai
                                </span>
                                @elseif($PSCP->status === 'submitted')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 001.414-1.414L11 9.586V5z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Menunggu Persetujuan
                                </span>
                                @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Draft
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('supervisor.checklist-manualbook.detail', $PSCP->id) }}"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-semibold text-sm shadow-sm">
                                    <svg class="w-3 h-3 mr-1 md:mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Detail</span>
                                    <span class="sm:hidden">Detail</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center">
                                <svg class="mx-auto w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-500 mb-2">Belum Ada Data</h3>
                                <p class="text-gray-400">Buku Pemeriksaan Manual PSCP akan muncul di sini setelah dibuat</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($manualBooksPSCP, 'links'))
            <div class="mt-6 px-4 flex justify-center">
                {{ $manualBooksPSCP->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Responsive Design */
    @media (max-width: 768px) {
        .max-w-6xl {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .px-6 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .px-4 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .py-4 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        /* Smaller text on mobile */
        .text-base {
            font-size: 0.875rem;
        }

        .text-sm {
            font-size: 0.75rem;
        }

        .text-xl {
            font-size: 1.125rem;
        }

        /* Button sizing for mobile */
        .px-4.py-2 {
            padding: 0.5rem 0.75rem;
        }
    }

    /* Table scrollbar styling */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Smooth transitions */
    .transition-colors {
        transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
    }

    /* Custom shadows */
    .shadow-lg {
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Border radius consistency */
    .rounded-2xl {
        border-radius: 1rem;
    }

    .rounded-xl {
        border-radius: 0.75rem;
    }

    .rounded-lg {
        border-radius: 0.5rem;
    }

    /* Ensure proper spacing in tables */
    table {
        border-collapse: separate;
        border-spacing: 0;
    }

    /* Button hover effects */
    .hover\:bg-blue-700:hover,
    .hover\:bg-green-700:hover {
        transform: translateY(-1px);
    }

    /* Status badge responsive sizing */
    @media (max-width: 640px) {
        .px-3.py-1 {
            padding: 0.375rem 0.5rem;
            font-size: 0.65rem;
        }

        .w-3.h-3 {
            width: 0.6rem;
            height: 0.6rem;
        }
    }
</style>
@endsection
