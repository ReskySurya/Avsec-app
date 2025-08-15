@extends('layouts.app')
@section('title', 'Prohibited Items')
@section('content')
<div
    x-data="{
            openItem: false,
            openEditItem: false,
            editItemData: {
            id: null,
                tenantID: '{{ $tenantID }}',
                items_name: '',
                quantity: ''
            },
            tenantID: '{{ $tenantID }}',
            tenant_name: '{{ $tenant->tenant_name ?? 'Unknown Tenant' }}'
        }" class="mx-auto p-6 min-h-screen" class="mx-auto sm:mt-6 lg:mt-20 px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header -->
    <div class="mt-20">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
            <div class="">
                <!-- Back Button -->
                <div class="mb-4">
                    <a href="{{ route('tenant-management.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Tenant
                    </a>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Prohibited Items</h1>
                <p class="text-gray-600">Kelola item untuk Tenant <span class="font-semibold" x-text="tenant_name"></span></p>
                (ID: <span class="font-semibold" x-text="tenantID"></span>)
            </div>
            <!-- <div class="mt-4 sm:mt-0">
                <button type="button"
                    class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1"
                    @click="openItem = true">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Item
                </button>
            </div> -->
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        class="bg-gradient-to-r from-green-400 to-green-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-green-600 animate-pulse">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
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

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-6 py-6 text-white">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div class="mb-4 sm:mb-0">
                    <h3 class="text-2xl font-bold mb-1">Daftar Item</h3>
                    <span class="text-sm ">Total : {{ count($prohibitedItems ?? []) }} item</span>
                </div>
                <button @click="openItem = true"
                    class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Item
                </button>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Item</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($prohibitedItems ?? [] as $index => $item)
                    <tr class="hover:bg-gray-50 transition-all duration-200">
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">{{ $item->items_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-sm font-medium">
                                {{ $item->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap flex justify-center">
                            <div class="flex space-x-2">
                                <button type="button"
                                    class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-4 py-2 rounded-lg font-medium transition-colors duration-200"
                                    @click="
                                        openEditItem = true;
                                        editItemData.id = {{ $item->id }};
                                        editItemData.tenantID = '{{ $tenantID }}';
                                        editItemData.items_name = '{{ $item->items_name }}';
                                        editItemData.quantity = '{{ $item->quantity }}';
                                    ">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    Edit
                                </button>
                                <form action="{{ route('prohibited-items.destroy', $item->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus item ini?')"
                                        class="bg-red-100 text-red-600 hover:bg-red-200 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-gray-500 text-lg font-medium mb-1">Belum ada item</p>
                            <p class="text-gray-400">Tambahkan item pertama untuk tenant ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div x-show="openItem" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="openItem" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div x-show="openItem" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <form action="{{ route('prohibited-items.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tenantID" :value="tenantID">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Tambah Item </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Item</label>
                                    <input type="text" name="items_name" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        placeholder="Masukkan nama item">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah</label>
                                    <input type="number" name="quantity" required min="1"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        placeholder="Masukkan jumlah">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full sm:w-auto sm:ml-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                            Tambah Item
                        </button>
                        <button type="button" @click="openItem = false"
                            class="mt-3 sm:mt-0 w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-medium transition-colors duration-200">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div x-show="openEditItem" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="openEditItem" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div x-show="openEditItem" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <form x-bind:action="`/tenant-management/items/update/${editItemData.id}`" method="POST" class="p-6">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="tenantID" x-bind:value="editItemData.tenantID">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Edit Item </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Item</label>
                                    <input type="text" name="items_name" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        x-model="editItemData.items_name">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah</label>
                                    <input type="number" name="quantity" required min="1"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        x-model="editItemData.quantity">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="mx-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white rounded-xl hover:from-blue-600 hover:to-cyan-700 transition-all duration-200 font-medium shadow-lg">
                            Perbarui
                        </button>
                        <button type="button" @click="openEditItem = false"
                            class="mt-3 sm:mt-0 w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-medium transition-colors duration-200">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection