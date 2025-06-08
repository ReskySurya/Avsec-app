@extends('layouts.app')

@section('title', 'Daftar Equipment & Location')

@section('content')
    <div 
    x-data="{ 
    openEquipment: false,
    openLocation: false, 
    openEquipmentLocation: false, 
    openEditEquipment: false,
    openEditLocation: false,
    editEquipmentData: { id: null, name: '', description: '' },
    editLocationData: { id: null, name: '', description: '' }} " 
    class=" mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-700">Equipment & Location Management</h2>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-700">Daftar Equipment Location</h3>
                <button @click="openEquipmentLocation = true"
                    class="text-white bg-green-500 hover:bg-green-600 px-3 py-1 rounded text-sm">
                    + Tambah
                </button>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-gray-600 uppercase">
                        <tr>
                            <th class="px-4 py-2">Equipment</th>
                            <th class="px-4 py-2">Location</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipments as $equipment)
                            @foreach($equipment->locations as $location)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $equipment->name }}</td>
                                    <td class="px-4 py-2">{{ $location->name }}</td>
                                
                                    <td class="px-4 py-2">{{ $location->pivot->description }}</td>
                                    <td class="px-4 py-2">
                                       <form action="{{ route('equipment-location.destroy', ['equipmentId' => $equipment->id, 'locationId' => $location->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus relasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Equipment Section --}}
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-700">Daftar Equipment</h3>
                    <button @click="openEquipment = true"
                        class="text-white bg-green-500 hover:bg-green-600 px-3 py-1 rounded text-sm">
                        + Tambah
                    </button>
                </div>
                <div class="overflow-x-auto p-4">
                    @if($equipments->count())
                        <table class="min-w-full text-sm text-left text-gray-700">
                            <thead class="bg-gray-100 text-gray-600 uppercase">
                                <tr>
                                    <th class="px-4 py-2">Id</th>
                                    <th class="px-4 py-2">Nama</th>
                                    <th class="px-4 py-2">Deskripsi</th>
                                    <th class="px-4 py-2 ">Pembuat</th>
                                    <th class="px-4 py-2">Aksi</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($equipments as $equipment)
                                    <tr class="border-b">
                                        <td class="px-4 py-2 font-medium">{{ $equipment->id }}</td>
                                        <td class="px-4 py-2 font-medium">{{ $equipment->name }}</td>
                                        <td class="px-4 py-2">{{ Str::limit($equipment->description, 50) }}</td>
                                        <td class="px-4 py-2">{{ $equipment->creator->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2">
                                            <button type="button" 
                                                @click="
                                                    openEditEquipment = true;
                                                    editEquipmentData.id = {{ $equipment->id }};
                                                    editEquipmentData.name = '{{ addslashes($equipment->name) }}';
                                                    editEquipmentData.description = '{{ addslashes($equipment->description) }}';
                                                "
                                                class="text-blue-500 hover:underline">Edit
                                            </button>

                                            <form action="{{ route('equipment.destroy', $equipment->id) }}" method="POST" class="inline-block ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-500 hover:underline">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-gray-500 py-4">Belum ada data Equipment.</p>
                    @endif
                </div>
            </div>

            {{-- Location Section --}}
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-700">Daftar Location</h3>
                    <button @click="openLocation = true"
                        class="text-white bg-green-500 hover:bg-green-600 px-3 py-1 rounded text-sm">
                        + Tambah
                    </button>
                </div>
                <div class="overflow-x-auto p-4">
                    @if($locations->count())
                        <table class="min-w-full text-sm text-left text-gray-700">
                            <thead class="bg-gray-100 text-gray-600 uppercase">
                                <tr>
                                     <th class="px-4 py-2">Id</th>
                                    <th class="px-4 py-2">Nama</th>
                                    <th class="px-4 py-2">Deskripsi</th>
                                    <th class="px-4 py-2">Pembuat</th>
                                    <th class="px-4 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($locations as $location)
                                    <tr class="border-b">
                                        <td class="px-4 py-2 font-medium">{{ $location->id }}</td>
                                        <td class="px-4 py-2 font-medium">{{ $location->name }}</td>
                                        <td class="px-4 py-2">{{ Str::limit($location->description, 50) }}</td>
                                        <td class="px-4 py-2">{{ $location->creator->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2">
                                           <button type="button" 
                                                @click="
                                                    openEditLocation = true;
                                                    editLocationData.id = {{ $location->id }};
                                                    editLocationData.name = '{{ addslashes($location->name) }}';
                                                    editLocationData.description = '{{ addslashes($location->description) }}';
                                                "
                                                class="text-blue-500 hover:underline">Edit
                                            </button>

                                            <form action="{{ route('location.destroy', $location->id) }}" method="POST" class="inline-block ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-500 hover:underline">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-gray-500 py-4">Belum ada data Location.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Tambah Equipment --}}
        <div x-show="openEquipment" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div @click.away="openEquipment = false" class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                <h2 class="text-lg font-bold mb-4">Tambah Equipment</h2>
                <form action="{{ route('equipment.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input type="text" name="name" required class="w-full border px-3 py-2 rounded" value="{{ old('name') }}">
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="description" class="w-full border px-3 py-2 rounded">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="button" @click="openEquipment = false"
                            class="mr-2 px-4 py-2 bg-gray-300 rounded">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Tambah Location --}}
        <div x-show="openLocation" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div @click.away="openLocation = false" class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                <h2 class="text-lg font-bold mb-4">Tambah Location</h2>
                <form action="{{ route('location.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input type="text" name="name" required class="w-full border px-3 py-2 rounded" value="{{ old('name') }}">
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="description" class="w-full border px-3 py-2 rounded">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="button" @click="openLocation = false"
                            class="mr-2 px-4 py-2 bg-gray-300 rounded">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Tambah Equipment Location --}}
        <div x-show="openEquipmentLocation" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div @click.away="openEquipmentLocation = false" class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                <h2 class="text-lg font-bold mb-4">Tambah Equipment Location</h2>
                <form action="{{ route('equipment-location.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Equipment</label>
                        <select name="equipment_id" required class="w-full border px-3 py-2 rounded">
                            <option value="">Pilih Equipment</option>
                            @foreach($equipments as $equipment)
                                <option value="{{ $equipment->id }}" {{ old('equipment_id') == $equipment->id ? 'selected' : '' }}>
                                    {{ $equipment->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('equipment_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Location</label>
                        <select name="location_id" required class="w-full border px-3 py-2 rounded">
                            <option value="">Pilih Location</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('location_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="description" class="w-full border px-3 py-2 rounded">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="button" @click="openEquipmentLocation = false"
                            class="mr-2 px-4 py-2 bg-gray-300 rounded">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Equipment --}}
        <div x-show="openEditEquipment" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div @click.away="openEditEquipment = false" class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                <h2 class="text-lg font-bold mb-4">Edit Equipment</h2>
                <form action="{{ route('equipment.update',$equipment->id) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input type="text" name="name" required class="w-full border px-3 py-2 rounded"
                            x-model="editEquipmentData.name">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="description" class="w-full border px-3 py-2 rounded"
                            x-model="editEquipmentData.description"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" @click="openEditEquipment = false"
                            class="mr-2 px-4 py-2 bg-gray-300 rounded">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Location --}}
        <div x-show="openEditLocation" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div @click.away="openEditLocation = false" class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                <h2 class="text-lg font-bold mb-4">Edit Location</h2>
                <form action="{{ route('location.update',$location->id) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input type="text" name="name" required class="w-full border px-3 py-2 rounded"
                            x-model="editLocationData.name">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="description" class="w-full border px-3 py-2 rounded"
                            x-model="editLocationData.description"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" @click="openEditLocation = false"
                            class="mr-2 px-4 py-2 bg-gray-300 rounded">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection