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

    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
        class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-blue-600">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif
    @if(session('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
        class="bg-gradient-to-r from-red-400 to-red-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-red-600">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-6 text-white">
            <div class="flex flex-col sm:flex-row justify-between items-start">
                <div>
                    <h3 class="text-2xl font-bold mb-1">Logbook Rotasi</h3>
                    <p class="text-blue-100">Catatan aktivitas harian rotasi PSCP dan HBSCP.</p>
                </div>
                <button @click="openLogbook = true"
                    class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 mt-3 sm:mt-0 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Data
                </button>
            </div>
        </div>

        {{-- === KONTEN UTAMA YANG DIRUBAH (TAB & TABEL DINAMIS) === --}}
        <div class="p-6">
            <div class="mb-4 border-b border-gray-200">
                <div class="flex space-x-2">
                    <a href="{{ route('logbookRotasi.index', ['type' => 'pscp']) }}"
                        class="px-4 py-2 text-sm font-medium rounded-t-lg transition-colors duration-200
                              {{ $typeForm === 'pscp' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                        Rotasi PSCP
                    </a>
                    <a href="{{ route('logbookRotasi.index', ['type' => 'hbscp']) }}"
                        class="px-4 py-2 text-sm font-medium rounded-t-lg transition-colors duration-200
                              {{ $typeForm === 'hbscp' ? 'bg-teal-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                        Rotasi HBSCP
                    </a>
                </div>
            </div>

            @if ($typeForm === 'pscp')
            @include('logbook.rotasi.partials.tabel_pscp', ['logbook' => $logbook])
            @else
            @include('logbook.rotasi.partials.tabel_hbscp', ['logbook' => $logbook])
            @endif
        </div>
    </div>

    {{-- Modal Tambah Logbook --}}
    <div x-show="openLogbook" x-transition
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openLogbook = false" x-data="{ selectedRotasi: '', selectedTempatJaga: '' }"
            class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Tambah Entry Logbook Rotasi</h2>
                <p class="text-blue-100">Tambahkan catatan rotasi harian Anda.</p>
            </div>

            <form action="{{ route('logbookRotasi.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                {{-- Input Tanggal --}}
                <div>
                    <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" id="date" name="date" required
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                        readonly>
                    @error('date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Dropdown Area Rotasi --}}
                <div>
                    <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Area Rotasi</label>
                    <select id="type" name="type" required x-model="selectedRotasi" @change="selectedTempatJaga = ''"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                        <option value="">Pilih Rotasi</option>
                        <option value="PSCP">PSCP</option>
                        <option value="HBSCP">HBSCP</option>
                    </select>
                    @error('type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Dropdown Tempat Jaga (Kondisional) --}}
                <div x-show="selectedRotasi" x-transition>
                    <label for="tempat_jaga" class="block text-sm font-semibold text-gray-700 mb-2">Tempat Jaga</label>

                    {{-- Opsi untuk PSCP --}}
                    <select name="tempat_jaga" x-show="selectedRotasi === 'PSCP'" x-model="selectedTempatJaga"
                        :disabled="selectedRotasi !== 'PSCP'" {{-- <<< TAMBAHKAN INI --}}
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none">
                        <option value="">Pilih Tempat Jaga PSCP</option>
                        @foreach($pscpOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    {{-- Opsi untuk HBSCP --}}
                    <select name="tempat_jaga" x-show="selectedRotasi === 'HBSCP'" x-model="selectedTempatJaga"
                        :disabled="selectedRotasi !== 'HBSCP'" {{-- <<< TAMBAHKAN INI --}}
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none">
                        <option value="">Pilih Tempat Jaga HBSCP</option>
                        @foreach($hbscpOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Input Jam & Input Tambahan --}}
                <div class="space-y-4" x-show="selectedTempatJaga" x-transition>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-2">Jam
                                Mulai</label>
                            <input type="time" name="start_time" required
                                class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-2">Jam
                                Selesai</label>
                            <input type="time" name="end_time" required
                                class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>

                    {{-- Counter untuk HHMD --}}
                    <div x-show="selectedRotasi === 'PSCP' && selectedTempatJaga === 'hhmd_petugas'"
                        class="grid grid-cols-2 gap-4 pt-2 border-t">
                        <div>
                            <label for="hhmd_random" class="block text-sm font-semibold text-gray-700 mb-2">HHMD
                                Random</label>
                            <input type="number" name="hhmd_random" value="0"
                                class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label for="hhmd_unpredictable" class="block text-sm font-semibold text-gray-700 mb-2">HHMD
                                Unpredictable</label>
                            <input type="number" name="hhmd_unpredictable" value="0"
                                class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>

                    {{-- Counter untuk Manual Kabin --}}
                    <div x-show="selectedRotasi === 'PSCP' && selectedTempatJaga === 'manual_kabin_petugas'"
                        class="grid grid-cols-2 gap-4 pt-2 border-t">
                        <div>
                            <label for="cek_random_barang" class="block text-sm font-semibold text-gray-700 mb-2">Cek
                                Random Barang</label>
                            <input type="number" name="cek_random_barang" value="0"
                                class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label for="barang_unpredictable"
                                class="block text-sm font-semibold text-gray-700 mb-2">Barang Unpredictable</label>
                            <input type="number" name="barang_unpredictable" value="0"
                                class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" @click="openLogbook = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">Batal</button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">Simpan</button>
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
        <div @click.away="openEditLogbook = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Edit Entry Logbook</h2>
                <p class="text-blue-100">Ubah informasi logbook</p>
            </div>
            <form :action="'{{ url('/logbook/posjaga') }}/' + editLogbookData.logbookID" method="POST" class="p-6">
                @csrf
                @method('PATCH')
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="date" required x-model="editLogbookData.date"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                    @error('date')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="edit_location_area_id" class="block text-sm font-semibold text-gray-700 mb-2">Area Pos
                        Jaga</label>
                    <select id="edit_location_area_id" name="location_area_id" required
                        x-model="editLogbookData.location_area_id"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
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
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
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
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                        <option value="">Pilih Dinas/Shift</option>
                        <option value="Pagi">Pagi</option>
                        <option value="Malam">Malam</option>
                    </select>
                    @error('shift')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openEditLogbook = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
    @if(isset($logbook))
    <div x-show="openFinishDialog" x-transition
        x-on:transitionend="if(openFinishDialog){ initializeSignaturePad(); }"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openFinishDialog = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Konfirmasi Penyelesaian Shift</h2>
                <p class="text-blue-100">Lengkapi data untuk menyelesaikan logbook.</p>
            </div>

            <form action="{{ route('logbookRotasi.submit', $logbook->id) }}" method="POST" class="p-6"
                onsubmit="return handleSignatureSubmit(event)">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    {{-- Tanda Tangan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan Digital Anda</label>
                        <div class="relative w-full h-40 border-2 border-gray-200 rounded-lg bg-white">
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
                            class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none"
                            required>
                            <option value="">Pilih Supervisor</option>
                            @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 mt-4 border-t">
                    <button type="button" @click="openFinishDialog = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-medium">Batal</button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 font-medium shadow-lg">Konfirmasi
                        & Kirim</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

{{-- Custom Scrollbar Styles --}}
<style>
    .scrollbar-thin {
        scrollbar-width: thin;
    }

    .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
    }

    .scrollbar-track-gray-100::-webkit-scrollbar-track {
        background-color: #f3f4f6;
    }

    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 3px;
    }

    .scrollbar-thin::-webkit-scrollbar-track {
        background-color: #f3f4f6;
    }
</style>

<script>
    // Bagian 1: Logika yang berjalan setelah halaman siap (DOM)
    document.addEventListener('DOMContentLoaded', function() {
        // Setel tanggal saat ini untuk input tanggal di modal "Tambah Data"
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${day}`;
        const dateInput = document.getElementById('date');
        if (dateInput) {
            dateInput.value = formattedDate;
        }

        // Validasi form "Tambah Data"
        const routeLogbookStore = "{{ route('logbook.store') }}";
        const form = document.querySelector(`form[action="${routeLogbookStore}"]`);
        if (form) {
            form.addEventListener('submit', function(e) {
                const grup = form.querySelector('#grup').value;
                const shift = form.querySelector('#shift').value;

                if (!grup || !shift) {
                    e.preventDefault();
                    alert('Silakan lengkapi semua field yang diperlukan');
                    return false;
                }
            });
        }

        // Reset form "Tambah Data" saat modal ditutup
        const resetForm = () => {
            if (form) {
                form.reset();
                if (dateInput) {
                    dateInput.value = formattedDate;
                }
            }
        };

        // Tambahkan event listener untuk tombol tutup modal "Tambah Data"
        const closeButtons = document.querySelectorAll('[x-on\\:click="openLogbook = false"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', resetForm);
        });
    });

    // Bagian 2: Logika untuk Signature Pad & Modal Konfirmasi
    let signaturePadInstance;

    function initializeSignaturePad() {
        const canvas = document.getElementById('signature-canvas');
        if (!canvas) {
            console.error("Elemen canvas tidak ditemukan!");
            return;
        }

        // --- PERUBAHAN UTAMA ---
        // Cek jika canvas sudah siap (memiliki dimensi). Jika tidak, tunggu dan coba lagi.
        if (canvas.offsetWidth === 0) {
            setTimeout(initializeSignaturePad, 100); // Coba lagi setelah 100ms
            return;
        }

        // Hindari membuat instance baru jika sudah ada. Cukup bersihkan.
        if (signaturePadInstance) {
            signaturePadInstance.clear();
            return;
        }

        // Inisialisasi hanya jika belum ada instance
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        signaturePadInstance = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)' // Latar belakang putih
        });

        const statusEl = document.getElementById('signature-status');
        signaturePadInstance.addEventListener("endStroke", () => {
            if (statusEl) {
                statusEl.textContent = 'Tanda tangan dibuat';
                statusEl.className = 'text-xs text-green-600';
            }
        });
    }

    function clearSignature() {
        // Cukup panggil inisialisasi. Fungsi ini sudah cerdas untuk menangani
        // apakah perlu membuat instance baru atau hanya membersihkan yang sudah ada.
        initializeSignaturePad();

        // Atur ulang status teks setelah dibersihkan
        if(signaturePadInstance){
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

        // Pastikan signature pad sudah diinisialisasi sebelum cek isEmpty
        if (!signaturePadInstance || signaturePadInstance.isEmpty()) {
            alert('Mohon berikan tanda tangan Anda.');
            event.preventDefault();
            return false;
        }

        document.getElementById('signature-data').value = signaturePadInstance.toDataURL('image/png');
        return confirm('Apakah Anda yakin ingin menyelesaikan logbook ini?');
    }

    // Listener Alpine.js (tidak berubah, tetapi sekarang memanggil fungsi yang lebih andal)
    document.addEventListener('alpine:init', () => {
        Alpine.watch('openFinishDialog', (value) => {
            if (value) {
                // tunggu animasi modal selesai, lalu inisialisasi
                setTimeout(initializeSignaturePad, 50);
            }
        });
    });
</script>
@endsection
