@extends('layouts.app')

@section('title', 'Tambah Form Pencatatan PI')

@section('content')
<div class="mx-auto p-2 sm:p-4 min-h-screen pt-5 sm:pt-20">

    @if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <p class="font-bold">Terjadi kesalahan:</p>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white w-full max-w-4xl mx-auto rounded-2xl shadow-2xl">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-4 sm:p-6 rounded-t-2xl">
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
        <form action="{{ route('checklist.pencatatanpi.store') }}" method="POST" class="p-3 sm:p-6">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                <div>
                    <label for="date" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Tanggal</label>
                    <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                </div>
                <div>
                    <label for="grup" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Grup</label>
                    <select required id="grup" name="grup" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                        <option value="">Pilih Grup</option>
                        <option value="A" @if(old('grup')=='A' ) selected @endif>A</option>
                        <option value="B" @if(old('grup')=='B' ) selected @endif>B</option>
                        <option value="C" @if(old('grup')=='C' ) selected @endif>C</option>
                    </select>
                </div>
                <div>
                    <label for="name_person" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Nama Pemilik</label>
                    <input required type="text" id="name_person" name="name_person" value="{{ old('name_person') }}" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base" placeholder="nama pemilik">
                </div>
                <div>
                    <label for="agency" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Instansi</label>
                    <input required type="text" id="agency" name="agency" value="{{ old('agency') }}" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base" placeholder="nama instansi">
                </div>
                <div>
                    <label for="in_time" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Jam Masuk</label>
                    <input required type="time" id="in_time" name="in_time" value="{{ old('in_time') }}" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                </div>
                <div>
                    <label for="out_time" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Jam Keluar</label>
                    <input readonly type="time" id="out_time" name="out_time" class="w-full bg-gray-100 border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                </div>
            </div>

            <!-- PI Items Section -->
            <div class="mt-6 pt-4 border-t">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-md font-semibold text-gray-800">Item Prohibited</h3>
                    <button type="button" id="add-item-btn" class="text-sm font-semibold text-white bg-blue-500 hover:bg-blue-600 px-3 py-2 rounded-lg shadow-sm">Tambah Item</button>
                </div>
                <div id="pi-items-container" class="space-y-3">
                    {{-- JS will populate this --}}
                </div>
            </div>

            <div class="sm:col-span-2 lg:col-span-3 mt-4">
                <label for="summary" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Keterangan</label>
                <textarea id="summary" name="summary" rows="2" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base" placeholder="Masukkan Keterangan">{{ old('summary') }}</textarea>
            </div>

            <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-4 sm:mt-6">
                <a href="{{ route('checklist.pencatatanpi.index') }}" class="w-full sm:w-auto text-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium text-sm sm:text-base">Batal</a>
                <button type="submit" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg text-sm sm:text-base">Simpan</button>
            </div>
        </form>
    </div>
</div>

<template id="pi-item-template">
    <div class="flex items-start space-x-2 p-3 bg-gray-50 rounded-lg border-2 item-row">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 flex-grow">
            <div>
                <label class="text-xs font-medium text-gray-600" data-label="jenis_pi">Jenis PI</label>
                <input type="text" data-name="jenis_pi" required class="w-full mt-1 p-2 border-2 border-gray-200 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600" data-label="in_quantity">Jumlah Masuk</label>
                <input type="text" data-name="in_quantity" required class="w-full mt-1 p-2 border-2 border-gray-200 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600" data-label="out_quantity">Jumlah Keluar</label>
                <input type="text" data-name="out_quantity" class="w-full mt-1 p-2 bg-gray-100 border-2 border-gray-200 rounded-md shadow-sm text-sm" placeholder="0" readonly>
            </div>
        </div>
        <div class="flex-shrink-0 pt-5">
            <button type="button" class="p-2 text-red-500 hover:bg-red-100 rounded-full remove-item-btn">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('pi-items-container');
        const addButton = document.getElementById('add-item-btn');
        const template = document.getElementById('pi-item-template');
        let itemIndex = 0;

        const addItemRow = (itemData = null) => {
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
            if (itemData && itemData.out_quantity) outQuantityInput.value = itemData.out_quantity;

            // Add labels 'for' attribute
            newRow.querySelector('label[data-label="jenis_pi"]').setAttribute('for', jenisPiInput.id);
            newRow.querySelector('label[data-label="in_quantity"]').setAttribute('for', inQuantityInput.id);
            newRow.querySelector('label[data-label="out_quantity"]').setAttribute('for', outQuantityInput.id);

            container.appendChild(newRow);
            itemIndex++;
        };

        container.addEventListener('click', function(e) {
            const removeButton = e.target.closest('.remove-item-btn');
            if (removeButton) {
                if (container.querySelectorAll('.item-row').length > 1) {
                    removeButton.closest('.item-row').remove();
                } else {
                    alert('Setidaknya harus ada satu item.');
                }
            }
        });

        addButton.addEventListener('click', () => addItemRow());

        const oldItems = {!!json_encode(old('items')) !!} || [];
        if (oldItems && oldItems.length > 0) {
            container.innerHTML = ''; // Clear initial
            oldItems.forEach(item => addItemRow(item));
        } else {
            addItemRow(); // Add one initial row
        }
    });
</script>
@endpush