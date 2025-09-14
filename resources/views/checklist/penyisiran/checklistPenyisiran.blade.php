@extends('layouts.app')

@section('title', 'Check List Penyisiran Daerah Steril Ruang Tunggu')

@section('content')
<div class="mx-auto p-2 sm:p-6 min-h-screen pt-5 sm:pt-20" x-data="checklistForm()">

    {{-- Alert Sukses --}}
    @if(session('success'))
    <div
        class="bg-green-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg text-sm sm:text-base">
        {{ session('success') }}
    </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
    <div class="bg-red-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg text-sm sm:text-base">
        {{ session('error') }}
    </div>
    @endif

    {{-- Menampilkan Error Validasi --}}
    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-3 rounded-xl relative mb-4 sm:mb-6 shadow-lg text-sm sm:text-base"
        role="alert">
        <strong class="font-bold">Oops! Terjadi kesalahan.</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
        {{-- Header Section - Responsive --}}
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-3 sm:px-6 py-4 sm:py-6 text-white">
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                {{-- Logo kiri - Hidden on mobile atau ukuran kecil --}}
                <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                    class="w-12 h-12 sm:w-20 sm:h-20 order-first sm:order-none">

                {{-- Teks tengah --}}
                <div class="text-center flex-1 px-2">
                    <h1 class="text-lg sm:text-2xl font-bold leading-tight">CHECK LIST PENGECEKAN HARIAN</h1>
                    <h2 class="text-base sm:text-xl font-semibold mt-1">PENYISIRAN DAERAH STERIL RUANG TUNGGU</h2>
                    <p class="text-blue-100 text-xs sm:text-sm mt-1">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
                </div>

                {{-- Logo kanan --}}
                <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo"
                    class="w-16 h-16 sm:w-24 sm:h-24 order-last sm:order-none">
            </div>
        </div>

        {{-- FORM --}}
        <form id="checklistForm" method="POST" action="{{ route('checklist.penyisiran.store') }}"
            @submit.prevent="handleFormSubmit" class="p-3 sm:p-6 space-y-4 sm:space-y-6">
            @csrf
            @if($checklist->exists)
            @method('PUT')
            @endif

            {{-- Hidden signature input --}}
            <input type="hidden" name="signature" id="signature-data">

            {{-- Header Information - Mobile Responsive --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-6 bg-gray-50 p-3 sm:p-4 rounded-lg">
                {{-- Hari/Tanggal --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Hari/Tanggal:</label>
                    <input type="date" name="date"
                        value="{{ old('date', $checklist->exists ? $checklist->date?->format('Y-m-d') : now()->format('Y-m-d')) }}"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                        required>
                </div>

                {{-- Jam --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Jam:</label>
                    <input type="time" name="jam"
                        value="{{ old('jam', $checklist->exists ? $checklist->date?->format('H:i') : now()->format('H:i')) }}"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                </div>

                {{-- Team --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Grup:</label>
                    <select name="grup" required
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                        <option value="">Pilih Grup</option>
                        <option value="A" {{ old('grup', $checklist->grup ?? '') == 'A' ? 'selected' : '' }}>Grup A
                        </option>
                        <option value="B" {{ old('grup', $checklist->grup ?? '') == 'B' ? 'selected' : '' }}>Grup B
                        </option>
                        <option value="C" {{ old('grup', $checklist->grup ?? '') == 'C' ? 'selected' : '' }}>Grup C
                        </option>
                    </select>
                </div>

                {{-- Nama Petugas --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Nama Petugas:</label>
                    <input type="text" name="officer_name" value="{{ $currentOfficer->name ?? '' }}" readonly
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg bg-gray-100 text-sm sm:text-base">
                    <input type="hidden" name="sender_id" value="{{ $currentOfficer->id ?? '' }}">
                </div>
            </div>

            {{-- Mobile: Card Layout --}}
            <div class="block lg:hidden space-y-4 mt-6">
                @php $no = 1; @endphp
                @foreach($penyisiranChecklist as $category => $items)
                <div class="bg-blue-100 p-3 rounded-lg font-bold text-blue-800 text-sm">
                    {{ $loop->iteration }}. {{ strtoupper($category) }}
                </div>
                @foreach($items as $item)
                @php
                $itemKey = $item->id;
                $existingDetail = $checklist->exists ? $checklist->details->firstWhere('checklist_item_id', $itemKey) :
                null;
                @endphp
                <div class="border border-gray-300 rounded-lg p-4 bg-white shadow-sm">
                    <div class="font-medium text-gray-800 mb-3 text-sm">{{ $no++ }}. {{ $item->name }}</div>

                    {{-- Temuan --}}
                    <div class="mb-3">
                        <label class="text-xs font-semibold text-gray-700">Temuan:</label>
                        <div class="flex space-x-6 mt-2">
                            <label class="flex items-center">
                                <input type="radio" name="items[{{ $itemKey }}][temuan]" value="ya" {{
                                    old("items.{$itemKey}.temuan", $existingDetail?->isfindings === true ? 'ya' : '') ==
                                'ya' ? 'checked' : '' }}
                                class="w-5 h-5 text-blue-600">
                                <span class="ml-2 text-sm">Ya</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="items[{{ $itemKey }}][temuan]" value="tidak" {{
                                    old("items.{$itemKey}.temuan", $existingDetail?->isfindings === false ? 'tidak' :
                                '') == 'tidak' ? 'checked' : '' }}
                                class="w-5 h-5 text-blue-600">
                                <span class="ml-2 text-sm">Tidak</span>
                            </label>
                        </div>
                    </div>

                    {{-- Kondisi --}}
                    <div class="mb-3">
                        <label class="text-xs font-semibold text-gray-700">Kondisi:</label>
                        <div class="flex space-x-6 mt-2">
                            <label class="flex items-center">
                                <input type="radio" name="items[{{ $itemKey }}][kondisi]" value="baik" {{
                                    old("items.{$itemKey}.kondisi", $existingDetail?->iscondition === true ? 'baik' :
                                '') == 'baik' ? 'checked' : '' }}
                                class="w-5 h-5 text-green-600">
                                <span class="ml-2 text-sm">Baik</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="items[{{ $itemKey }}][kondisi]" value="rusak" {{
                                    old("items.{$itemKey}.kondisi", $existingDetail?->iscondition === false ? 'rusak' :
                                '') == 'rusak' ? 'checked' : '' }}
                                class="w-5 h-5 text-red-600">
                                <span class="ml-2 text-sm">Rusak</span>
                            </label>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Catatan:</label>
                        <textarea name="items[{{ $itemKey }}][notes]"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm mt-1"
                            placeholder="Opsional...">{{ old("items.{$itemKey}.notes", $existingDetail?->notes ?? '') }}</textarea>
                    </div>
                </div>
                @endforeach
                @endforeach
            </div>

            {{-- Desktop: Table Layout --}}
            <div class="hidden lg:block mt-6">
                <div class="overflow-x-auto -mx-4 lg:mx-0">
                    <div class="min-w-max px-4 lg:px-0">
                        <table class="w-full border-collapse border-2 border-gray-400">
                            <thead>
                                <tr class="bg-blue-600 text-white">
                                    <th class="border-2 border-gray-400 px-2 lg:px-4 py-2 lg:py-3 text-center font-bold w-10 lg:w-12 text-sm"
                                        rowspan="2">NO</th>
                                    <th class="border-2 border-gray-400 px-2 lg:px-4 py-2 lg:py-3 text-center font-bold min-w-[180px] lg:min-w-0 text-sm"
                                        rowspan="2">KETERANGAN</th>
                                    <th class="border-2 border-gray-400 px-2 lg:px-4 py-2 lg:py-3 text-center font-bold text-sm"
                                        colspan="2">TEMUAN</th>
                                    <th class="border-2 border-gray-400 px-2 lg:px-4 py-2 lg:py-3 text-center font-bold text-sm"
                                        colspan="2">KONDISI</th>
                                    <th class="border-2 border-gray-400 px-2 lg:px-4 py-2 lg:py-3 text-center font-bold min-w-[140px] lg:min-w-0 text-sm"
                                        rowspan="2">Catatan</th>
                                </tr>
                                <tr class="bg-blue-500 text-white">
                                    <th
                                        class="border-2 border-gray-400 px-2 py-1 lg:py-2 text-center font-bold w-14 lg:w-20 text-sm">
                                        YA</th>
                                    <th
                                        class="border-2 border-gray-400 px-2 py-1 lg:py-2 text-center font-bold w-14 lg:w-20 text-sm">
                                        TIDAK</th>
                                    <th
                                        class="border-2 border-gray-400 px-2 py-1 lg:py-2 text-center font-bold w-14 lg:w-20 text-sm">
                                        BAIK</th>
                                    <th
                                        class="border-2 border-gray-400 px-2 py-1 lg:py-2 text-center font-bold w-14 lg:w-20 text-sm">
                                        RUSAK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @php $noCategory = 1; @endphp
                                @foreach($penyisiranChecklist as $category => $items)
                                <tr class="bg-blue-100">
                                    <td colspan="7"
                                        class="border-2 border-gray-400 px-2 lg:px-4 py-2 font-bold text-blue-800 text-left text-sm">
                                        {{ $noCategory }}. {{ strtoupper($category) }}</td>
                                </tr>
                                @foreach($items as $item)
                                @php
                                $itemKey = $item->id;
                                $existingDetail = $checklist->exists ?
                                $checklist->details->firstWhere('checklist_item_id', $itemKey) : null;
                                @endphp
                                <tr class="hover:bg-blue-50/50 transition-colors duration-200 border-b border-gray-100">
                                    <td
                                        class="border-2 border-gray-400 px-2 lg:px-4 py-2 lg:py-3 text-center font-bold text-sm">
                                        {{ $no }}.</td>
                                    <td class="border-2 border-gray-400 px-2 lg:px-4 py-2 lg:py-3 font-medium text-sm">
                                        {{ $item->name }}</td>

                                    {{-- TEMUAN - Radio buttons --}}
                                    <td class="border-2 border-gray-400 px-2 py-2 lg:py-3 text-center">
                                        <input type="radio" name="items[{{ $itemKey }}][temuan]" value="ya" {{
                                            old("items.{$itemKey}.temuan", $existingDetail?->isfindings === true ? 'ya'
                                        : '') == 'ya' ? 'checked' : '' }}
                                        class="w-4 h-4 lg:w-5 lg:h-5 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </td>
                                    <td class="border-2 border-gray-400 px-2 py-2 lg:py-3 text-center">
                                        <input type="radio" name="items[{{ $itemKey }}][temuan]" value="tidak" {{
                                            old("items.{$itemKey}.temuan", $existingDetail?->isfindings === false ?
                                        'tidak' : '') == 'tidak' ? 'checked' : '' }}
                                        class="w-4 h-4 lg:w-5 lg:h-5 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </td>

                                    {{-- KONDISI - Radio buttons --}}
                                    <td class="border-2 border-gray-400 px-2 py-2 lg:py-3 text-center">
                                        <input type="radio" name="items[{{ $itemKey }}][kondisi]" value="baik" {{
                                            old("items.{$itemKey}.kondisi", $existingDetail?->iscondition === true ?
                                        'baik' : '') == 'baik' ? 'checked' : '' }}
                                        class="w-4 h-4 lg:w-5 lg:h-5 text-green-600 focus:ring-green-500
                                        cursor-pointer">
                                    </td>
                                    <td class="border-2 border-gray-400 px-2 py-2 lg:py-3 text-center">
                                        <input type="radio" name="items[{{ $itemKey }}][kondisi]" value="rusak" {{
                                            old("items.{$itemKey}.kondisi", $existingDetail?->iscondition === false ?
                                        'rusak' : '') == 'rusak' ? 'checked' : '' }}
                                        class="w-4 h-4 lg:w-5 lg:h-5 text-red-600 focus:ring-red-500 cursor-pointer">
                                    </td>

                                    {{-- CATATAN --}}
                                    <td class="border-2 border-gray-400 px-2 lg:px-4 py-2 lg:py-3 font-medium">
                                        <textarea name="items[{{ $itemKey }}][notes]"
                                            class="w-full px-2 py-1 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                                            placeholder="Opsional..."
                                            rows="2">{{ old("items.{$itemKey}.notes", $existingDetail?->notes ?? '') }}</textarea>
                                    </td>
                                </tr>
                                @php $no++; @endphp
                                @endforeach
                                @php $noCategory++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Keterangan Section - Mobile Responsive --}}
            <div class="mt-4 sm:mt-6 bg-gray-50 p-3 sm:p-4 rounded-lg">
                <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-3 sm:mb-4">KETERANGAN:</h3>
                <ul class="space-y-1 sm:space-y-2 text-xs sm:text-sm text-gray-700">
                    <li>• No. 1, 2 dan 3 di isi tanda centang pada kolom TEMUAN</li>
                    <li>• No. 4 di isi tanda centang pada kolom KONDISI</li>
                    <li>• Tulis hasil temuan pada kolom CATATAN</li>
                </ul>
            </div>
            

            {{-- Submit Buttons - Mobile Responsive --}}
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 mt-6 sm:mt-8">

                <button type="button" @click="openFinishDialog = true"
                    class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg text-sm sm:text-base order-1 sm:order-2">
                    Simpan Checklist
                </button>
            </div>

            <!-- Modal - Mobile Responsive -->
            <div x-show="openFinishDialog" @keydown.escape.window="openFinishDialog = false"
                class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95" x-cloak>
                <div @click.away="openFinishDialog = false"
                    class="bg-white w-full max-w-xs sm:max-w-md p-4 sm:p-6 rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">Konfirmasi dan
                        Tanda Tangan</h3>

                    <div class="space-y-4 sm:space-y-6">

                        {{-- Signature Pad - Mobile Responsive --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan Petugas:</label>
                            <div class="relative w-full h-32 sm:h-48 border border-gray-300 rounded-lg bg-white">
                                <canvas id="signature-canvas" class="w-full h-full touch-action-none"></canvas>
                            </div>
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mt-2 space-y-1 sm:space-y-0">
                                <span id="signature-status" class="text-xs text-gray-500">Belum ada tanda tangan</span>
                                <button type="button" @click="clearSignature"
                                    class="text-sm text-blue-600 hover:underline">Hapus Tanda Tangan</button>
                            </div>
                        </div>

                        {{-- Diserahkan kepada - Mobile Responsive --}}
                        <div>
                            <label for="received_id" class="block text-sm font-semibold text-gray-700 mb-2">Diserahkan
                                kepada (Officer):</label>
                            <select name="received_id" id="received_id"
                                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                                required>
                                <option value="">Pilih Officer</option>
                                @foreach($allOfficers as $officer)
                                <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Diketahui oleh - Mobile Responsive --}}
                        <div>
                            <label for="approved_id" class="block text-sm font-semibold text-gray-700 mb-2">Diketahui
                                oleh (Supervisor):</label>
                            <select name="approved_id" id="approved_id"
                                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                                required>
                                <option value="">Pilih Supervisor</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Modal Buttons - Mobile Responsive --}}
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 mt-6 sm:mt-8">
                        <button type="button" @click="openFinishDialog = false"
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm sm:text-base order-2 sm:order-1">
                            Batal
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-lg text-sm sm:text-base order-1 sm:order-2">
                            Selesaikan & Simpan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if(session('duplicate_error'))
<script>
    // Menunggu halaman selesai dimuat sebelum menjalankan script
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Data Duplikat!',
            // Ambil pesan error yang kita kirim dari controller
            text: "{{ session('duplicate_error') }}",
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#DC2626', // Warna merah
        });
    });
</script>
@endif

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
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(0, 0, 0)'
                    });
                }

                const statusEl = document.getElementById('signature-status');
                this.signaturePad.addEventListener("endStroke", () => {
                    statusEl.textContent = 'Tanda tangan tersimpan sementara';
                    statusEl.className = 'text-xs text-green-600 font-semibold';
                });

                // Sesuaikan ukuran canvas untuk layar HiDPI dan mobile
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * ratio;
                canvas.height = rect.height * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                canvas.style.width = rect.width + 'px';
                canvas.style.height = rect.height + 'px';
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

@push('styles')
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<style>
    /* Disable zoom */
    * {
        touch-action: manipulation;
    }

    /* Prevent text selection on mobile */
    .no-select {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Better mobile table scrolling */
    .table-container::-webkit-scrollbar {
        height: 6px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    @media (max-width: 768px) {
        .text-sm {
            font-size: 0.8rem;
        }

        .text-base {
            font-size: 0.9rem;
        }

        input,
        textarea,
        select {
            font-size: 1rem;
            /* Mencegah zoom otomatis di iOS */
        }
    }
</style>
@endpush
