@extends('layouts.app')

@section('title', 'Daftar Equipment & Location')

@section('content')
<div x-data="{
        openEquipment: false,
        openLocation: false,
        openEquipmentLocation: false,
        openEditEquipment: false,
        openEditLocation: false,
        editEquipmentData: { id: null, name: '', description: '' },
        editLocationData: { id: null, name: '', description: '' }} "
    class="mx-auto  p-6 min-h-screen">

    <!-- Header Section with Enhanced Design -->
    <div class="mb-8 mt-20">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
            <div>
                <h1
                    class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                    Equipment & Location
                </h1>
                <p class="text-gray-600 text-lg">Kelola peralatan dan lokasi dengan mudah</p>
            </div>
            <div class="flex space-x-2 mt-4 sm:mt-0">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border">
                    <span class="text-sm text-gray-500">Total Equipment:</span>
                    <span class="font-bold text-blue-600 ml-1">{{ $equipmentList->count() }}</span>
                </div>
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border">
                    <span class="text-sm text-gray-500">Total Location:</span>
                    <span class="font-bold text-purple-600 ml-1">{{ $locationList->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages with Enhanced Design -->
    @if(session('success'))
    <div
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
    <div
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

    <!-- Equipment Location Section with Enhanced Design -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold mb-1">Equipment Locations</h3>
                    <p class="text-indigo-100">Relasi antara peralatan dan lokasi</p>
                </div>
                <button @click="openEquipmentLocation = true"
                    class="bg-white text-indigo-600 hover:bg-indigo-50 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Equipment Location
                </button>
            </div>
        </div>
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <div class="p-5">
                <form method="GET" action="{{ route('equipment-locations.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search_table" value="{{ request('search_table') }}"
                        placeholder="Cari location di tabel..."
                        class="border border-gray-300 rounded-lg px-4 py-2 w-64" />
                    <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Cari
                    </button>
                </form>
            </div>

            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Equipment</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Location</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Description</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($equipmentLocations as $equipment)
                    @foreach($equipment->locations as $location)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900">{{ $equipment->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900">{{ $location->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $location->pivot->description ?: 'No description' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form
                                action="{{ route('equipment-location.destroy', ['equipmentId' => $equipment->id, 'locationId' => $location->id]) }}"
                                method="POST" onsubmit="return confirm('Yakin ingin menghapus relasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-100 text-red-600 hover:bg-red-200 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>

                    @endforeach
                    @endforeach

                </tbody>
            </table>
            <div class="px-4 py-3 bg-white border-t border-gray-200">
                {{ $equipmentLocations->links() }}
            </div>
        </div>
    </div>

    <!-- Equipment and Location Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        {{-- Equipment Section --}}
        <div
            class="bg-white shadow-xl rou1nded-2xl overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-6 py-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold mb-1">Equipment</h3>
                        <p class="text-blue-100">Kelola peralatan</p>
                    </div>
                    <button @click="openEquipment = true"
                        class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </nav>
                            </path>
                        </svg>
                        Tambah
                    </button>
                </div>
            </div>
            <div class="p-6">
                @if($equipmentList->count())
                <div class="space-y-4">
                    <form method="GET" action="{{ route('equipment-locations.index') }}"
                        class="flex items-center gap-2">
                        <input type="text" name="search_equipment" value="{{ request('search_equipment') }}"
                            placeholder="Cari equipment di tabel..."
                            class="border border-gray-300 rounded-lg px-4 py-2 w-64" />
                        <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Cari
                        </button>
                    </form>

                    @foreach($equipmentList as $equipment)
                    <div
                        class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <span class="text-blue-600 font-bold text-sm">#{{ $equipment->id }}</span>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $equipment->name }}</h4>
                                </div>
                                <p class="text-gray-600 mb-2">{{ Str::limit($equipment->description, 80) }}</p>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                    {{ $equipment->creator->name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <button type="button" @click="
                                                                                                            openEditEquipment = true;
                                                                                                            editEquipmentData.id = {{ $equipment->id }};
                                                                                                            editEquipmentData.name = '{{ addslashes($equipment->name) }}';
                                                                                                            editEquipmentData.description = '{{ addslashes($equipment->description) }}';
                                                                                                        "
                                    class="bg-blue-100 text-blue-600 hover:bg-blue-200 p-2 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                                <form action="{{ route('equipment.destroy', $equipment->id) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus?')"
                                        class="bg-red-100 text-red-600 hover:bg-red-200 p-2 rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="px-4 py-3 bg-white border-t border-gray-200">
                        {{ $equipmentList->links() }}
                    </div>
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    <p class="text-gray-500 text-lg">Belum ada data Equipment</p>
                    <p class="text-gray-400">Tambahkan equipment pertama Anda</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Location Section --}}
        <div
            class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-2xl">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold mb-1">Locations</h3>
                        <p class="text-purple-100">Kelola lokasi</p>
                    </div>
                    <button @click="openLocation = true"
                        class="bg-white text-purple-600 hover:bg-purple-50 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Tambah
                    </button>
                </div>
            </div>
            <div class="p-6">
                @if($locationList->count())
                <div class="space-y-4">
                    <form method="GET" action="{{ route('equipment-locations.index') }}"
                        class="flex items-center gap-2">
                        <input type="text" name="search_location" value="{{ request('search_location') }}"
                            placeholder="Cari location di tabel..."
                            class="border border-gray-300 rounded-lg px-4 py-2 w-64" />
                        <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Cari
                        </button>
                    </form>

                    @foreach($locationList as $location)
                    <div
                        class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                        <span class="text-purple-600 font-bold text-sm">#{{ $location->id }}</span>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $location->name }}</h4>
                                </div>
                                <p class="text-gray-600 mb-2">{{ Str::limit($location->description, 80) }}</p>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                    {{ $location->creator->name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <button type="button" @click="
                                                                        openEditLocation = true;
                                                                        editLocationData.id = {{ $location->id }};
                                                                        editLocationData.name = '{{ addslashes($location->name) }}';
                                                                        editLocationData.description = '{{ addslashes($location->description) }}';
                                                                    "
                                    class="bg-purple-100 text-purple-600 hover:bg-purple-200 p-2 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                                <form action="{{ route('location.destroy', $location->id) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus?')"
                                        class="bg-red-100 text-red-600 hover:bg-red-200 p-2 rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="px-4 sm:px-6 py-3 bg-white border-t border-gray-200 flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <button
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Sebelumnya</button>
                            <button
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Selanjutnya</button>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">Menampilkan <span class="font-medium">1</span> sampai
                                    <span class="font-medium">{{ count($locationList) }}</span> dari <span
                                        class="font-medium">{{count($locationList) }}</span> hasil
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                    aria-label="Pagination">
                                    <button
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Sebelumnya</button>
                                    <button
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">1</button>
                                    <button
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Selanjutnya</button>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">Belum ada data Location</p>
                    <p class="text-gray-400">Tambahkan lokasi pertama Anda</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Enhanced Modals --}}
    {{-- Modal Tambah Equipment --}}
    <div x-show="openEquipment" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEquipment = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Tambah Equipment</h2>
                <p class="text-blue-100">Tambahkan peralatan baru</p>
            </div>
            <form action="{{ route('equipment.store') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Equipment</label>
                    <input type="text" name="name" required
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                        value="{{ old('name') }}" placeholder="Masukkan nama equipment">
                    @error('name')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="4"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                        placeholder="Masukkan deskripsi equipment">{{ old('description') }}</textarea>
                    @error('description')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openEquipment = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white rounded-xl hover:from-blue-600 hover:to-cyan-700 transition-all duration-200 font-medium shadow-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tambah Location --}}
    <div x-show="openLocation" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openLocation = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Tambah Location</h2>
                <p class="text-purple-100">Tambahkan lokasi baru</p>
            </div>
            <form action="{{ route('location.store') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Location</label>
                    <input type="text" name="name" required
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-purple-500 focus:outline-none transition-colors duration-200"
                        value="{{ old('name') }}" placeholder="Masukkan nama lokasi">
                    @error('name')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="4"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-purple-500 focus:outline-none transition-colors duration-200"
                        placeholder="Masukkan deskripsi lokasi">{{ old('description') }}</textarea>
                    @error('description')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openLocation = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl hover:from-purple-600 hover:to-pink-700 transition-all
                                                                                                    duration-200 font-medium shadow-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tambah Equipment Location --}}
    <div x-show="openEquipmentLocation" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEquipmentLocation = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Tambah Equipment Location</h2>
                <p class="text-indigo-100">Hubungkan equipment dengan lokasi</p>
            </div>
            <form action="{{ route('equipment-location.store') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Equipment</label>
                    <select name="equipment_id" required
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors duration-200">
                        <option value="">Pilih Equipment</option>
                        @foreach($equipmentList as $equipment)
                        <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                        @endforeach
                    </select>
                    @error('equipment_id')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                    <select name="location_id" required
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors duration-200">
                        <option value="">Pilih Location</option>
                        @foreach($locationList as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                    @error('location_id')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Relasi</label>
                    <textarea name="description" rows="3"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors duration-200"
                        placeholder="Masukkan deskripsi relasi (opsional)">{{ old('description') }}</textarea>
                    @error('description')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openEquipmentLocation = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 font-medium shadow-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Equipment --}}
    <div x-show="openEditEquipment" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEditEquipment = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Edit Equipment</h2>
                <p class="text-blue-100">Ubah informasi equipment</p>
            </div>
            <form action="{{ route('equipment.update', $equipment->id) }}" method="POST" class="p-6">
                @csrf
                @method('POST')
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Equipment</label>
                    <input type="text" name="name" required x-model="editEquipmentData.name"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                        placeholder="Masukkan nama equipment">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="4" x-model="editEquipmentData.description"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                        placeholder="Masukkan deskripsi equipment"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openEditEquipment = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white rounded-xl hover:from-blue-600 hover:to-cyan-700 transition-all duration-200 font-medium shadow-lg">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Location --}}
    <div x-show="openEditLocation" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEditLocation = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Edit Location</h2>
                <p class="text-purple-100">Ubah informasi lokasi</p>
            </div>
            <form action="{{ route('location.update', $location->id) }}" method="POST" class="p-6">
                @csrf
                @method('POST')
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Location</label>
                    <input type="text" name="name" required x-model="editLocationData.name"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-purple-500 focus:outline-none transition-colors duration-200"
                        placeholder="Masukkan nama lokasi">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="4" x-model="editLocationData.description"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-purple-500 focus:outline-none transition-colors duration-200"
                        placeholder="Masukkan deskripsi lokasi"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openEditLocation = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl hover:from-purple-600 hover:to-pink-700 transition-all duration-200 font-medium shadow-lg">
                        Update
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
    .pagination-wrapper nav > div {
        @apply inline-flex shadow-sm rounded-md;
    }
    .pagination-wrapper nav > div > span,
    .pagination-wrapper nav > div > a {
        @apply px-4 py-2 text-sm font-medium border border-gray-200;
    }
    .pagination-wrapper nav > div > span:first-child,
    .pagination-wrapper nav > div > a:first-child {
        @apply rounded-l-md;
    }
    .pagination-wrapper nav > div > span:last-child,
    .pagination-wrapper nav > div > a:last-child {
        @apply rounded-r-md;
    }
    .pagination-wrapper nav > div > span.active {
        @apply bg-blue-50 text-blue-600 border-blue-300;
    }
</style>
@endpush
@endsection
