@extends('layouts.app')

@section('title', 'Check List Penyisiran Daerah Steril Ruang Tunggu')

@section('content')
<div class="mx-auto p-4 sm:p-6 min-h-screen pt-5 sm:pt-20" x-data="checklistForm()">

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

    {{-- Menampilkan Error Validasi --}}
    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6 shadow-lg" role="alert">
        <strong class="font-bold">Oops! Terjadi kesalahan.</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-6 text-white">
            <div class="flex items-center justify-between">
                <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                    class="w-20 h-20 mb-2 sm:mb-0">
                <div class="text-center">
                    <h1 class="text-2xl font-bold">CHECK LIST PENGECEKAN HARIAN</h1>
                    <h2 class="text-xl font-semibold">PENYISIRAN DAERAH STERIL RUANG TUNGGU</h2>
                    <p class="text-blue-100">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
                </div>
                <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo" class="w-24 h-24 mt-2 sm:mt-0">
            </div>
        </div>

        {{-- FORM --}}
        <form id="checklistForm" method="POST" action="{{ route('checklist.penyisiran.store') }}" @submit.prevent="handleFormSubmit" class="p-6 space-y-6">
            @csrf
            @if($checklist->exists)
            @method('PUT')
            @endif

            {{-- Hidden signature input --}}
            <input type="hidden" name="signature" id="signature-data">

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
                    <input type="text" name="officer_name" value="{{ $currentOfficer->name ?? '' }}" readonly
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100">
                    <input type="hidden" name="sender_id" value="{{ $currentOfficer->id ?? '' }}">
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
                    {{-- Bagian table form yang perlu diperbaiki --}}
                    <tbody>
                        @php $no = 1; @endphp
                        @php $noCategory = 1; @endphp
                        @foreach($penyisiranChecklist as $category => $items)
                        <tr class="bg-blue-100">
                            <td colspan="7" class="border-2 border-gray-400 px-4 py-2 font-bold text-blue-800 text-left">{{ $noCategory }}. {{ strtoupper($category) }}</td>
                        </tr>
                        @foreach($items as $item)
                        @php
                        $itemKey = $item->id;
                        $existingDetail = $checklist->exists ? $checklist->details->firstWhere('checklist_item_id', $itemKey) : null;
                        @endphp
                        <tr class="hover:bg-blue-50/50 transition-colors duration-200 border-b border-gray-100">
                            <td class="border-2 border-gray-400 px-4 py-3 text-center font-bold">{{ $no }}.</td>
                            <td class="border-2 border-gray-400 px-4 py-3 font-medium">{{ $item->name }}</td>

                            {{-- TEMUAN - Radio buttons dengan name yang sama untuk saling eksklusif --}}
                            <td class="border-2 border-gray-400 px-2 py-3 text-center">
                                <input type="radio" name="items[{{ $itemKey }}][temuan]" value="ya"
                                    {{ old("items.{$itemKey}.temuan", $existingDetail?->isfindings === true ? 'ya' : '') == 'ya' ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 focus:ring-blue-500 scale-125 cursor-pointer">
                            </td>
                            <td class="border-2 border-gray-400 px-2 py-3 text-center">
                                <input type="radio" name="items[{{ $itemKey }}][temuan]" value="tidak"
                                    {{ old("items.{$itemKey}.temuan", $existingDetail?->isfindings === false ? 'tidak' : '') == 'tidak' ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 focus:ring-blue-500 scale-125 cursor-pointer">
                            </td>

                            {{-- KONDISI - Radio buttons dengan name yang sama untuk saling eksklusif --}}
                            <td class="border-2 border-gray-400 px-2 py-3 text-center">
                                <input type="radio" name="items[{{ $itemKey }}][kondisi]" value="baik"
                                    {{ old("items.{$itemKey}.kondisi", $existingDetail?->iscondition === true ? 'baik' : '') == 'baik' ? 'checked' : '' }}
                                    class="w-5 h-5 text-green-600 focus:ring-green-500 scale-125 cursor-pointer">
                            </td>
                            <td class="border-2 border-gray-400 px-2 py-3 text-center">
                                <input type="radio" name="items[{{ $itemKey }}][kondisi]" value="rusak"
                                    {{ old("items.{$itemKey}.kondisi", $existingDetail?->iscondition === false ? 'rusak' : '') == 'rusak' ? 'checked' : '' }}
                                    class="w-5 h-5 text-red-600 focus:ring-red-500 scale-125 cursor-pointer">
                            </td>

                            {{-- CATATAN --}}
                            <td class="border-2 border-gray-400 px-4 py-3 font-medium">
                                <textarea name="items[{{ $itemKey }}][notes]"
                                    class="w-full px-2 py-1 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                                    placeholder="Opsional...">{{ old("items.{$itemKey}.notes", $existingDetail?->notes ?? '') }}</textarea>
                            </td>
                        </tr>
                        @php $no++; @endphp
                        @endforeach
                        @php $noCategory++; @endphp
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

                <button type="button" @click="openFinishDialog = true"
                    class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                    Simpan Checklist
                </button>
            </div>

            <!-- Modal -->
            <div x-show="openFinishDialog" @keydown.escape.window="openFinishDialog = false" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95" x-cloak>
                <div @click.away="openFinishDialog = false" class="bg-white w-full max-w-md p-6 rounded-2xl shadow-2xl">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Konfirmasi dan Tanda Tangan</h3>

                    <div class="space-y-6">

                        {{-- Signature Pad --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan Petugas:</label>
                            <div class="relative w-full h-48 border border-gray-300 rounded-lg bg-white">
                                <canvas id="signature-canvas" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <span id="signature-status" class="text-xs text-gray-500">Belum ada tanda tangan</span>
                                <button type="button" @click="clearSignature" class="text-sm text-blue-600 hover:underline">Hapus Tanda Tangan</button>
                            </div>
                        </div>

                        {{-- Diserahkan kepada --}}
                        <div>
                            <label for="received_id" class="block text-sm font-semibold text-gray-700 mb-2">Diserahkan kepada (Officer):</label>
                            <select name="received_id" id="received_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Officer</option>
                                @foreach($allOfficers as $officer)
                                <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Diketahui oleh --}}
                        <div>
                            <label for="approved_id" class="block text-sm font-semibold text-gray-700 mb-2">Diketahui oleh (Supervisor):</label>
                            <select name="approved_id" id="approved_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Supervisor</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Modal Buttons --}}
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" @click="openFinishDialog = false" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-lg">
                            Selesaikan & Simpan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Signature Pad Library --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
    function checklistForm() {
        return {
            openFinishDialog: false,
            signaturePad: null,

            init() {
                this.$watch('openFinishDialog', (value) => {
                    if (value) {
                        // Inisialisasi signature pad saat modal terbuka
                        this.$nextTick(() => this.initializeSignaturePad());
                    }
                });
            },

            initializeSignaturePad() {
                const canvas = document.getElementById('signature-canvas');
                if (!canvas) {
                    console.error('Canvas element not found!');
                    return;
                }

                // Hindari inisialisasi ganda
                if (this.signaturePad) {
                    this.signaturePad.clear();
                } else {
                    this.signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)'
                    });
                }

                const statusEl = document.getElementById('signature-status');
                this.signaturePad.addEventListener("endStroke", () => {
                    statusEl.textContent = 'Tanda tangan tersimpan sementara';
                    statusEl.className = 'text-xs text-green-600 font-semibold';
                });

                // Sesuaikan ukuran canvas untuk layar HiDPI
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                this.signaturePad.clear(); // Hapus canvas setelah penyesuaian ukuran
            },

            clearSignature() {
                if (this.signaturePad) {
                    this.signaturePad.clear();
                    const statusEl = document.getElementById('signature-status');
                    statusEl.textContent = 'Belum ada tanda tangan';
                    statusEl.className = 'text-xs text-gray-500';
                }
            },

            handleFormSubmit(event) {
                // Validasi client-side sebelum submit
                const receivedId = document.getElementById('received_id').value;
                const approvedId = document.getElementById('approved_id').value;
                const signatureDataInput = document.getElementById('signature-data');

                if (!receivedId) {
                    alert('Mohon pilih officer yang akan menerima checklist.');
                    return;
                }
                if (!approvedId) {
                    alert('Mohon pilih supervisor yang mengetahui.');
                    return;
                }
                if (this.signaturePad.isEmpty()) {
                    alert('Tanda tangan petugas tidak boleh kosong.');
                    return;
                }

                // Simpan data tanda tangan ke input tersembunyi
                signatureDataInput.value = this.signaturePad.toDataURL('image/png');

                // Kirim form
                event.target.submit();
            }
        }
    }
</script>
@endsection