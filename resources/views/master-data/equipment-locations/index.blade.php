@extends('layouts.app')

@section('title', 'Daftar Equipment & Location')

@section('content')
    <div x-data="{
                        activeTab: 'equipmentLocations',
                        openEquipment: false,
                        openLocation: false,
                        openEquipmentLocation: false,
                        openEditEquipment: false,
                        openEditLocation: false,
                        openEditEquipmentLocation: false,
                        editEquipmentData: { id: null, name: '', description: '' },
                        editLocationData: { id: null, name: '', description: '' },
                        editEquipmentLocationData: { 
                            equipment_id: null, 
                            locationd: null, 
                            merkType: '', 
                            description: '',
                            old_equipment_id: null,  
                            old_location_id: null, 
                        },
                        }" class="mx-auto  p-6 min-h-screen">

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

                <!-- Tab Toggles -->
        <div class="mb-8">
            <div class="flex justify-center bg-gray-100 rounded-xl p-1.5 shadow-inner">
                <button @click="activeTab = 'equipmentLocations'"
                        :class="{'bg-white text-indigo-600 shadow-md': activeTab === 'equipmentLocations', 'text-gray-500': activeTab !== 'equipmentLocations'}"
                        class="w-full text-center px-4 py-3 rounded-lg font-semibold transition-all duration-300">
                    Relasi
                </button>
                <button @click="activeTab = 'equipment'"
                        :class="{'bg-white text-blue-600 shadow-md': activeTab === 'equipment', 'text-gray-500': activeTab !== 'equipment'}"
                        class="w-full text-center px-4 py-3 rounded-lg font-semibold transition-all duration-300">
                    Equipment
                </button>
                <button @click="activeTab = 'locations'"
                        :class="{'bg-white text-purple-600 shadow-md': activeTab === 'locations', 'text-gray-500': activeTab !== 'locations'}"
                        class="w-full text-center px-4 py-3 rounded-lg font-semibold transition-all duration-300">
                    Lokasi
                </button>
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

        <!-- Equipment Location Section -->
        <div x-show="activeTab === 'equipmentLocations'" class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold mb-1">Equipment Locations</h3>
                        <p class="text-indigo-100">Relasi antara peralatan dan lokasi</p>
                    </div>
                    <button @click="openEquipmentLocation = true"
                        class="bg-white text-indigo-600 hover:bg-indigo-50 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Relasi
                    </button>
                </div>
            </div>
            
            <!-- Mobile Card View -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 md:hidden">
                @foreach($equipmentLocations as $el)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">{{ $el->equipment->name ?? '-' }}</span>
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                  </svg>
                                <span class="px-3 py-1 text-xs font-semibold text-purple-800 bg-purple-100 rounded-full">{{ $el->location->name ?? '-' }}</span>
                            </div>
                            <div class="mt-4 space-y-2 text-sm text-gray-600">
                                <p><strong class="font-medium text-gray-800">Merk/Type:</strong> {{ $el->merk_type ?? 'N/A' }}</p>
                                <p><strong class="font-medium text-gray-800">Sertifikat:</strong> {{ $el->certificateInfo ?? 'N/A' }}</p>
                                <p><strong class="font-medium text-gray-800">Deskripsi:</strong> {{ $el->description ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3 flex justify-end space-x-2">
                            <button type="button"
                                class="p-2 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors duration-200"
                                @click="
                                    openEditEquipmentLocation = true;
                                    editEquipmentLocationData.equipment_id = {{ $el->equipment_id }};
                                    editEquipmentLocationData.location_id = {{ $el->location_id }};
                                    editEquipmentLocationData.old_equipment_id = {{ $el->equipment_id }};
                                    editEquipmentLocationData.old_location_id = {{ $el->location_id }};
                                    editEquipmentLocationData.merkType = '{{ addslashes($el->merkType ?? '') }}';
                                    editEquipmentLocationData.description = '{{ addslashes($el->description ?? '') }}';
                                ">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <form action="{{ route('equipment-location.destroy', $el->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus relasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop Table View -->
            <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hidden md:block">

                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Equipment</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Location</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Merk/Type</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Certificate</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Description</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($equipmentLocations as $equipmentLocation)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-2 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <span
                                            class="font-medium text-gray-900">{{ $equipmentLocation->equipment->name ?? '-'}}</span>
                                    </div>
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap">
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
                                        <span
                                            class="font-medium text-gray-900">{{ $equipmentLocation->location->name ?? '-'}}</span>
                                    </div>
                                </td>
                                <td class="px-2 py-4 text-gray-600">{{ $equipmentLocation->merk_type ?? 'Tidak ada merk' }}</td>
                                <td class="px-2 py-4 text-gray-600">
                                    {{ $equipmentLocation->certificateInfo ?? 'Tidak ada certificate info' }}
                                </td>
                                <td class="px-2 py-4 text-gray-600">
                                    {{ $equipmentLocation->description ?? 'Tidak ada deskripsi' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap flex space-x-2">
                                    <button type="button"
                                        class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-4 py-2 rounded-lg font-medium transition-colors duration-200"
                                        @click="
                                                                            openEditEquipmentLocation = true;
                                                                            editEquipmentLocationData.equipment_id = {{ $equipmentLocation->equipment_id }};
                                                                            editEquipmentLocationData.location_id = {{ $equipmentLocation->location_id }};
                                                                            editEquipmentLocationData.old_equipment_id = {{ $equipmentLocation->equipment_id }};
                                                                            editEquipmentLocationData.old_location_id = {{ $equipmentLocation->location_id }};
                                                                            editEquipmentLocationData.merkType = '{{ addslashes($equipmentLocation->merkType ?? '') }}';
                                                                            editEquipmentLocationData.description = '{{ addslashes($equipmentLocation->description ?? '') }}';
                                                                            ">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Edit
                                    </button>
                                    <form action="{{ route('equipment-location.destroy', $equipmentLocation->id) }}"
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
                    </tbody>
                </table>
                <div class="px-4 py-3 bg-white border-t border-gray-200">
                    {{ $equipmentLocations->links() }}
                </div>
            </div>
        </div>

        <!-- Equipment and Location Sections -->
        <div x-show="activeTab === 'equipment'">
            {{-- Equipment Section --}}
            <div
                class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-2xl mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-6 py-6 text-white">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div class="mb-4 sm:mb-0">
                            <h3 class="text-2xl font-bold mb-1">Equipment</h3>
                            <p class="text-blue-100">Kelola semua peralatan Anda</p>
                        </div>
                        <button @click="openEquipment = true"
                            class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Equipment
                        </button>
                    </div>
                </div>
                
                <!-- Mobile Card View -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 md:hidden">
                    @forelse($equipmentList as $equipment)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                            <div class="p-5">
                                <h4 class="text-lg font-bold text-gray-800 truncate">{{ $equipment->name }}</h4>
                                <p class="text-sm text-gray-500 mt-1">Dibuat oleh: {{ $equipment->creator->name ?? 'N/A' }}</p>
                                <p class="text-gray-600 mt-3 text-sm">{{ Str::limit($equipment->description, 100) }}</p>
                            </div>
                            <div class="bg-gray-50 px-5 py-3 flex justify-end space-x-2">
                                <button type="button" @click="
                                            openEditEquipment = true;
                                            editEquipmentData.id = {{ $equipment->id }};
                                            editEquipmentData.name = '{{ addslashes($equipment->name) }}';
                                            editEquipmentData.description = '{{ addslashes($equipment->description) }}';
                                        "
                                    class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('equipment.destroy', $equipment->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus?')"
                                        class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-1 sm:col-span-2 text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <p class="text-gray-500 text-lg">Belum ada data Equipment</p>
                            <p class="text-gray-400">Tambahkan equipment pertama Anda</p>
                        </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="overflow-x-auto p-6 hidden md:block">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-2 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    No</th>
                                <th
                                    class="px-2 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Nama</th>
                                <th
                                    class="px-2 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Deskripsi</th>
                                <th
                                    class="px-2 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Dibuat Oleh</th>
                                <th
                                    class="px-2 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($equipmentList as $equipment)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-2 py-4 text-blue-600 font-bold">{{ $equipment->id }}</td>
                                    <td class="px-2 py-4 text-gray-900 font-semibold">{{ $equipment->name }}</td>
                                    <td class="px-2 py-4 text-gray-600">{{ Str::limit($equipment->description, 80) }}</td>
                                    <td class="px-2 py-4 text-gray-500">{{ $equipment->creator->name ?? 'N/A' }}</td>
                                    <td class="px-2 py-4 flex space-x-2">
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-12 hidden md:table-cell"></td>
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p class="text-gray-500 text-lg">Belum ada data Equipment</p>
                                    <p class="text-gray-400">Tambahkan equipment pertama Anda</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="px-4 py-3 bg-white border-t border-gray-200">
                        {{ $equipmentList->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'locations'">
            {{-- Location Section --}}
            <div
                class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-2xl">
                <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-6 text-white">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div class="mb-4 sm:mb-0">
                            <h3 class="text-2xl font-bold mb-1">Locations</h3>
                            <p class="text-purple-100">Kelola semua lokasi Anda</p>
                        </div>
                        <button @click="openLocation = true"
                            class="bg-white text-purple-600 hover:bg-purple-50 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Lokasi
                        </button>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 md:hidden">
                    @forelse($locationList as $location)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                            <div class="p-5">
                                <h4 class="text-lg font-bold text-gray-800 truncate">{{ $location->name }}</h4>
                                <p class="text-sm text-gray-500 mt-1">Dibuat oleh: {{ $location->creator->name ?? 'N/A' }}</p>
                                <p class="text-gray-600 mt-3 text-sm">{{ Str::limit($location->description, 100) }}</p>
                            </div>
                            <div class="bg-gray-50 px-5 py-3 flex justify-end space-x-2">
                                <button type="button" @click="
                                            openEditLocation = true;
                                            editLocationData.id = {{ $location->id }};
                                            editLocationData.name = '{{ addslashes($location->name) }}';
                                            editLocationData.description = '{{ addslashes($location->description) }}';
                                        "
                                    class="p-2 bg-purple-100 text-purple-600 rounded-full hover:bg-purple-200 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('location.destroy', $location->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus?')"
                                        class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-1 sm:col-span-2 text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <p class="text-gray-500 text-lg">Belum ada data Location</p>
                            <p class="text-gray-400">Tambahkan lokasi pertama Anda</p>
                        </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="overflow-x-auto p-6 hidden md:block">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                No</th>
                                <th
                                    class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Nama</th>
                                <th
                                    class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Deskripsi</th>
                                <th
                                    class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Dibuat Oleh</th>
                                <th
                                    class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($locationList as $location)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-5 py-4 text-purple-600 font-bold">{{ $location->id }}</td>
                                    <td class="px-5 py-4 text-gray-900 font-semibold">{{ $location->name }}</td>
                                    <td class="px-5 py-4 text-gray-600">{{ Str::limit($location->description, 80) }}</td>
                                    <td class="px-5 py-4 text-gray-500">{{ $location->creator->name ?? 'N/A' }}</td>
                                    <td class="px-5 py-4 flex space-x-2">
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-12 hidden md:table-cell">
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
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="px-4 py-3 bg-white border-t border-gray-200">
                        {{ $locationList->links() }}
                    </div>
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
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Merk / Type</label>
                        <input type="text" name="merk_type"
                            class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors duration-200"
                            value="{{ old('merk_type') }}" placeholder="Masukkan merk/type (opsional)">
                        @error('merk_type')
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

        {{-- Modal Edit Equipment Location --}}
        <div x-show="openEditEquipmentLocation" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
            <div @click.away="openEditEquipmentLocation = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white p-6 rounded-t-2xl">
                    <h2 class="text-2xl font-bold">Edit Equipment Location</h2>
                    <p class="text-indigo-100">Ubah relasi equipment dan lokasi</p>
                </div>
                <form
                    :action="'/equipment-location/update/' + editEquipmentLocationData.old_equipment_id + '/' + editEquipmentLocationData.old_location_id"
                    method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Equipment</label>
                        <select name="equipment_id" required
                            class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors duration-200"
                            x-model="editEquipmentLocationData.equipment_id">
                            <option value="">Pilih Equipment</option>
                            @foreach($equipmentList as $equipment)
                                <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                        <select name="location_id" required
                            class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors duration-200"
                            x-model="editEquipmentLocationData.location_id">
                            <option value="">Pilih Location</option>
                            @foreach($locationList as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Merk / Type</label>
                        <input type="text" name="merk_type"
                            class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors duration-200"
                            x-model="editEquipmentLocationData.merk_type" placeholder="Masukkan merk/type (opsional)">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Relasi</label>
                        <textarea name="description" rows="3"
                            class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors duration-200"
                            x-model="editEquipmentLocationData.description"
                            placeholder="Masukkan deskripsi relasi (opsional)"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="openEditEquipmentLocation = false"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 font-medium shadow-lg">
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