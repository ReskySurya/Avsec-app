@extends('layouts.app')

@section('title', 'Check List Pengecekan Harian Kendaraan Patroli')
@section('content')
<div x-data="checklistData()" class="mx-auto p-4 sm:p-6 min-h-screen pt-5 sm:pt-20">
    <!-- Alert Messages -->
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

    <!-- Main Form Container -->
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
        <!-- Header with Logo -->
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

        <form method="POST" action="#" class="p-6 space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Operator -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Nama Operator Penerbangan:</label>
                    <input type="text" name="operator_name" value="Bandar Udara Adisutjipto Yogyakarta"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Tanggal & Waktu -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Tanggal & Waktu Pengujian:</label>
                    <div class="flex space-x-2">
                        <input type="datetime-local" name="test_datetime" x-model="testDateTime"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium">WIB
                        </div>
                    </div>
                </div>

                <!-- Jenis Kendaraan -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Jenis Kendaraan:</label>
                    <select name="vehicle_type" x-model="selectedVehicle"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="mobil">Mobil Patroli</option>
                        <option value="motor">Motor Patroli</option>
                    </select>
                </div>

                <!-- Shift -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Shift:</label>
                    <select name="shift" x-model="selectedShift"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="pagi">Shift Pagi</option>
                        <option value="siang">Shift Siang</option>
                    </select>
                </div>
            </div>

            <!-- Checklist Section -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4"
                    x-text="selectedVehicle === 'mobil' ? 'CHECK LIST PENGECEKAN HARIAN MOBIL PATROLI' : 'CHECK LIST PENGECEKAN HARIAN MOTOR PATROLI'">
                </h3>

                <!-- Mobile View -->
                <div class="block md:hidden space-y-4">
                    <template x-for="(category, categoryIndex) in getCurrentChecklist()" :key="categoryIndex">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="bg-blue-600 text-white px-4 py-3 font-semibold" x-text="category.name"></div>
                            <div class="p-4 space-y-3">
                                <template x-for="(item, itemIndex) in category.items" :key="itemIndex">
                                    <div class="border-b border-gray-200 pb-3">
                                        <div class="font-medium text-gray-800 mb-2" x-text="item.name"></div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-600 mb-2"
                                                    x-text="selectedShift === 'pagi' ? 'Kondisi Shift Pagi' : 'Kondisi Shift Siang'">
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
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Desktop View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="border border-gray-300 px-4 py-3 text-left font-semibold">NO</th>
                                <th class="border border-gray-300 px-4 py-3 text-left font-semibold">KETERANGAN</th>
                                <th class="border border-gray-300 px-4 py-3 text-center font-semibold"
                                    x-text="selectedShift === 'pagi' ? 'KONDISI SHIFT PAGI' : 'KONDISI SHIFT SIANG'">
                                </th>
                            </tr>
                            <tr class="bg-blue-500 text-white">
                                <th class="border border-gray-300 px-4 py-2"></th>
                                <th class="border border-gray-300 px-4 py-2"></th>
                                <th class="border border-gray-300 px-4 py-2">
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
                                    <td class="border border-gray-300 px-4 py-3">
                                        <template x-if="!row.isCategory">
                                            <div class="grid grid-cols-2 gap-1">
                                                <div class="text-center">
                                                    <input type="radio" :name="`items[${row.id}][is_ok]`"
                                                        :id="`desktop_item_ok_${row.id}`" value="1"
                                                        class="text-green-600 focus:ring-green-500">
                                                </div>
                                                <div class="text-center">
                                                    <input type="radio" :name="`items[${row.id}][is_ok]`"
                                                        :id="`desktop_item_not_ok_${row.id}`" value="0"
                                                        class="text-red-600 focus:ring-red-500">
                                                </div>
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <button type="button" onclick="window.history.back()"
                    class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200 font-medium">
                    Kembali
                </button>
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition duration-200 font-medium shadow-lg">
                    Simpan Checklist
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function checklistData() {
    return {
        selectedVehicle: 'mobil',
        selectedShift: 'pagi',
        testDateTime: new Date().toISOString().slice(0, 16),
        mobilChecklist: @json($mobilChecklist),
        motorChecklist: @json($motorChecklist),

        getCurrentChecklist() {
            return this.selectedVehicle === 'mobil' ? this.mobilChecklist : this.motorChecklist;
        },

        getFlattenedRows() {
            const checklist = this.getCurrentChecklist();
            if (!checklist || !Array.isArray(checklist)) return [];

            const rows = [];
            let itemNumber = 1;

            checklist.forEach((category, categoryIndex) => {
                // Add category header row
                rows.push({
                    index: `cat-${categoryIndex}`,
                    isCategory: true,
                    letter: category.letter || '',
                    name: category.name || 'UNCATEGORIZED'
                });

                // Add category items
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
        }
    }
}
</script>

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

    /* Radio button styling */
    input[type="radio"]:checked {
        background-color: currentColor;
    }
</style>
@endsection
