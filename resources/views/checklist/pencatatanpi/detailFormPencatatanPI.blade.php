@extends('layouts.app')

@section('title', 'Detail Form Pencatatan PI')

@section('content')
<div x-data="checklistForm()" class="mx-auto p-0 sm:p-6 min-h-screen pt-5 sm:pt-20">
    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg border-l-4 border-blue-600 text-sm sm:text-base">
        <div class="flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition class="bg-gradient-to-r from-red-400 to-red-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg border-l-4 border-red-600 text-sm sm:text-base">
        <div class="flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-6 text-white">
            <h3 class="text-2xl font-bold mb-1">Edit Data Pencatatan PI</h3>
        </div>
        <form id="updateForm" action="{{ route('checklist.pencatatanpi.update', $pencatatanPI->id) }}" method="POST" class="p-6" @submit.prevent="handleFormSubmit($event)">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" id="date" name="date" value="{{ old('date', $pencatatanPI->date ? $pencatatanPI->date->format('Y-m-d') : '') }}" required class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200" readonly>
                </div>
                <div>
                    <label for="grup" class="block text-sm font-semibold text-gray-700 mb-2">Grup</label>
                    <select id="grup" name="grup_disabled" required class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 bg-gray-100" disabled>
                        <option value="">Pilih Grup</option>
                        <option value="A" {{ old('grup', $pencatatanPI->grup) == 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ old('grup', $pencatatanPI->grup) == 'B' ? 'selected' : '' }}>B</option>
                        <option value="C" {{ old('grup', $pencatatanPI->grup) == 'C' ? 'selected' : '' }}>C</option>
                    </select>
                    <input type="hidden" name="grup" value="{{ $pencatatanPI->grup }}">
                </div>
                <div>
                    <label for="name_person" class="block text-sm font-semibold text-gray-700 mb-2">Nama Pemilik</label>
                    <input required type="text" id="name_person" name="name_person" value="{{ old('name_person', $pencatatanPI->name_person) }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200" placeholder="nama penanggung jawab" readonly>
                </div>
                <div>
                    <label for="agency" class="block text-sm font-semibold text-gray-700 mb-2">Instansi</label>
                    <input type="text" id="agency" name="agency" value="{{ old('agency', $pencatatanPI->agency) }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200" placeholder="nama instansi" readonly>
                </div>
                <div>
                    <label for="in_time" class="block text-sm font-semibold text-gray-700 mb-2">Jam Masuk</label>
                    <input required type="time" id="in_time" name="in_time" value="{{ old('in_time', $pencatatanPI->in_time) }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200" readonly>
                </div>
                <div>
                    <label for="out_time" class="block text-sm font-semibold text-gray-700 mb-2">Jam Keluar</label>
                    <input type="time" id="out_time" name="out_time" value="{{ old('out_time', $pencatatanPI->out_time) }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                </div>
                <div>
                    <label for="jenis_PI" class="block text-sm font-semibold text-gray-700 mb-2">Jenis PI</label>
                    <input required type="text" id="jenis_PI" name="jenis_PI" value="{{ old('jenis_PI', $pencatatanPI->jenis_PI) }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200" readonly>
                </div>
                <div>
                    <label for="in_quantity" class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Masuk</label>
                    <input required type="text" id="in_quantity" name="in_quantity" value="{{ old('in_quantity', $pencatatanPI->in_quantity) }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 " placeholder="0" readonly>
                </div>
                <div>
                    <label for="out_quantity" class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Keluar</label>
                    <input type="text" id="out_quantity" name="out_quantity"
                        value="{{ old('out_quantity', $pencatatanPI->in_quantity) }}"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                        placeholder="0">
                </div>

                <div class="md:col-span-2">
                    <label for="summary" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <textarea id="summary" name="summary" rows="3" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200" placeholder="Masukkan Keterangan">{{ old('summary', $pencatatanPI->summary) }}</textarea>
                </div>
            </div>

            <!-- Hidden inputs -->
            <input type="hidden" name="senderSignature" id="senderSignature">
            <input type="hidden" name="approved_id" id="approved_id">

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('checklist.pencatatanpi.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">Batal</a>
                <button type="button" @click="openFinishDialog = true" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    {{-- Modal Konfirmasi --}}
    <div x-show="openFinishDialog" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openFinishDialog = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Konfirmasi Selesai</h2>
                <p class="text-blue-100">Konfirmasi penyelesaian Catatan PI</p>
            </div>
            <div class="p-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan Petugas (Wajib):</label>
                    <div class="relative w-full h-48 border border-gray-300 rounded-lg bg-white">
                        <canvas id="signature-canvas" class="w-full h-full"></canvas>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span id="signature-status" class="text-xs text-gray-500">Belum ada tanda tangan</span>
                        <button type="button" @click="clearSignature()" class="text-sm text-blue-600 hover:underline">Hapus Tanda Tangan</button>
                    </div>
                </div>

                <div class="mb-4 mt-4">
                    <label for="supervisor_id" class="block text-gray-700 font-bold text-sm mb-2">Pilih Supervisor Mengetahui:</label>
                    <select id="supervisor_id" class="w-full border rounded px-2 py-1 text-sm" required>
                        <option value="">Pilih Supervisor</option>
                        @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="openFinishDialog = false" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-medium">Batal</button>
                    <button type="button" @click="submitForm()" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-lg hover:from-blue-600 hover:to-teal-700 transition-colors font-medium shadow-lg">
                        Konfirmasi & Kirim
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
       document.addEventListener("DOMContentLoaded", function () {
        const inQty = document.getElementById("in_quantity");
        const outQty = document.getElementById("out_quantity");

        outQty.value = inQty.value; // awalnya sama
        inQty.addEventListener("input", function () {
            outQty.value = inQty.value;
        });
    });
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

            submitForm() {
                const approvedId = document.getElementById('supervisor_id').value;
                const signatureDataInput = document.getElementById('senderSignature');
                const outTime = document.getElementById('out_time').value;
                const inTime = document.getElementById('in_time').value;

                if (!outTime) {
                    alert('Mohon isi jam keluar.');
                    return;
                }
                if (!inTime) {
                    alert('Mohon isi jam masuk.');
                    return;
                }

                if (!approvedId) {
                    alert('Mohon pilih supervisor yang mengetahui.');
                    return;
                }
                if (!this.signaturePad || this.signaturePad.isEmpty()) {
                    alert('Tanda tangan petugas tidak boleh kosong.');
                    return;
                }

                // Simpan data tanda tangan ke input tersembunyi
                signatureDataInput.value = this.signaturePad.toDataURL('image/png');
                document.getElementById('approved_id').value = approvedId;

                // Kirim form
                document.getElementById('updateForm').submit();
            },

            handleFormSubmit(event) {
                // Form akan disubmit melalui submitForm() method
                event.preventDefault();
            }
        }
    }
</script>
@endsection