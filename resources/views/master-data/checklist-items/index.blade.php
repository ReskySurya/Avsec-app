@extends('layouts.app')

@section('title', 'Master Data Checklist Items Kendaraan & Penyisiran')

@section('content')
<div x-data="{
    activeTab: 'kendaraan',
    openKendaraan: false,
    openPenyisiran: false,
    openEditKendaraan: false,
    openEditPenyisiran: false,
    editKendaraanData: { id: null, name: '', description: '' },
    editPenyisiranData: { id: null, name: '', type: '' },
    
    // Functions for handling edit modals
    openEditKendaraanModal(item) {
        this.editKendaraanData = {
            id: item.id,
            name: item.name,
            description: item.description
        };
        this.openEditKendaraan = true;
    },
    
    openEditPenyisiranModal(item) {
        this.editPenyisiranData = {
            id: item.id,
            name: item.name,
            type: item.type
        };
        this.openEditPenyisiran = true;
    }
}" class="mx-auto p-6 min-h-screen">

    <!-- Header Section with Enhanced Design -->
    <div class="mb-8 mt-20">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Master Data Checklist Items</h1>
                <p class="text-gray-600">Kelola master data checklist items kendaraan dan penyisiran</p>
            </div>
            <div class="flex space-x-2 mt-4 sm:mt-0">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border">
                    <span class="text-sm text-gray-500">Total Items Kendaraan:</span>
                    <span class="font-bold text-blue-600 ml-1">{{ $kendaraanItems->count() ?? 0 }}</span>
                </div>
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border">
                    <span class="text-sm text-gray-500">Total Items Penyisiran:</span>
                    <span class="font-bold text-purple-600 ml-1">{{ $penyisiranItems->count() ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Toggles -->
    <div class="mb-8">
        <div class="flex justify-center bg-gray-100 rounded-xl p-1.5 shadow-inner">
            <button @click="activeTab = 'kendaraan'"
                :class="{'bg-white text-blue-600 shadow-md': activeTab === 'kendaraan', 'text-gray-500': activeTab !== 'kendaraan'}"
                class="w-full text-center px-4 py-3 rounded-lg font-semibold transition-all duration-300">
                Items Kendaraan
            </button>
            <button @click="activeTab = 'penyisiran'"
                :class="{'bg-white text-purple-600 shadow-md': activeTab === 'penyisiran', 'text-gray-500': activeTab !== 'penyisiran'}"
                class="w-full text-center px-4 py-3 rounded-lg font-semibold transition-all duration-300">
                Items Penyisiran
            </button>
        </div>
    </div>

    <!-- Alert Messages with Enhanced Design -->
    @if(session('success'))
    <div class="bg-gradient-to-r from-green-400 to-green-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-green-600 animate-pulse">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-gradient-to-r from-red-400 to-red-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-red-600">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    <!-- Kendaraan Section -->
    <div x-show="activeTab === 'kendaraan'">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-2xl mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-6 py-6 text-white">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div class="mb-4 sm:mb-0">
                        <h3 class="text-2xl font-bold mb-1">Master Data Items Kendaraan</h3>
                        <p class="text-blue-100">Kelola semua checklist items untuk kendaraan</p>
                    </div>
                    <button @click="openKendaraan = true"
                        class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Item Kendaraan
                    </button>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 md:hidden">
                @forelse($kendaraanItems ?? [] as $item)
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-semibold text-gray-900 text-lg">{{ $item->name }}</h4>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Kendaraan</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">{{ $item->description ?? 'Tidak ada deskripsi' }}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">{{ $item->created_by ?? 'System' }}</span>
                        <div class="flex space-x-2">
                            <button @click="openEditKendaraanModal({{ $item }})"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Edit
                            </button>
                            <form action="{{ route('kendaraan.destroy', $item->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus item ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-8">
                    <div class="text-gray-400 mb-2">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500">Belum ada item kendaraan yang ditambahkan</p>
                </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="overflow-x-auto p-6 hidden md:block">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Item</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dibuat Oleh</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Dibuat</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($kendaraanItems ?? [] as $index => $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 max-w-xs">{{ $item->description ?? 'Tidak ada deskripsi' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->created_by ?? 'System' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->created_at->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openEditKendaraanModal({{ $item }})"
                                    class="text-blue-600 hover:text-blue-900 mr-3 transition-colors duration-200">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <form action="{{ route('kendaraan.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus item ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p>Belum ada item kendaraan yang ditambahkan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Penyisiran Section -->
    <div x-show="activeTab === 'penyisiran'">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-2xl">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-6 text-white">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div class="mb-4 sm:mb-0">
                        <h3 class="text-2xl font-bold mb-1">Master Data Items Penyisiran</h3>
                        <p class="text-purple-100">Kelola semua checklist items untuk penyisiran</p>
                    </div>
                    <button @click="openPenyisiran = true"
                        class="bg-white text-purple-600 hover:bg-purple-50 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Item Penyisiran
                    </button>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 md:hidden">
                @forelse($penyisiranItems ?? [] as $item)
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-semibold text-gray-900 text-lg">{{ $item->name }}</h4>
                        <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">{{ $item->type ?? 'Penyisiran' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">{{ $item->created_by ?? 'System' }}</span>
                        <div class="flex space-x-2">
                            <button @click="openEditPenyisiranModal({{ $item }})"
                                class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                Edit
                            </button>
                            <form action="{{ route('penyisiran.destroy', $item->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus item ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-8">
                    <div class="text-gray-400 mb-2">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500">Belum ada item penyisiran yang ditambahkan</p>
                </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="overflow-x-auto p-6 hidden md:block">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Item</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dibuat Oleh</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Dibuat</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($penyisiranItems ?? [] as $index => $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">
                                    {{ $item->type ?? 'Penyisiran' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->created_by ?? 'System' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->created_at->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openEditPenyisiranModal({{ $item }})"
                                    class="text-purple-600 hover:text-purple-900 mr-3 transition-colors duration-200">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <form action="{{ route('penyisiran.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus item ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p>Belum ada item penyisiran yang ditambahkan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Item Kendaraan -->
    <div x-show="openKendaraan" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openKendaraan = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Tambah Item Kendaraan</h2>
                <p class="text-blue-100">Tambahkan checklist item baru untuk kendaraan</p>
            </div>
            <form action="{{ route('kendaraan.store') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Item Penyisiran</label>
                    <input type="text" name="name" required
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-purple-500 focus:outline-none transition-colors duration-200"
                        value="{{ old('name') }}" placeholder="Contoh: Area Parkir, Kantin, Toilet, dll">
                    @error('name')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Item</label>
                    <select name="type" required
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-purple-500 focus:outline-none transition-colors duration-200">
                        <option value="">Pilih Tipe Item</option>
                        <option value="Area Dalam" {{ old('type') == 'Area Dalam' ? 'selected' : '' }}>Area Dalam</option>
                        <option value="Area Luar" {{ old('type') == 'Area Luar' ? 'selected' : '' }}>Area Luar</option>
                        <option value="Fasilitas Umum" {{ old('type') == 'Fasilitas Umum' ? 'selected' : '' }}>Fasilitas Umum</option>
                        <option value="Keamanan" {{ old('type') == 'Keamanan' ? 'selected' : '' }}>Keamanan</option>
                        <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('type')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openPenyisiran = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl hover:from-purple-600 hover:to-pink-700 transition-all duration-200 font-medium shadow-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Item Kendaraan -->
    <div x-show="openEditKendaraan" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEditKendaraan = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Edit Item Kendaraan</h2>
                <p class="text-blue-100">Ubah informasi item kendaraan</p>
            </div>
            <form :action="`
            {{ route('kendaraan.index') }}/${editKendaraanData.id}`" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Item Kendaraan</label>
                    <input type="text" name="name" required x-model="editKendaraanData.name"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                        placeholder="Masukkan nama item kendaraan">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="4" x-model="editKendaraanData.description"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                        placeholder="Masukkan deskripsi item kendaraan"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openEditKendaraan = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white rounded-xl hover:from-blue-600 hover:to-cyan-700 transition-all duration-200 font-medium shadow-lg">
                        Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Item Penyisiran -->
    <div x-show="openEditPenyisiran" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEditPenyisiran = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Edit Item Penyisiran</h2>
                <p class="text-purple-100">Ubah informasi item penyisiran</p>
            </div>
            <form :action="`{{ route('penyisiran.index') }}/${editPenyisiranData.id}`" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Item Penyisiran</label>
                    <input type="text" name="name" required x-model="editPenyisiranData.name"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-purple-500 focus:outline-none transition-colors duration-200"
                        placeholder="Masukkan nama item penyisiran">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Item</label>
                    <select name="type" required x-model="editPenyisiranData.type"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-purple-500 focus:outline-none transition-colors duration-200">
                        <option value="">Pilih Tipe Item</option>
                        <option value="Area Dalam">Area Dalam</option>
                        <option value="Area Luar">Area Luar</option>
                        <option value="Fasilitas Umum">Fasilitas Umum</option>
                        <option value="Keamanan">Keamanan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openEditPenyisiran = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl hover:from-purple-600 hover:to-pink-700 transition-all duration-200 font-medium shadow-lg">
                        Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- Custom Scrollbar Styles --}}
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
</style>

@push('styles')
<style>
    .pagination-wrapper nav {
        @apply flex justify-center mt-4;
    }

    .pagination-wrapper nav>div {
        @apply inline-flex shadow-sm rounded-md;
    }

    .pagination-wrapper nav>div>span,
    .pagination-wrapper nav>div>a {
        @apply px-4 py-2 text-sm font-medium border border-gray-200;
    }

    .pagination-wrapper nav>div>span:first-child,
    .pagination-wrapper nav>div>a:first-child {
        @apply rounded-l-md;
    }

    .pagination-wrapper nav>div>span:last-child,
    .pagination-wrapper nav>div>a:last-child {
        @apply rounded-r-md;
    }

    .pagination-wrapper nav>div>span.active {
        @apply bg-blue-50 text-blue-600 border-blue-300;
    }
</style>
@endpush

@endsection