@extends('layouts.app')

@section('title', 'Detail Form Pencatatan PI')

@section('content')
<div class="mx-auto p-0 sm:p-6 min-h-screen pt-5 sm:pt-20">
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
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="bg-gradient-to-r from-red-400 to-red-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg border-l-4 border-red-600 text-sm sm:text-base">
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
            <div class="flex justify-between items-center">
                <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                    class="w-20 h-20 mb-2 sm:mb-0">
                <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
                    CHECK LIST PENGUJIAN HARIAN<br>
                    FORM PENCATATAN <br>
                    PROHIBITED ITEM
                </h1>
                <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo" class="w-20 h-20 mt-2 sm:mt-0">
            </div>
        </div>
        <form id="updateForm" action="{{ route('checklist.pencatatanpi.update', $pencatatanPI->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" id="date" name="date" value="{{ old('date', $pencatatanPI->date ? $pencatatanPI->date->format('Y-m-d') : '') }}" required class="w-full border-2 bg-gray-100 border-gray-200 px-4 py-3 rounded-xl" readonly>
                </div>
                <div>
                    <label for="grup" class="block text-sm font-semibold text-gray-700 mb-2">Grup</label>
                    <input type="text" name="grup" value="{{ old('grup', $pencatatanPI->grup) }}" class="w-full border-2 bg-gray-100 border-gray-200 px-4 py-3 rounded-xl" readonly>
                </div>
                <div>
                    <label for="name_person" class="block text-sm font-semibold text-gray-700 mb-2">Nama Pemilik</label>
                    <input required type="text" id="name_person" name="name_person" value="{{ old('name_person', $pencatatanPI->name_person) }}" class="w-full border-2 bg-gray-100 border-gray-200 px-4 py-3 rounded-xl" readonly>
                </div>
                <div>
                    <label for="agency" class="block text-sm font-semibold text-gray-700 mb-2">Instansi</label>
                    <input type="text" id="agency" name="agency" value="{{ old('agency', $pencatatanPI->agency) }}" class="w-full border-2 bg-gray-100 border-gray-200 px-4 py-3 rounded-xl" readonly>
                </div>
                <div>
                    <label for="in_time" class="block text-sm font-semibold text-gray-700 mb-2">Jam Masuk</label>
                    <input required type="time" id="in_time" name="in_time" value="{{ old('in_time', $pencatatanPI->in_time) }}" class="w-full border-2 bg-gray-100 border-gray-200 px-4 py-3 rounded-xl" readonly>
                </div>
                <div>
                    <label for="out_time" class="block text-sm font-semibold text-gray-700 mb-2">Jam Keluar</label>
                    <input type="time" id="out_time" name="out_time" value="{{ old('out_time', $pencatatanPI->out_time) }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                </div>
            </div>

            <!-- PI Items Section -->
            <div class="mt-6 pt-4 border-t">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-md font-semibold text-gray-800">Item Prohibited</h3>
                </div>
                <div id="pi-items-container" class="space-y-3">
                    {{-- JS will populate this --}}
                </div>
            </div>

            <div class="md:col-span-2 mt-4">
                <label for="summary" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                <textarea id="summary" name="summary" rows="3" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200" placeholder="Masukkan Keterangan">{{ old('summary', $pencatatanPI->summary) }}</textarea>
            </div>

            <!-- Hidden inputs -->
            <input type="hidden" name="senderSignature" id="senderSignature">
            <input type="hidden" name="approved_id" id="approved_id">

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('checklist.pencatatanpi.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">Batal</a>
                <button type="button" id="open-modal-btn" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    {{-- Modal Konfirmasi --}}
    <div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
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
                        <button type="button" id="clear-signature-btn" class="text-sm text-blue-600 hover:underline">Hapus Tanda Tangan</button>
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
                    <button type="button" id="cancel-modal-btn" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-medium">Batal</button>
                    <button type="button" id="submit-form-btn" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-lg hover:from-blue-600 hover:to-teal-700 transition-colors font-medium shadow-lg">
                        Konfirmasi & Kirim
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="pi-item-template">
    <div class="flex items-start space-x-2 p-3 bg-gray-50 rounded-lg border-2 item-row">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 flex-grow">
            <div>
                <label class="text-xs font-medium text-gray-600" data-label="jenis_pi">Jenis PI</label>
                <input type="text" data-name="jenis_pi" required class="w-full mt-1 p-2 bg-gray-100 border-2 border-gray-200 rounded-md shadow-sm text-sm" readonly>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600" data-label="in_quantity">Jumlah Masuk</label>
                <input type="text" data-name="in_quantity" required class="w-full mt-1 p-2 bg-gray-100 border-2 border-gray-200 rounded-md shadow-sm text-sm" readonly>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600" data-label="out_quantity">Jumlah Keluar</label>
                <input type="text" data-name="out_quantity" class="w-full mt-1 p-2 border-2 border-gray-200 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0">
            </div>
        </div>
    </div>
</template>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Dynamic Items Script ---
    const container = document.getElementById('pi-items-container');
    const template = document.getElementById('pi-item-template');
    let itemIndex = 0;

    const addItemRow = (itemData) => {
        const newRow = template.content.cloneNode(true);
        const uniqueId = Date.now() + '_' + itemIndex;

        const jenisPiInput = newRow.querySelector('input[data-name="jenis_pi"]');
        jenisPiInput.name = `items[${itemIndex}][jenis_pi]`;
        jenisPiInput.id = `items_jenis_pi_${uniqueId}`;
        if (itemData && itemData.jenis_pi) jenisPiInput.value = itemData.jenis_pi;

        const inQuantityInput = newRow.querySelector('input[data-name="in_quantity"]');
        inQuantityInput.name = `items[${itemIndex}][in_quantity]`;
        inQuantityInput.id = `items_in_quantity_${uniqueId}`;
        if (itemData && itemData.in_quantity) inQuantityInput.value = itemData.in_quantity;

        const outQuantityInput = newRow.querySelector('input[data-name="out_quantity"]');
        outQuantityInput.name = `items[${itemIndex}][out_quantity]`;
        outQuantityInput.id = `items_out_quantity_${uniqueId}`;
        if (itemData) {
            if (itemData.out_quantity !== null && itemData.out_quantity !== undefined && itemData.out_quantity !== '') {
                outQuantityInput.value = itemData.out_quantity;
            } else {
                outQuantityInput.value = itemData.in_quantity;
            }
        }

        newRow.querySelector('label[data-label="jenis_pi"]').setAttribute('for', jenisPiInput.id);
        newRow.querySelector('label[data-label="in_quantity"]').setAttribute('for', inQuantityInput.id);
        newRow.querySelector('label[data-label="out_quantity"]').setAttribute('for', outQuantityInput.id);

        container.appendChild(newRow);
        itemIndex++;
    };

    const existingItems = {!! json_encode(old('items', $pencatatanPI->details), JSON_UNESCAPED_SLASHES) !!};
    if (existingItems && existingItems.length > 0) {
        container.innerHTML = '';
        existingItems.forEach(item => addItemRow(item));
    }

    // --- Modal and Signature Script ---
    const modal = document.getElementById('confirmation-modal');
    const openModalBtn = document.getElementById('open-modal-btn');
    const cancelModalBtn = document.getElementById('cancel-modal-btn');
    const submitFormBtn = document.getElementById('submit-form-btn');
    const clearSignatureBtn = document.getElementById('clear-signature-btn');
    const canvas = document.getElementById('signature-canvas');
    const signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });

    const resizeCanvas = () => {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    };

    openModalBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        resizeCanvas();
    });

    cancelModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
    clearSignatureBtn.addEventListener('click', () => signaturePad.clear());

    submitFormBtn.addEventListener('click', () => {
        const approvedId = document.getElementById('supervisor_id').value;
        const outTime = document.getElementById('out_time').value;

        if (!outTime) {
            alert('Mohon isi jam keluar.');
            return;
        }
        if (!approvedId) {
            alert('Mohon pilih supervisor yang mengetahui.');
            return;
        }
        if (signaturePad.isEmpty()) {
            alert('Tanda tangan petugas tidak boleh kosong.');
            return;
        }

        document.getElementById('senderSignature').value = signaturePad.toDataURL('image/png');
        document.getElementById('approved_id').value = approvedId;
        document.getElementById('updateForm').submit();
    });
});
</script>
@endpush
@endsection