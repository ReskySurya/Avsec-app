@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-2 sm:px-4 py-4 sm:py-6 lg:pt-20">

    {{-- Filter Section --}}
    <div class="mb-4 sm:mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <div class="flex items-center">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Filter Data</h3>
                    {{-- PERBAIKAN: Menggunakan url()->current() untuk link reset --}}
                    @if(request()->query('status'))
                    <a href="{{ url()->current() }}"
                        class="ml-3 text-xs sm:text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Clear Filter
                    </a>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                    <div class="flex items-center">
                        <label for="statusFilter" class="text-xs sm:text-sm text-gray-600 mr-2">Status:</label>
                        <select id="statusFilter"
                            class="text-xs sm:text-sm border border-gray-300 rounded-lg px-2 sm:px-3 py-1.5 sm:py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            onchange="applyFilter(this.value)">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request()->query('status') === 'draft' ? 'selected' : '' }}>Draft
                            </option>
                            <option value="submitted" {{ request()->query('status') === 'submitted' ? 'selected' : ''
                                }}>Menunggu Persetujuan</option>
                            <option value="approved" {{ request()->query('status') === 'approved' ? 'selected' : ''
                                }}>Selesai</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Active Filter Tags --}}
            @if(request()->query('status'))
            <div class="mt-3 flex flex-wrap gap-2">
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Status: {{ ucfirst(request()->query('status')) }}
                    <a href="{{ url()->current() }}" class="ml-1.5 inline-flex items-center">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </span>
            </div>
            @endif
        </div>
    </div>

    {{-- Checklist Penyisiran Ruang Tunggu --}}
    <div class="mb-8 sm:mb-10">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-3 sm:px-6 py-4 sm:py-5">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0">
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-white">Checklist Penyisiran Ruang Tunggu</h2>
                        {{-- PERBAIKAN: Tampilkan total data yang difilter --}}
                        <p class="text-blue-100 text-xs sm:text-sm mt-1">
                            {{ $checklistsPenyisiran->total() }} data ditemukan
                            @if(request()->query('status'))
                            (difilter berdasarkan status: "{{ ucfirst(request()->query('status')) }}")
                            @endif
                        </p>
                    </div>
                    <div
                        class="bg-blue-500 text-white px-3 py-1 sm:px-4 sm:py-2 rounded-full font-semibold text-xs sm:text-sm">
                        {{-- PERBAIKAN: Tampilkan jumlah data di halaman ini dan totalnya --}}
                        {{ $checklistsPenyisiran->firstItem() ?? 0 }}-{{ $checklistsPenyisiran->lastItem() ?? 0 }} dari
                        {{ $checklistsPenyisiran->total() }} Data
                    </div>
                </div>
            </div>

            {{-- Mobile: Card Layout --}}
            <div class="block lg:hidden p-2 sm:p-4 space-y-3">
                @forelse($checklistsPenyisiran as $checklist)
                <div
                    class="border border-gray-200 rounded-xl p-3 sm:p-4 bg-white shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <div class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-semibold text-xs">
                            ID: {{ $checklist->id }}
                        </div>
                        <div class="text-right">
                            @if($checklist->status === 'approved')
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Selesai
                            </span>
                            @elseif($checklist->status === 'submitted')
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 001.414-1.414L11 9.586V5z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Menunggu
                            </span>
                            @else
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Draft
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-xs sm:text-sm text-gray-600">
                            <svg class="w-3 h-3 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-semibold text-gray-900">{{
                                \Carbon\Carbon::parse($checklist->date)->translatedFormat('l') }}</span>
                        </div>
                        <div class="text-xs text-gray-500 ml-5">
                            {{ \Carbon\Carbon::parse($checklist->date)->translatedFormat('d F Y') }}
                        </div>

                        <div class="flex items-center text-xs sm:text-sm text-gray-600 mt-2">
                            <svg class="w-3 h-3 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-semibold">
                                Grup {{ ucfirst($checklist->grup) }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <a href="{{ route('supervisor.checklist-penyisiran.detail', $checklist->id) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-semibold text-sm shadow-sm">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Detail
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="mx-auto w-12 h-12 sm:w-16 sm:h-16 text-gray-300 mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-500 mb-1">Belum Ada Data</h3>
                    <p class="text-xs sm:text-sm text-gray-400">Checklist akan muncul di sini setelah dibuat</p>
                </div>
                @endforelse
            </div>

            {{-- Desktop: Table Layout --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-blue-50 border-b border-blue-200">
                        <tr>
                            <th
                                class="px-3 sm:px-4 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-blue-800">
                                <div class="flex items-center">
                                    <svg class="w-3 sm:w-4 h-3 sm:h-4 mr-1 sm:mr-2" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                        <path fill-rule="evenodd"
                                            d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    ID Checklist
                                </div>
                            </th>
                            <th
                                class="px-3 sm:px-4 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-blue-800">
                                <div class="flex items-center">
                                    <svg class="w-3 sm:w-4 h-3 sm:h-4 mr-1 sm:mr-2" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Tanggal
                                </div>
                            </th>
                            <th
                                class="px-3 sm:px-4 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-blue-800">
                                <div class="flex items-center">
                                    <svg class="w-3 sm:w-4 h-3 sm:h-4 mr-1 sm:mr-2" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Grup
                                </div>
                            </th>
                            <th
                                class="px-3 sm:px-4 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-blue-800">
                                <div class="flex items-center">
                                    <svg class="w-3 sm:w-4 h-3 sm:h-4 mr-1 sm:mr-2" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Status
                                </div>
                            </th>
                            <th
                                class="px-3 sm:px-4 py-3 sm:py-4 text-center text-xs sm:text-sm font-semibold text-blue-800">
                                <div class="flex items-center justify-center">
                                    <svg class="w-3 sm:w-4 h-3 sm:h-4 mr-1 sm:mr-2" fill="currentColor"
                                        viewBox="0 0 20 20">
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
                        @forelse($checklistsPenyisiran as $checklist)
                        <tr class="hover:bg-blue-50 transition-colors duration-200">
                            <td class="px-3 sm:px-4 py-3 sm:py-4">
                                <span
                                    class="bg-blue-100 text-blue-800 px-2 sm:px-3 py-1 rounded-full font-semibold text-xs sm:text-sm">
                                    {{ $checklist->id }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-4 py-3 sm:py-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900 text-sm sm:text-base">{{
                                        \Carbon\Carbon::parse($checklist->date)->translatedFormat('l') }}</span>
                                    <span class="text-xs sm:text-sm text-gray-500">{{
                                        \Carbon\Carbon::parse($checklist->date)->translatedFormat('d F Y') }}</span>
                                </div>
                            </td>
                            <td class="px-3 sm:px-4 py-3 sm:py-4">
                                <span
                                    class="bg-blue-100 text-blue-800 px-2 sm:px-3 py-1 rounded-full font-semibold text-xs sm:text-sm">
                                    {{ ucfirst($checklist->grup) }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-4 py-3 sm:py-4">
                                @if($checklist->status === 'approved')
                                <span
                                    class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <svg class="w-2 sm:w-3 h-2 sm:h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Selesai
                                </span>
                                @elseif($checklist->status === 'submitted')
                                <span
                                    class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    <svg class="w-2 sm:w-3 h-2 sm:h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 001.414-1.414L11 9.586V5z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Menunggu Persetujuan
                                </span>
                                @else
                                <span
                                    class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <svg class="w-2 sm:w-3 h-2 sm:h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Draft
                                </span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-4 py-3 sm:py-4 text-center">
                                <a href="{{ route('supervisor.checklist-penyisiran.detail', $checklist->id) }}"
                                    class="inline-flex items-center px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-semibold text-xs sm:text-sm shadow-sm">
                                    <svg class="w-2 sm:w-3 h-2 sm:h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 sm:py-12 text-center">
                                <svg class="mx-auto w-12 h-12 sm:w-16 sm:h-16 text-gray-300 mb-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <h3 class="text-base sm:text-lg font-semibold text-gray-500 mb-1">Belum Ada Data</h3>
                                <p class="text-xs sm:text-sm text-gray-400">Checklist akan muncul di sini setelah dibuat
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($checklistsPenyisiran->hasPages())
            <div class="mt-4 sm:mt-6 px-2 sm:px-4 pb-4 flex justify-center">
                {{-- Ini sudah benar karena controller sudah menambahkan appends() --}}
                {{ $checklistsPenyisiran->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    function applyFilter(status) {
        // Membuat objek URL dari alamat halaman saat ini
        const url = new URL(window.location);

        // Jika status memiliki nilai (bukan "Semua Status")
        if (status) {
            // Atur parameter 'status' di URL
            url.searchParams.set('status', status);
        } else {
            // Jika memilih "Semua Status", hapus parameter 'status'
            url.searchParams.delete('status');
        }

        // Selalu reset ke halaman pertama saat filter diubah
        url.searchParams.delete('page');

        // Arahkan browser ke URL yang baru
        window.location.href = url.toString();
    }
</script>

<style>
    /* Responsive Design */
    @media (max-width: 768px) {
        .max-w-6xl {
            max-width: 100%;
        }

        /* Smaller text on mobile */
        .text-base {
            font-size: 0.875rem;
        }

        .text-sm {
            font-size: 0.8125rem;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .text-lg {
            font-size: 1.125rem;
        }

        .text-xl {
            font-size: 1.25rem;
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

    .transition-shadow {
        transition: box-shadow 0.2s ease-in-out;
    }

    /* Custom shadows */
    .shadow-lg {
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
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

    .rounded-full {
        border-radius: 9999px;
    }

    /* Ensure proper spacing in tables */
    table {
        border-collapse: separate;
        border-spacing: 0;
    }

    /* Button hover effects */
    .hover\:bg-blue-700:hover {
        transform: translateY(-1px);
    }

    /* Status badge responsive sizing */
    @media (max-width: 640px) {
        .px-2.py-1 {
            padding: 0.25rem 0.5rem;
            font-size: 0.65rem;
        }

        .w-2.h-2 {
            width: 0.5rem;
            height: 0.5rem;
        }

        .w-3.h-3 {
            width: 0.75rem;
            height: 0.75rem;
        }
    }

    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection
