@extends('layouts.app')

@section('title', 'Logbook Rotasi ')
@section('content')
<div x-data="{
        openLogbook: false,
        openEditLogbook: false,
        openFinishDialog: false,
        editLogbookData: {
            logbookID: null,
            date: '',
            grup: '',
            shift: ''
        },
    }" class="mx-auto p-0 sm:p-6 min-h-screen pt-5 lg:pt-20">

    <div class="mb-4">
        <a href="{{route('logbook.posjaga.list', ['id' => $posjaga]) }}"
            class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>

    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
        class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-4 sm:px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-blue-600 mx-4 sm:mx-0">
        <div class="flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-sm sm:text-base">{{ session('success') }}</span>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
        class="bg-gradient-to-r from-red-400 to-red-500 text-white px-4 sm:px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-red-600 mx-4 sm:mx-0">
        <div class="flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm sm:text-base">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <div
        class="bg-white shadow-xl rounded-none sm:rounded-2xl overflow-hidden mb-8 border-0 sm:border border-gray-100 mx-0 sm:mx-0">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-4 sm:px-6 py-4 sm:py-6 text-white">
            <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:justify-between sm:items-start">
                <div class="flex-1">
                    <h3 class="text-xl sm:text-2xl font-bold mb-1">Logbook Rotasi {{ strtoupper($typeForm) }}</h3>
                    <p class="text-blue-100 text-sm sm:text-base">Catatan aktivitas harian rotasi PSCP dan HBSCP.</p>
                </div>
                <button @click="openLogbook = true"
                    class="bg-white text-blue-600 hover:bg-blue-50 px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto text-sm sm:text-base">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Data
                </button>
            </div>
        </div>

        {{-- Include partial untuk menampilkan personil --}}
        {{-- @include('logbook.rotasi.partials.personil_rotasi', ['personil' => $personil]) --}}

        @if ($typeForm === 'pscp')
        @include('logbook.rotasi.partials.tabel_pscp', ['logbook' => $logbook])
        @else
        @include('logbook.rotasi.partials.tabel_hbscp', ['logbook' => $logbook])
        @endif
    </div>

    {{-- Modal Tambah Logbook --}}
    <div x-show="openLogbook" x-transition
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openLogbook = false"
            x-data="{ selectedRotasi: '{{ strtoupper($typeForm) }}', selectedTempatJaga: '' }"
            class="bg-white w-full max-w-md rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div
                class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-4 sm:p-6 rounded-t-2xl sticky top-0 z-10">
                <h2 class="text-xl sm:text-2xl font-bold">Tambah Entry Logbook Rotasi</h2>
                <p class="text-blue-100 text-sm sm:text-base">Tambahkan catatan rotasi harian Anda.</p>
            </div>

            <form action="{{ route('logbookRotasi.store') }}" method="POST" class="p-4 sm:p-6 space-y-4">
                @csrf
                {{-- Input Tanggal --}}
                <div>
                    <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" id="date" name="date" required
                        class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base"
                        readonly>
                    @error('date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Dropdown Area Rotasi --}}
                <input type="hidden" name="type" value="{{ strtoupper($typeForm) }}">

                {{-- Dropdown Tempat Jaga (Kondisional) --}}
                {{-- Dropdown Tempat Jaga (Kondisional) --}}
                <div x-show="selectedRotasi" x-transition>
                    <label for="tempat_jaga" class="block text-sm font-semibold text-gray-700 mb-2">Tempat Jaga</label>

                    {{-- SATU select untuk semua, isinya yang dinamis --}}
                    <select name="tempat_jaga" id="tempat_jaga" required x-model="selectedTempatJaga"
                        class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none text-sm sm:text-base">

                        {{-- Opsi placeholder yang dinamis --}}
                        <option value="">Pilih Tempat Jaga {{ strtoupper($typeForm) }}</option>

                        {{-- Logika Blade untuk menampilkan opsi yang relevan --}}
                        @if (strtoupper($typeForm) === 'PSCP')
                        @foreach($pscpOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                        @else
                        @foreach($hbscpOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                        @endif
                    </select>
                    @error('tempat_jaga') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Dropdown Personil --}}
                <div x-show="selectedRotasi" x-transition>
                    <label for="personil_id" class="block text-sm font-semibold text-gray-700 mb-2">Personil</label>
                    <input type="text" id="personil_id" name="personil_id" readonly
                        value="{{ Auth::user()->id }}" hidden>
                    <div class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none text-sm sm:text-base">
                        {{ Auth::user()->name }}
                    </div>
                    </input>
                    @error('personil_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Input Jam & Input Tambahan --}}
                <div class="space-y-4" x-show="selectedTempatJaga" x-transition>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-2">Jam
                                Mulai</label>
                            <input type="time" name="start_time" required
                                class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none text-sm sm:text-base">
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-2">Jam
                                Selesai</label>
                            <input type="time" name="end_time" required
                                class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none text-sm sm:text-base">
                        </div>
                    </div>

                    {{-- Counter untuk HHMD --}}
                    <div x-show="selectedRotasi === 'PSCP' && selectedTempatJaga === 'hhmd_petugas'"
                        class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t">
                        <div>
                            <label for="hhmd_random" class="block text-sm font-semibold text-gray-700 mb-2">HHMD
                                Random</label>
                            <input type="number" name="hhmd_random" value="0"
                                class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none text-sm sm:text-base">
                        </div>
                        <div>
                            <label for="hhmd_unpredictable" class="block text-sm font-semibold text-gray-700 mb-2">HHMD
                                Unpredictable</label>
                            <input type="number" name="hhmd_unpredictable" value="0"
                                class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none text-sm sm:text-base">
                        </div>
                    </div>

                    {{-- Counter untuk Manual Kabin --}}
                    <div x-show="selectedRotasi === 'PSCP' && selectedTempatJaga === 'manual_kabin_petugas'"
                        class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t">
                        <div>
                            <label for="cek_random_barang" class="block text-sm font-semibold text-gray-700 mb-2">Cek
                                Random Barang</label>
                            <input type="number" name="cek_random_barang" value="0"
                                class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none text-sm sm:text-base">
                        </div>
                        <div>
                            <label for="barang_unpredictable"
                                class="block text-sm font-semibold text-gray-700 mb-2">Barang Unpredictable</label>
                            <input type="number" name="barang_unpredictable" value="0"
                                class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none text-sm sm:text-base">
                        </div>
                    </div>
                </div>

                <div
                    class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-4 sticky bottom-0 bg-white">
                    <button type="button" @click="openLogbook = false"
                        class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium text-sm sm:text-base">Batal</button>
                    <button type="submit"
                        class="px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg text-sm sm:text-base">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Logbook --}}
    <div x-show="openEditLogbook" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEditLogbook = false"
            class="bg-white w-full max-w-md rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div
                class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-4 sm:p-6 rounded-t-2xl sticky top-0 z-10">
                <h2 class="text-xl sm:text-2xl font-bold">Edit Entry Logbook</h2>
                <p class="text-blue-100 text-sm sm:text-base">Ubah informasi logbook</p>
            </div>
            <form :action="'{{ url('/logbook/posjaga') }}/' + editLogbookData.logbookID" method="POST"
                class="p-4 sm:p-6">
                @csrf
                @method('PATCH')
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="date" required x-model="editLogbookData.date"
                        class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                    @error('date')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="edit_location_area_id" class="block text-sm font-semibold text-gray-700 mb-2">Area Pos
                        Jaga</label>
                    <select id="edit_location_area_id" name="location_area_id" required
                        x-model="editLogbookData.location_area_id"
                        class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                        <option value="">Pilih Lokasi</option>
                        @if(isset($locations))
                        @foreach($locations as $location)
                        <option value="{{ $location->id }}">
                            {{ $location->name }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                    @error('location_area_id')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Grup</label>
                    <select name="grup" required x-model="editLogbookData.grup"
                        class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                        <option value="">Pilih Grup</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </select>
                    @error('grup')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dinas / Shift</label>
                    <select name="shift" required x-model="editLogbookData.shift"
                        class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                        <option value="">Pilih Dinas/Shift</option>
                        <option value="Pagi">Pagi</option>
                        <option value="Malam">Malam</option>
                    </select>
                    @error('shift')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" @click="openEditLogbook = false"
                        class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium text-sm sm:text-base">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg text-sm sm:text-base">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
    @if(isset($logbook))
    <div x-show="openFinishDialog" x-transition x-on:transitionend="if(openFinishDialog){ initializeSignaturePad(); }"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openFinishDialog = false"
            class="bg-white w-full max-w-md rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div
                class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-4 sm:p-6 rounded-t-2xl sticky top-0 z-10">
                <h2 class="text-xl sm:text-2xl font-bold">Konfirmasi Penyelesaian Shift</h2>
                <p class="text-blue-100 text-sm sm:text-base">Lengkapi data untuk menyelesaikan logbook.</p>
            </div>

            <form action="{{ route('logbookRotasi.submit', ['id' => $logbook->id, 'posjaga' => $posjaga]) }}" method="POST" class="p-4 sm:p-6"
                onsubmit="return handleSignatureSubmit(event)">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    {{-- Tanda Tangan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan Digital Anda</label>
                        <div class="relative w-full h-32 sm:h-40 border-2 border-gray-200 rounded-lg bg-white">
                            <canvas id="signature-canvas" class="w-full h-full"></canvas>
                        </div>
                        <input type="hidden" name="signature" id="signature-data" value="">
                        <div class="flex justify-between items-center mt-1">
                            <span id="signature-status" class="text-xs text-gray-500">Belum ada tanda tangan</span>
                            <button type="button" class="text-sm text-blue-600 hover:text-blue-800"
                                onclick="clearSignature()">Hapus</button>
                        </div>
                    </div>

                    {{-- Pilih Supervisor --}}
                    <div>
                        <label for="approvedID" class="block text-sm font-semibold text-gray-700 mb-2">Diketahui Oleh
                            (Supervisor)</label>
                        <select name="approvedID" id="approvedID"
                            class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none text-sm sm:text-base"
                            required>
                            <option value="">Pilih Supervisor</option>
                            @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div
                    class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-6 mt-4 border-t sticky bottom-0 bg-white">
                    <button type="button" @click="openFinishDialog = false"
                        class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-medium text-sm sm:text-base">Batal</button>
                    <button type="submit"
                        class="px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 font-medium shadow-lg text-sm sm:text-base">Konfirmasi
                        & Kirim</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

{{-- Enhanced Responsive Styles --}}
<style>
    /* Base responsive improvements */
    @media (max-width: 640px) {
        .min-h-screen {
            min-height: 100vh;
        }

        /* Ensure full width on mobile */
        .mx-auto {
            margin-left: 0;
            margin-right: 0;
        }

        /* Remove border radius on mobile for full screen effect */
        .rounded-none {
            border-radius: 0;
        }
    }

    /* Scrollbar improvements */
    .scrollbar-thin {
        scrollbar-width: thin;
        scrollbar-color: #d1d5db #f3f4f6;
    }

    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 2px;
    }

    .scrollbar-thin::-webkit-scrollbar-track {
        background-color: #f3f4f6;
    }

    /* Modal improvements for mobile */
    @media (max-width: 640px) {
        .fixed.inset-0>div {
            margin: 1rem;
            max-height: calc(100vh - 2rem);
        }

        /* Sticky headers and footers in modals */
        .sticky {
            position: sticky;
            z-index: 20;
        }

        /* Better touch targets */
        button {
            min-height: 44px;
        }

        input,
        select {
            min-height: 44px;
        }
    }

    /* Enhanced table responsiveness */
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }

    /* Animation improvements */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }

    /* Focus improvements */
    .focus\:border-blue-500:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>

<script>
    // Enhanced responsive JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Set current date for date input
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${day}`;
        const dateInput = document.getElementById('date');
        if (dateInput) {
            dateInput.value = formattedDate;
        }

        // Form validation
        const routeLogbookStore = "{{ route('logbook.store') }}";
        const form = document.querySelector(`form[action="${routeLogbookStore}"]`);
        if (form) {
            form.addEventListener('submit', function(e) {
                const grup = form.querySelector('#grup')?.value;
                const shift = form.querySelector('#shift')?.value;

                if (!grup || !shift) {
                    e.preventDefault();
                    alert('Silakan lengkapi semua field yang diperlukan');
                    return false;
                }
            });
        }

        // Reset form when modal is closed
        const resetForm = () => {
            if (form) {
                form.reset();
                if (dateInput) {
                    dateInput.value = formattedDate;
                }
            }
        };

        // Add event listeners for modal close buttons
        const closeButtons = document.querySelectorAll('[x-on\\:click="openLogbook = false"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', resetForm);
        });

        // Enhanced mobile viewport handling
        const setViewportHeight = () => {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        };

        window.addEventListener('resize', setViewportHeight);
        window.addEventListener('orientationchange', setViewportHeight);
        setViewportHeight();
    });

    // Signature Pad Logic (Enhanced for mobile)
    let signaturePadInstance;

    function initializeSignaturePad() {
        const canvas = document.getElementById('signature-canvas');
        if (!canvas) {
            console.error("Canvas element not found!");
            return;
        }

        // Wait for canvas to be ready
        if (canvas.offsetWidth === 0) {
            setTimeout(initializeSignaturePad, 100);
            return;
        }

        // Clear existing instance if any
        if (signaturePadInstance) {
            signaturePadInstance.clear();
            return;
        }

        // Initialize signature pad
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        signaturePadInstance = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)',
            minWidth: 1,
            maxWidth: 3,
            throttle: 16,
            minDistance: 5
        });

        const statusEl = document.getElementById('signature-status');
        signaturePadInstance.addEventListener("endStroke", () => {
            if (statusEl) {
                statusEl.textContent = 'Tanda tangan dibuat';
                statusEl.className = 'text-xs text-green-600';
            }
        });

        // Enhanced touch handling for mobile
        canvas.style.touchAction = 'none';
    }

    function clearSignature() {
        initializeSignaturePad();

        if (signaturePadInstance) {
            const statusEl = document.getElementById('signature-status');
            if (statusEl) {
                statusEl.textContent = 'Belum ada tanda tangan';
                statusEl.className = 'text-xs text-gray-500';
            }
            document.getElementById('signature-data').value = '';
        }
    }

    function handleSignatureSubmit(event) {
        const approvedID = document.getElementById('approvedID').value;

        if (!approvedID) {
            alert('Mohon lengkapi pilihan Supervisor.');
            event.preventDefault();
            return false;
        }

        if (!signaturePadInstance || signaturePadInstance.isEmpty()) {
            alert('Mohon berikan tanda tangan Anda.');
            event.preventDefault();
            return false;
        }

        document.getElementById('signature-data').value = signaturePadInstance.toDataURL('image/png');
        return confirm('Apakah Anda yakin ingin menyelesaikan logbook ini?');
    }

    // Alpine.js integration
    document.addEventListener('alpine:init', () => {
        Alpine.watch('openFinishDialog', (value) => {
            if (value) {
                setTimeout(initializeSignaturePad, 50);
            }
        });
    });
</script>
@endsection