@extends('layouts.app')

@section('title', 'Check List Penyisiran Daerah Steril Ruang Tunggu')

@section('content')
<div class="mx-auto p-4 sm:p-6 min-h-screen pt-5 sm:pt-20" x-data="{
    openFinishDialog: false 
     }">

    {{-- Alert Sukses --}}
    @if(session('success'))
    <div class=" bg-green-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg">
        {{ session('success') }}
    </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
    <div class="bg-red-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-6 text-white">
            <div class="flex items-center justify-between">
                <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                    class="w-20 h-20 mb-2 sm:mb-0">
                <div class="text-center">
                    <h1 class="text-2xl font-bold">CHECK LIST PENGECEKAN HARIAN</h1>
                    <h2 class="text-xl font-semibold">CHECK LIST PENYISIRAN DAERAH STERIL RUANG TUNGGU</h2>
                    <p class="text-blue-100">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
                </div>
                <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo" class="w-24 h-24 mt-2 sm:mt-0">
            </div>
        </div>

        {{-- FORM --}}
        <form id="checklistForm" method="POST" action="{{ route('checklist.penyisiran.store') }}" class="p-6 space-y-6">
            @csrf
            @if($checklist->exists)
            @method('PUT')
            @endif

            {{-- Header Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg">
                {{-- Hari/Tanggal --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Hari/Tanggal:</label>
                    <input type="date" name="date"
                        value="{{ old('date', $checklist->exists ? $checklist->date?->format('Y-m-d') : now()->format('Y-m-d')) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>

                {{-- Jam --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Jam:</label>
                    <input type="time" name="jam"
                        value="{{ old('jam', $checklist->exists ? $checklist->date?->format('H:i') : now()->format('H:i')) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Team --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Grup:</label>
                    <select name="grup" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Grup</option>
                        <option value="A" {{ old('grup', $checklist->grup ?? '') == 'A' ? 'selected' : '' }}>Grup A</option>
                        <option value="B" {{ old('grup', $checklist->grup ?? '') == 'B' ? 'selected' : '' }}>Grup B</option>
                        <option value="C" {{ old('grup', $checklist->grup ?? '') == 'C' ? 'selected' : '' }}>Grup C</option>
                    </select>
                </div>

                {{-- Nama Petugas --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Nama Petugas:</label>
                    <input type="text" name="officer_name" value="{{ $officers->name ?? '' }}" readonly
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100">
                    <input type="hidden" name="sender_id" value="{{ $officers->id ?? '' }}">
                </div>
            </div>

            {{-- Checklist Table --}}
            <div class="mt-8">
                <table class="w-full border-collapse border-2 border-gray-400">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="border-2 border-gray-400 px-4 py-3 text-center font-bold w-12" rowspan="2">NO</th>
                            <th class="border-2 border-gray-400 px-4 py-3 text-center font-bold" rowspan="2">KETERANGAN</th>
                            <th class="border-2 border-gray-400 px-4 py-3 text-center font-bold" colspan="2">TEMUAN</th>
                            <th class="border-2 border-gray-400 px-4 py-3 text-center font-bold" colspan="2">KONDISI</th>
                            <th class="border-2 border-gray-400 px-4 py-3 text-center font-bold" rowspan="2">Catatan</th>
                        </tr>
                        <tr class="bg-blue-500 text-white">
                            <th class="border-2 border-gray-400 px-2 py-2 text-center font-bold w-20">YA</th>
                            <th class="border-2 border-gray-400 px-2 py-2 text-center font-bold w-20">TIDAK</th>
                            <th class="border-2 border-gray-400 px-2 py-2 text-center font-bold w-20">BAIK</th>
                            <th class="border-2 border-gray-400 px-2 py-2 text-center font-bold w-20">RUSAK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($penyisiranChecklist as $item)
                        @php
                        $itemKey = $item['id'];
                        $existingDetail = $checklist->exists ? $checklist->details->firstWhere('item_key', $itemKey) : null;
                        @endphp
                        <tr class="hover:bg-blue-50/50 transition-colors duration-200 border-b border-gray-100">
                            <td class="border-2 border-gray-400 px-4 py-3 text-center font-bold">{{ $no }}.</td>
                            <td class="border-2 border-gray-400 px-4 py-3 font-medium">{{ $item['name'] }}</td>
                            <td class="border-2 border-gray-400 px-2 py-3 text-center">
                                <input type="radio" name="items[{{ $itemKey }}][temuan_ya]" value="1"
                                    {{ old("items.{$itemKey}.temuan_ya", $existingDetail?->temuan_ya ?? false) ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 focus:ring-blue-500 scale-125 cursor-pointer">
                            </td>
                            <td class="border-2 border-gray-400 px-2 py-3 text-center">
                                <input type="radio" name="items[{{ $itemKey }}][temuan_tidak]" value="0"
                                    {{ old("items.{$itemKey}.temuan_tidak", $existingDetail?->temuan_tidak ?? false) ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 focus:ring-blue-500 scale-125 cursor-pointer">
                            </td>
                            <td class="border-2 border-gray-400 px-2 py-3 text-center">
                                <input type="radio" name="items[{{ $itemKey }}][kondisi_baik]" value="1"
                                    {{ old("items.{$itemKey}.kondisi_baik", $existingDetail?->kondisi_baik ?? false) ? 'checked' : '' }}
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 scale-125 cursor-pointer">
                            </td>
                            <td class="border-2 border-gray-400 px-2 py-3 text-center">
                                <input type="radio" name="items[{{ $itemKey }}][kondisi_rusak]" value="1"
                                    {{ old("items.{$itemKey}.kondisi_rusak", $existingDetail?->kondisi_rusak ?? false) ? 'checked' : '' }}
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 scale-125 cursor-pointer">
                            </td>
                            <td class="border-2 border-gray-400 px-4 py-3 font-medium">
                                <textarea name="items[{{ $itemKey }}][notes]" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Opsional..."
                                    {{ old("items.{$itemKey}.notes", $existingDetail?->notes ?? '') }}></textarea>
                            </td>
                        </tr>
                        @php $no++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Keterangan Section --}}
            <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4">KETERANGAN:</h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li>• No. 1, 2 dan 3 di isi tanda centang pada kolom TEMUAN</li>
                    <li>• No. 4 di isi tanda centang pada kolom KONDISI</li>
                    <li>• Tulis hasil temuan pada kolom CATATAN</li>
                </ul>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex justify-end space-x-4 mt-8">
                <button type="button" onclick="history.back()"
                    class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Kembali
                </button>

                <button @click="openFinishDialog = true; $nextTick(() => initializeSignaturePad())"
                    class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                    Simpan Checklist
                </button>
            </div>

            <div x-show="openFinishDialog" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                style="display: none;">

                <div @click.away="openFinishDialog = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
                    <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                        <h2 class="text-2xl font-bold">Konfirmasi Selesai</h2>
                        <p class="text-blue-100">Konfirmasi penyelesaian checklist hari ini</p>
                    </div>

                    <form
                        action="{{ route('checklist.penyisiran.store') }}"
                        method="POST" class="p-6" onsubmit="return handleSignatureSubmit(event)">
                        @csrf
                        @method('POST')
                        <div class="mb-6">
                            <p class="text-gray-700 text-lg mb-4">Apakah Anda yakin sudah menyelesaikan checklist hari
                                ini?</p>

                            <div class="border-2 border-gray-200 rounded-xl p-4 mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan Digital</label>

                                <!-- Container untuk signature pad -->
                                <div class="relative w-full h-48 border border-gray-300 rounded-lg bg-white">
                                    @if($checklist->senderSignature)
                                    <!-- Tampilkan tanda tangan yang sudah ada -->
                                    <img src="data:image/png;base64,{{ $checklist->senderSignature }}"
                                        alt="Tanda tangan sudah ada" class="w-full h-full object-contain">
                                    @else
                                    <!-- Canvas untuk tanda tangan baru -->
                                    <canvas id="signature-canvas" class="w-full h-full"></canvas>
                                    @endif
                                </div>

                                <input type="hidden" name="signature" id="signature-data"
                                    value="{{ $checklist->senderSignature ?? '' }}">

                                <div class="flex justify-between items-center mt-2">
                                    <span id="signature-status" class="text-xs text-gray-500">
                                        {{ $checklist->senderSignature ? 'Tanda tangan sudah ada' : 'Belum ada tanda tangan'
                                    }}
                                    </span>
                                    @if(!$checklist->senderSignature)
                                    <button type="button"
                                        class="text-sm text-blue-600 hover:text-blue-800 transition-colors"
                                        onclick="clearSignature()">
                                        Clear
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 sm:mb-4">
                            <label for="received_id"
                                class="block text-gray-700 font-bold text-xs sm:text-base mb-1 sm:mb-2">Pilih
                                Officer yang Menerima:
                            </label>

                            @php
                            $officers = \App\Models\User::whereHas('role', function ($query) {
                            $query->where('name', \App\Models\Role::OFFICER);
                            })
                            ->where('id', '!=', auth()->id()) // Mengecualikan user yang sedang login
                            ->get();
                            @endphp

                            <select name="received_id" id="received_id"
                                class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base" required>
                                <option value="">Pilih Officer</option>
                                @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" {{ old('received_id', $checklist->receivedID ?? '') ==
                                $officer->id ? 'selected' : '' }}>
                                    {{ $officer->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2 sm:mb-4">
                            <label for="approved_id"
                                class="block text-gray-700 font-bold text-xs sm:text-base mb-1 sm:mb-2">Pilih
                                Supervisor yang Mengetahui:
                            </label>

                            @php
                            $supervisors = \App\Models\User::whereHas('role', function ($query) {
                            $query->where('name', \App\Models\Role::SUPERVISOR);
                            })->get();
                            @endphp

                            <select name="approved_id" id="approved_id"
                                class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base" required>
                                <option value="">Pilih Supervisor</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" {{ old('approved_id', $checklist->approvedID ?? '') ==
                                $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="openFinishDialog = false"
                                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">
                                {{ $checklist->senderSignature ? 'Update Konfirmasi' : 'Konfirmasi & Kirim' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-uncheck opposing checkboxes
        const checkboxes = document.querySelectorAll('input[type="radio"]');

        checkboxes.forEach(radio => {
            radio.addEventListener('change', function() {
                const name = this.name;
                const itemKey = name.match(/items\[([^\]]+)\]/)[1];

                if (name.includes('[temuan_ya]') && this.checked) {
                    const temuanTidak = document.querySelector(`input[name="items[${itemKey}][temuan_tidak]"]`);
                    if (temuanTidak) temuanTidak.checked = false;
                } else if (name.includes('[temuan_tidak]') && this.checked) {
                    const temuanYa = document.querySelector(`input[name="items[${itemKey}][temuan_ya]"]`);
                    if (temuanYa) temuanYa.checked = false;
                } else if (name.includes('[kondisi_baik]') && this.checked) {
                    const kondisiRusak = document.querySelector(`input[name="items[${itemKey}][kondisi_rusak]"]`);
                    if (kondisiRusak) kondisiRusak.checked = false;
                } else if (name.includes('[kondisi_rusak]') && this.checked) {
                    const kondisiBaik = document.querySelector(`input[name="items[${itemKey}][kondisi_baik]"]`);
                    if (kondisiBaik) kondisiBaik.checked = false;
                }
            });
        });

        // Form validation
        // document.getElementById('checklistForm').addEventListener('submit', function(e) {
        //     const date = document.querySelector('input[name="date"]').value;
        //     const grup = document.querySelector('select[name="grup"]').value;

        //     if (!date || !grup) {
        //         e.preventDefault();
        //         alert('Mohon lengkapi semua field yang wajib diisi!');
        //         return false;
        //     }
        // });
    });

    let signaturePadInstance;

    function initializeSignaturePad() {
        // Jika sudah ada tanda tangan di database, tidak perlu inisialisasi
        const existingSignature = document.getElementById('signature-data').value;
        if (existingSignature && existingSignature.startsWith('data:image')) {
            return;
        }

        // Hindari inisialisasi ulang jika sudah ada
        if (signaturePadInstance) {
            signaturePadInstance.clear();
            return;
        }

        const canvas = document.getElementById('signature-canvas');
        if (!canvas) {
            console.error('Elemen canvas untuk tanda tangan tidak ditemukan!');
            return;
        }

        // Sesuaikan ukuran canvas untuk layar HiDPI (Retina)
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        // Inisialisasi SignaturePad
        signaturePadInstance = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)' // Penting agar background tidak transparan
        });

        const statusEl = document.getElementById('signature-status');

        // Update status saat pengguna selesai menulis
        signaturePadInstance.addEventListener("endStroke", () => {
            if (statusEl) {
                statusEl.textContent = 'Tanda tangan tersimpan';
                statusEl.className = 'text-xs text-green-600 font-semibold';
            }
        });
    }

    function clearSignature() {
        // Jika signature pad belum diinisialisasi, inisialisasi dulu
        if (!signaturePadInstance) {
            initializeSignaturePad();
        }

        if (signaturePadInstance) {
            signaturePadInstance.clear();
            const statusEl = document.getElementById('signature-status');
            if (statusEl) {
                statusEl.textContent = 'Belum ada tanda tangan';
                statusEl.className = 'text-xs text-gray-500';
            }
            document.getElementById('signature-data').value = '';
        }
    }

    function handleSignatureSubmit(event) {
        const signatureDataEl = document.getElementById('signature-data');
        const existingSignature = signatureDataEl.value;

        // 1. Validasi pemilihan officer dan supervisor (selalu dilakukan)
        const received_id = document.getElementById('received_id').value;
        const approved_id = document.getElementById('approved_id').value;

        if (!received_id) {
            alert('Mohon pilih Officer yang akan menerima checklist.');
            event.preventDefault();
            return false;
        }

        if (!approved_id) {
            alert('Mohon pilih Supervisor yang mengetahui.');
            event.preventDefault();
            return false;
        }

        // 2. Cek apakah sudah ada tanda tangan di database
        if (existingSignature && existingSignature.trim() !== '') {
            // Jika sudah ada tanda tangan di database, langsung submit
            const confirmSubmit = confirm('Apakah Anda yakin ingin memperbarui konfirmasi checklist ini?');
            if (!confirmSubmit) {
                event.preventDefault();
            }
            return confirmSubmit;
        }

        // 3. Jika tidak ada tanda tangan di database, validasi signature pad
        if (!signaturePadInstance) {
            initializeSignaturePad();
        }

        if (signaturePadInstance.isEmpty()) {
            alert('Mohon berikan tanda tangan Anda sebelum melanjutkan.');
            event.preventDefault();
            return false;
        }

        // 4. Proses tanda tangan baru
        try {
            const signatureData = signaturePadInstance.toDataURL('image/png');
            if (!signatureData.startsWith('data:image/png;base64,')) {
                throw new Error('Format tanda tangan tidak valid');
            }

            signatureDataEl.value = signatureData;

            const confirmSubmit = confirm('Apakah Anda yakin ingin menyelesaikan checklist ini? Tindakan ini tidak dapat dibatalkan.');
            if (!confirmSubmit) {
                event.preventDefault();
            }
            return confirmSubmit;
        } catch (error) {
            alert('Terjadi kesalahan saat memproses tanda tangan: ' + error.message);
            event.preventDefault();
            return false;
        }
    }
    document.addEventListener('alpine:init', () => {
        Alpine.data('checklist', () => ({
            openFinishDialog: false,
            // ... data alpine lainnya

            initFinishDialog() {
                this.$watch('openFinishDialog', (value) => {
                    if (value) {
                        // Tunggu hingga modal selesai render
                        setTimeout(initializeSignaturePad, 100);
                    }
                });
            }
        }));
    });
</script>

@endsection