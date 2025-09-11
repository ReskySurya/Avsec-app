@extends('layouts.app')

@section('title', 'Check List Pengecekan Harian Kendaraan Patroli')

@section('content')
<div x-data='checklistForm(@json($mobilChecklist), @json($motorChecklist), {{ isset($checklist) ? ' true' : 'false' }})'
    class="mx-auto p-2 sm:p-4 min-h-screen pt-5 sm:pt-20">
    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
        class="bg-gradient-to-r from-green-400 to-green-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg border-l-4 border-green-600 text-sm sm:text-base">
        <div class="flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif
    @if(session('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
        class="bg-gradient-to-r from-red-400 to-red-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg border-l-4 border-red-600 text-sm sm:text-base">
        <div class="flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-3 sm:px-6 py-4 sm:py-6 text-white">
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                    class="w-12 h-12 sm:w-20 sm:h-20 order-first sm:order-none">
                <div class="text-center flex-1 px-2">
                    <h1 class="text-lg sm:text-2xl font-bold">CHECK LIST PENGECEKAN HARIAN</h1>
                    <h2 class="text-base sm:text-xl font-semibold mt-1"
                        x-text="selectedVehicle === 'mobil' ? 'KENDARAAN MOBIL PATROLI' : 'KENDARAAN MOTOR PATROLI'">
                    </h2>
                    <p class="text-blue-100 text-xs sm:text-sm mt-1">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
                </div>
                <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo"
                    class="w-16 h-16 sm:w-24 sm:h-24 order-last sm:order-none">
            </div>
        </div>

        <form x-ref="checklistForm" id="checklistForm" method="POST" action="{{ route('checklist.kendaraan.store') }}"
            @submit.prevent="submitForm" class="p-3 sm:p-6 space-y-4 sm:space-y-6">
            @csrf
            @if($checklist->exists)
            @method('PUT')
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-6">
                <div class="space-y-1 sm:space-y-2">
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700">Nama Operator
                        Penerbangan:</label>
                    <input type="text" name="operator_name" value="Bandar Udara Adisutjipto Yogyakarta"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                </div>
                <div class="space-y-1 sm:space-y-2">
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700">Tanggal & Waktu
                        Pengujian:</label>
                    <div class="flex items-center space-x-1 sm:space-x-2">
                        <input type="datetime-local" name="date" id="date" x-model="testDateTime"
                            class="flex-1 px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                        <div
                            class="px-2 sm:px-4 py-2 sm:py-3 bg-gray-100 border border-gray-300 rounded-lg text-xs sm:text-sm font-medium">
                            WIB
                        </div>
                    </div>
                </div>
                <div class="space-y-1 sm:space-y-2">
                    <label for="type" class="block text-xs sm:text-sm font-semibold text-gray-700">Jenis
                        Kendaraan:</label>
                    <select name="type" x-model="selectedVehicle"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                        <option value="mobil">Mobil Patroli</option>
                        <option value="motor">Motor Patroli</option>
                    </select>
                </div>
                <div class="space-y-1 sm:space-y-2">
                    <label for="shift" class="block text-xs sm:text-sm font-semibold text-gray-700">Shift:</label>
                    <select name="shift" x-model="selectedShift"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                        <option value="pagi">Shift Pagi</option>
                        <option value="malam">Shift Malam</option>
                    </select>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-3 sm:p-6">
                <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-3 sm:mb-4"
                    x-text="selectedVehicle === 'mobil' ? 'CHECK LIST PENGECEKAN HARIAN MOBIL PATROLI' : 'CHECK LIST PENGECEKAN HARIAN MOTOR PATROLI'">
                </h3>

                {{-- Mobile: Card Layout --}}
                <div class="block lg:hidden space-y-3">
                    <template x-for="(category, categoryIndex) in getCurrentChecklist()" :key="categoryIndex">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-blue-600 text-white px-3 sm:px-4 py-2 sm:py-3 font-semibold text-sm" x-text="category.name">
                            </div>
                            <div class="p-3 sm:p-4 space-y-3">
                                <template x-for="(item, itemIndex) in category.items" :key="itemIndex">
                                    <div class="border-b border-gray-100 pb-3 last:border-b-0">
                                        <div class="font-medium text-gray-800 mb-2 text-sm" x-text="item.name"></div>
                                        <div class="space-y-3">
                                            <div>
                                                <div class="text-xs font-medium text-gray-600 mb-2"
                                                    x-text="selectedShift === 'pagi' ? 'Kondisi Shift Pagi' : 'Kondisi Shift Malam'">
                                                </div>
                                                <div class="flex space-x-4">
                                                    <label :for="`item_ok_${item.id}`" class="flex items-center">
                                                        <input type="radio" :name="`items[${item.id}][is_ok]`"
                                                            :id="`item_ok_${item.id}`" value="1"
                                                            class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-600">
                                                        <span
                                                            class="text-xs sm:text-sm text-green-600 font-medium">BAIK</span>
                                                    </label>
                                                    <label :for="`item_not_ok_${item.id}`" class="flex items-center">
                                                        <input type="radio" :name="`items[${item.id}][is_ok]`"
                                                            :id="`item_not_ok_${item.id}`" value="0"
                                                            class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-red-600">
                                                        <span
                                                            class="text-xs sm:text-sm text-red-600 font-medium">TIDAK</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <label :for="`notes_${item.id}`"
                                                    class="text-xs font-medium text-gray-600">Catatan:</label>
                                                <textarea
                                                    :name="`items[${item.id}][notes]`"
                                                    :id="`notes_${item.id}`"
                                                    x-model="formData[item.id].notes"
                                                    class="mt-1 w-full px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm"
                                                    placeholder="Opsional..."
                                                    rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Desktop: Table Layout --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="border border-gray-300 px-3 sm:px-4 py-2 sm:py-3 text-left font-semibold text-xs sm:text-sm"
                                    rowspan="2">NO</th>
                                <th class="border border-gray-300 px-3 sm:px-4 py-2 sm:py-3 text-left font-semibold text-xs sm:text-sm"
                                    rowspan="2">KETERANGAN</th>
                                <th class="border border-gray-300 px-3 sm:px-4 py-2 sm:py-3 text-center font-semibold text-xs sm:text-sm"
                                    colspan="2"
                                    x-text="selectedShift === 'pagi' ? 'KONDISI SHIFT PAGI' : 'KONDISI SHIFT MALAM'">
                                </th>
                                <th class="border border-gray-300 px-3 sm:px-4 py-2 sm:py-3 text-left font-semibold text-xs sm:text-sm"
                                    rowspan="2">CATATAN</th>
                            </tr>
                            <tr class="bg-blue-500 text-white">
                                <th class="border border-gray-300 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm"
                                    colspan="2">
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
                                    <td class="border border-gray-300 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs sm:text-sm"
                                        :class="row.isCategory ? 'font-bold' : ''"
                                        x-text="row.isCategory ? (row.letter || '') : row.number"></td>
                                    <td class="border border-gray-300 px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm"
                                        :class="row.isCategory ? 'font-bold' : ''" x-text="row.name"></td>
                                    <td class="border border-gray-300 px-3 sm:px-4 py-2 sm:py-3" colspan="2">
                                        <template x-if="!row.isCategory">
                                            <div class="grid grid-cols-2 gap-1">
                                                <div class="text-center"><input type="radio"
                                                        :name="`items[${row.id}][is_ok]`"
                                                        :id="`desktop_item_ok_${row.id}`" value="1"
                                                        class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 focus:ring-green-500">
                                                </div>
                                                <div class="text-center"><input type="radio"
                                                        :name="`items[${row.id}][is_ok]`"
                                                        :id="`desktop_item_not_ok_${row.id}`" value="0"
                                                        class="w-4 h-4 sm:w-5 sm:h-5 text-red-600 focus:ring-red-500">
                                                </div>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="border border-gray-300 px-3 sm:px-4 py-2 sm:py-3">
                                        <template x-if="!row.isCategory">
                                            <textarea
                                                :name="`items[${row.id}][notes]`"
                                                :id="`desktop_notes_${row.id}`"
                                                x-model="formData[row.id].notes"
                                                rows="1"
                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm"
                                                placeholder="Opsional..."></textarea>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-4 sm:pt-6 border-t border-gray-200">
                <button type="button" onclick="window.history.back()"
                    class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200 font-medium text-sm sm:text-base">Kembali</button>
                <button type="button" @click="openFinishDialog"
                    class="w-full sm:w-auto px-4 sm:px-8 py-2 sm:py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition duration-200 font-medium shadow-lg text-sm sm:text-base">Simpan
                    Checklist</button>
            </div>
        </form>
    </div>

    <div x-show="isFinishDialogOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4"
        style="display: none;">
        <div @click.away="isFinishDialogOpen = false"
            class="bg-white w-full max-w-xs sm:max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-4 sm:p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg sm:text-2xl font-bold">Konfirmasi Selesai</h2>
                        <p class="text-blue-100 text-xs sm:text-sm">Konfirmasi penyelesaian logbook shift</p>
                    </div>
                    <button @click="isFinishDialogOpen = false"
                        class="text-white hover:text-gray-200 text-2xl">&times;</button>
                </div>
            </div>
            <div class="p-3 sm:p-6">
                <div class="mb-4 sm:mb-6">
                    <p class="text-gray-700 text-sm sm:text-base mb-3 sm:mb-4">Apakah Anda yakin sudah menyelesaikan
                        logbook shift hari ini?</p>
                    <div class="border-2 border-gray-200 rounded-xl p-3 sm:p-4 mb-3 sm:mb-4">
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Tanda Tangan
                            Digital</label>
                        <div class="relative w-full h-32 sm:h-48 border border-gray-300 rounded-lg bg-white">
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
                                class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 transition-colors">Clear</button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mb-3 sm:mb-4">
                    <label for="receivedID" class="block text-gray-700 font-bold text-xs sm:text-sm mb-1">Pilih Officer
                        yang Menerima:</label>
                    <select name="receivedID" x-ref="receivedID" form="checklistForm"
                        class="w-full border rounded px-2 py-2 sm:px-3 sm:py-3 text-xs sm:text-sm">
                        <option value="">Pilih Officer</option>
                        @foreach($officers as $officer)
                        <option value="{{ $officer->id }}" {{ old('receivedID', $checklist->received_id ?? '') ==
                            $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                        @endforeach
                    </select>
                    <span x-show="errors.receivedID" x-text="errors.receivedID"
                        class="text-xs text-red-500 mt-1"></span>
                </div>

                <div class="mb-3 sm:mb-4">
                    <label for="approvedID" class="block text-gray-700 font-bold text-xs sm:text-sm mb-1">Pilih
                        Supervisor yang Mengetahui:</label>
                    <select name="approvedID" x-ref="approvedID" form="checklistForm"
                        class="w-full border rounded px-2 py-2 sm:px-3 sm:py-3 text-xs sm:text-sm">
                        <option value="">Pilih Supervisor</option>
                        @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" {{ old('approvedID', $checklist->approved_id ?? '') ==
                            $supervisor->id ? 'selected' : '' }}>{{ $supervisor->name }}</option>
                        @endforeach
                    </select>
                    <span x-show="errors.approvedID" x-text="errors.approvedID"
                        class="text-xs text-red-500 mt-1"></span>
                </div>

                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" @click="isFinishDialogOpen = false"
                        class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium text-sm sm:text-base">Batal</button>
                    <button type="button" @click="submitForm"
                        class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg text-sm sm:text-base">
                        {{ isset($checklist) ? 'Update Konfirmasi' : 'Konfirmasi & Kirim' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
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

            init() {
                this.initializeFormData();
                this.$watch('selectedVehicle', () => {
                    this.initializeFormData();
                });
            },

            // State untuk menyimpan notes
            formData: {},

            // Methods
            getCurrentChecklist() {
                return this.selectedVehicle === 'mobil' ? this.mobilChecklist : this.motorChecklist;
            },

            // Method untuk menginisialisasi form data
            initializeFormData() {
                const checklist = this.getCurrentChecklist();
                if (!checklist || !Array.isArray(checklist)) return;

                checklist.forEach(category => {
                    if (category.items && Array.isArray(category.items)) {
                        category.items.forEach(item => {
                            if (!this.formData[item.id]) {
                                this.formData[item.id] = {
                                    is_ok: null,
                                    notes: ''
                                };
                            }
                        });
                    }
                });
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

<style>
    @media (max-width: 768px) {
        .max-w-6xl {
            max-width: 100%;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        /* Smaller text on mobile */
        .text-sm {
            font-size: 0.8125rem;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .text-base {
            font-size: 0.875rem;
        }

        .text-lg {
            font-size: 1.125rem;
        }

        .text-xl {
            font-size: 1.25rem;
        }

        .text-2xl {
            font-size: 1.5rem;
        }
    }

    /* Modal responsive sizing */
    @media (max-width: 640px) {
        .max-w-xs {
            max-width: 90%;
        }
    }

    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }
    }

    /* Smooth transitions */
    .transition-colors {
        transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
    }

    .transition-shadow {
        transition: box-shadow 0.2s ease-in-out;
    }

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
