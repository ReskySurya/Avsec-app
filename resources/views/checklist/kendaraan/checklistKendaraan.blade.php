@extends('layouts.app')

@section('title', 'Check List Pengecekan Harian Kendaraan Patroli')

@section('content')
<div x-data='checklistForm(@json($mobilChecklist), @json($motorChecklist), {{ isset($checklist) ? ' true' : 'false' }})'
    class="mx-auto p-4 sm:p-6 min-h-screen pt-5 sm:pt-20">
    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
        class="bg-gradient-to-r from-green-400 to-green-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-green-600">
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

    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                        class="w-20 h-20 mb-2 sm:mb-0">
                    <div>
                        <h1 class="text-2xl font-bold">CHECK LIST PENGECEKAN HARIAN</h1>
                        <h2 class="text-xl font-semibold"
                            x-text="selectedVehicle === 'mobil' ? 'KENDARAAN MOBIL PATROLI' : 'KENDARAAN MOTOR PATROLI'">
                        </h2>
                        <p class="text-blue-100">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium">Adisutjipto</div>
                    <div class="text-sm font-medium">Airport</div>
                </div>
            </div>
        </div>

        <form x-ref="checklistForm" id="checklistForm" method="POST" action="{{ route('checklist.kendaraan.store') }}"
            @submit.prevent="submitForm" class="p-6 space-y-6">
            @csrf
            @if($checklist->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Nama Operator Penerbangan:</label>
                    <input type="text" name="operator_name" value="Bandar Udara Adisutjipto Yogyakarta"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Tanggal & Waktu Pengujian:</label>
                    <div class="flex items-center space-x-2">
                        <input type="datetime-local" name="date" id="date" x-model="testDateTime"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium">WIB
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="type" class="block text-sm font-semibold text-gray-700">Jenis Kendaraan:</label>
                    <select name="type" x-model="selectedVehicle"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="mobil">Mobil Patroli</option>
                        <option value="motor">Motor Patroli</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label for="shift" class="block text-sm font-semibold text-gray-700">Shift:</label>
                    <select name="shift" x-model="selectedShift"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="pagi">Shift Pagi</option>
                        <option value="malam">Shift Malam</option>
                    </select>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4"
                    x-text="selectedVehicle === 'mobil' ? 'CHECK LIST PENGECEKAN HARIAN MOBIL PATROLI' : 'CHECK LIST PENGECEKAN HARIAN MOTOR PATROLI'">
                </h3>

                <div class="block md:hidden space-y-4">
                    <template x-for="(category, categoryIndex) in getCurrentChecklist()" :key="categoryIndex">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="bg-blue-600 text-white px-4 py-3 font-semibold" x-text="category.name"></div>
                            <div class="p-4 space-y-3">
                                <template x-for="(item, itemIndex) in category.items" :key="itemIndex">
                                    <div class="border-b border-gray-200 pb-3">
                                        <div class="font-medium text-gray-800 mb-2" x-text="item.name"></div>
                                        <div class="grid grid-cols-1 gap-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-600 mb-2"
                                                    x-text="selectedShift === 'pagi' ? 'Kondisi Shift Pagi' : 'Kondisi Shift Malam'">
                                                </div>
                                                <div class="flex space-x-4">
                                                    <label :for="`item_ok_${item.id}`" class="flex items-center">
                                                        <input type="radio" :name="`items[${item.id}][is_ok]`"
                                                            :id="`item_ok_${item.id}`" value="1"
                                                            class="mr-2 text-green-600">
                                                        <span class="text-sm text-green-600 font-medium">BAIK</span>
                                                    </label>
                                                    <label :for="`item_not_ok_${item.id}`" class="flex items-center">
                                                        <input type="radio" :name="`items[${item.id}][is_ok]`"
                                                            :id="`item_not_ok_${item.id}`" value="0"
                                                            class="mr-2 text-red-600">
                                                        <span class="text-sm text-red-600 font-medium">TIDAK</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <label :for="`notes_${item.id}`"
                                                    class="text-sm font-medium text-gray-600">Catatan:</label>
                                                <textarea :name="`items[${item.id}][notes]`" :id="`notes_${item.id}`"
                                                    cols="2"
                                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                    placeholder="Opsional..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="border border-gray-300 px-4 py-3 text-left font-semibold" rowspan="2">NO</th>
                                <th class="border border-gray-300 px-4 py-3 text-left font-semibold" rowspan="2">KETERANGAN</th>
                                <th class="border border-gray-300 px-4 py-3 text-center font-semibold" colspan="2"
                                    x-text="selectedShift === 'pagi' ? 'KONDISI SHIFT PAGI' : 'KONDISI SHIFT MALAM'">
                                </th>
                                <th class="border border-gray-300 px-4 py-3 text-left font-semibold" rowspan="2">CATATAN</th>
                            </tr>
                            <tr class="bg-blue-500 text-white">
                                <th class="border border-gray-300 px-4 py-2" colspan="2">
                                    <div class="grid grid-cols-2 gap-1">
                                        <div class="text-center font-semibold">BAIK</div>
                                        <div class="text-center font-semibold">TIDAK</div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="row in getFlattenedRows()" :key="`row-${row.index}`">
                                <tr :class="row.isCategory ? 'bg-blue-100' : 'hover:bg-gray-50'">
                                    <td class="border border-gray-300 px-4 py-3 text-center"
                                        :class="row.isCategory ? 'font-bold' : ''"
                                        x-text="row.isCategory ? (row.letter || '') : row.number"></td>
                                    <td class="border border-gray-300 px-4 py-3"
                                        :class="row.isCategory ? 'font-bold' : ''" x-text="row.name"></td>
                                    <td class="border border-gray-300 px-4 py-3" colspan="2">
                                        <template x-if="!row.isCategory">
                                            <div class="grid grid-cols-2 gap-1">
                                                <div class="text-center"><input type="radio"
                                                        :name="`items[${row.id}][is_ok]`"
                                                        :id="`desktop_item_ok_${row.id}`" value="1"
                                                        class="text-green-600 focus:ring-green-500"></div>
                                                <div class="text-center"><input type="radio"
                                                        :name="`items[${row.id}][is_ok]`"
                                                        :id="`desktop_item_not_ok_${row.id}`" value="0"
                                                        class="text-red-600 focus:ring-red-500"></div>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-3">
                                        <template x-if="!row.isCategory">
                                            <textarea :name="`items[${row.id}][notes]`" :id="`desktop_notes_${row.id}`"
                                                rows="1"
                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                placeholder="Opsional..."></textarea>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <button type="button" onclick="window.history.back()"
                    class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200 font-medium">Kembali</button>
                <button type="button" @click="openFinishDialog"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition duration-200 font-medium shadow-lg">Simpan
                    Checklist</button>
            </div>
        </form>
    </div>

    <div x-show="isFinishDialogOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="isFinishDialogOpen = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Konfirmasi Selesai</h2>
                <p class="text-blue-100">Konfirmasi penyelesaian logbook shift hari ini</p>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <p class="text-gray-700 text-lg mb-4">Apakah Anda yakin sudah menyelesaikan logbook shift hari ini?
                    </p>
                    <div class="border-2 border-gray-200 rounded-xl p-4 mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan Digital</label>
                        <div class="relative w-full h-48 border border-gray-300 rounded-lg bg-white">
                            @if(isset($checklist) && $checklist->senderSignature)
                            <img src="data:image/png;base64,{{ $checklist->senderSignature }}"
                                alt="Tanda tangan sudah ada" class="w-full h-full object-contain">
                            @else
                            <canvas x-ref="signatureCanvas" class="w-full h-full"></canvas>
                            @endif
                        </div>
                        <input type="hidden" name="signature" x-ref="signatureData" form="checklistForm"
                            value="{{ isset($checklist) ? $checklist->senderSignature : '' }}">
                        <div class="flex justify-between items-center mt-2">
                            <span x-show="errors.signature" x-text="errors.signature"
                                class="text-xs text-red-500"></span>
                            @if(!isset($checklist) || !$checklist->senderSignature)
                            <button type="button" @click="clearSignature"
                                class="text-sm text-blue-600 hover:text-blue-800 transition-colors">Clear</button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mb-2 sm:mb-4">
                    <label for="receivedID"
                        class="block text-gray-700 font-bold text-xs sm:text-base mb-1 sm:mb-2">Pilih Officer yang
                        Menerima:</label>
                    <select name="receivedID" x-ref="receivedID" form="checklistForm"
                        class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base" required>
                        <option value="">Pilih Officer</option>
                        @foreach($officers as $officer)
                        <option value="{{ $officer->id }}" {{ old('receivedID', $checklist->received_id ?? '') ==
                            $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                        @endforeach
                    </select>
                    <span x-show="errors.receivedID" x-text="errors.receivedID"
                        class="text-xs text-red-500 mt-1"></span>
                </div>

                <div class="mb-2 sm:mb-4">
                    <label for="approvedID"
                        class="block text-gray-700 font-bold text-xs sm:text-base mb-1 sm:mb-2">Pilih Supervisor yang
                        Mengetahui:</label>
                    <select name="approvedID" x-ref="approvedID" form="checklistForm"
                        class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base" required>
                        <option value="">Pilih Supervisor</option>
                        @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" {{ old('approvedID', $checklist->approved_id ?? '') ==
                            $supervisor->id ? 'selected' : '' }}>{{ $supervisor->name }}</option>
                        @endforeach
                    </select>
                    <span x-show="errors.approvedID" x-text="errors.approvedID"
                        class="text-xs text-red-500 mt-1"></span>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="isFinishDialogOpen = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">Batal</button>
                    <button type="button" @click="submitForm"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">
                        {{ isset($checklist) ? 'Update Konfirmasi' : 'Konfirmasi & Kirim' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('checklistForm', (mobilChecklist, motorChecklist, isEditMode = false) => ({
            // State Properties
            selectedVehicle: 'mobil',
            selectedShift: 'pagi',
            testDateTime: new Date().toISOString().slice(0, 16),
            isFinishDialogOpen: false,
            signaturePad: null,
            errors: {},

            // Data
            mobilChecklist: mobilChecklist,
            motorChecklist: motorChecklist,

            // Methods
            getCurrentChecklist() {
                return this.selectedVehicle === 'mobil' ? this.mobilChecklist : this.motorChecklist;
            },

            getFlattenedRows() {
                const checklist = this.getCurrentChecklist();
                if (!checklist || !Array.isArray(checklist)) return [];
                const rows = [];
                let itemNumber = 1;
                checklist.forEach((category, categoryIndex) => {
                    rows.push({
                        index: `cat-${categoryIndex}`,
                        isCategory: true,
                        letter: category.letter || '',
                        name: category.name || ''
                    });
                    if (category.items && Array.isArray(category.items)) {
                        category.items.forEach((item, itemIndex) => {
                            rows.push({
                                index: `item-${categoryIndex}-${itemIndex}`,
                                isCategory: false,
                                number: itemNumber++,
                                name: item.name,
                                id: item.id
                            });
                        });
                    }
                });
                return rows;
            },

            openFinishDialog() {
                this.isFinishDialogOpen = true;
                this.$nextTick(() => {
                    // Hanya inisialisasi signature pad jika belum ada
                    if (!this.signaturePad && this.$refs.signatureCanvas) {
                        this.initializeSignaturePad();
                    }
                });
            },

            initializeSignaturePad() {
                const canvas = this.$refs.signatureCanvas;
                if (!canvas) return;

                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);

                this.signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)'
                });
            },

            clearSignature() {
                if (this.signaturePad) {
                    this.signaturePad.clear();
                    this.$refs.signatureData.value = '';
                }
            },

            submitForm() {
                if (this.validateForm()) {
                    this.$refs.checklistForm.submit();
                }
            },

            validateForm() {
                this.errors = {}; // Reset errors

                // 1. Validasi tanda tangan
                const signatureDataInput = this.$refs.signatureData;
                if (this.signaturePad && !this.signaturePad.isEmpty()) {
                    signatureDataInput.value = this.signaturePad.toDataURL('image/png');
                }
                if (!signatureDataInput.value) {
                    this.errors.signature = 'Tanda tangan wajib diisi.';
                }

                // 2. Validasi dropdown
                if (!this.$refs.receivedID.value) {
                    this.errors.receivedID = 'Mohon pilih Officer yang Menerima.';
                }
                if (!this.$refs.approvedID.value) {
                    this.errors.approvedID = 'Mohon pilih Supervisor yang Mengetahui.';
                }

                return Object.keys(this.errors).length === 0;
            }
        }));
    });
</script>

{{-- Pindahkan style ke file CSS utama jika memungkinkan --}}
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

    input[type="radio"]:checked {
        background-color: currentColor;
    }
</style>
@endsection
