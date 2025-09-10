@extends('layouts.app')

@section('title', 'Check List Senjata Api')

@section('content')
<div class="mx-auto p-2 sm:p-4 min-h-screen pt-5 sm:pt-20" x-data="{
    showModal: false,
    editMode: false,
    editData: {
        id: null,
        date: null,
        name: null,
        agency: null,
        flightNumber: null,
        destination: null,
        typeSenpi: null,
        quantitySenpi: null,
        quantityMagazine: null,
        quantityBullet: null,
        licenseNumber: null
    },
    openAddModal() {
        this.editMode = false;
        this.editData = {
            id: null,
            date: null,
            name: null,
            agency: null,
            flightNumber: null,
            destination: null,
            typeSenpi: null,
            quantitySenpi: null,
            quantityMagazine: null,
            quantityBullet: null,
            licenseNumber: null
        };
        this.showModal = true;
    },
    openEditModal(data) {
        this.editMode = true;
        this.editData = { ...data };
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
        this.editMode = false;
        this.editData = {
            id: null,
            date: null,
            name: null,
            agency: null,
            flightNumber: null,
            destination: null,
            typeSenpi: null,
            quantitySenpi: null,
            quantityMagazine: null,
            quantityBullet: null,
            licenseNumber: null
        };
    }
}">

    {{-- Alert Sukses --}}
    @if(session('success'))
    <div
        class="bg-green-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg text-sm sm:text-base">
        {{ session('success') }}
    </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
    <div class="bg-red-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg text-sm sm:text-base">
        {{ session('error') }}
    </div>
    @endif

    {{-- Menampilkan Error Validasi --}}
    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-3 rounded-xl relative mb-4 sm:mb-6 shadow-lg text-sm sm:text-base"
        role="alert">
        <strong class="font-bold">Oops! Terjadi kesalahan.</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Modal Tambah/Edit Data --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-2"
        @keydown.escape.window="closeModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs sm:max-w-2xl lg:max-w-4xl max-h-[90vh] overflow-y-auto"
            @click.away="closeModal()">
            <div
                class="bg-gradient-to-r from-blue-500 to-teal-600 px-4 sm:px-6 py-4 sm:py-6 text-white sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <h1 class="text-lg sm:text-2xl font-bold"
                        x-text="editMode ? 'Edit Data Checklist Senjata Api' : 'Tambah Data Checklist Senjata Api'">
                    </h1>
                    <button @click="closeModal()"
                        class="text-white hover:text-gray-200 text-2xl sm:text-3xl">&times;</button>
                </div>
            </div>

            <form method="POST"
                :action="editMode ? '{{ url('/checklist-senpi/update') }}/' + editData.id : '{{ route('checklist.senpi.store') }}'"
                class="p-3 sm:p-6 space-y-4 sm:space-y-6">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="POST">
                </template>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-5">
                    {{-- Form fields --}}
                    <div class="space-y-1 sm:space-y-2">
                        <label for="date" class="block text-xs sm:text-sm font-semibold text-gray-700">Tanggal</label>
                        <input type="date" name="date" id="date" x-bind:value="editMode ?
                            (editData.date ? editData.date.split('T')[0] : '') :
                            '{{ old('date', now()->format('Y-m-d')) }}'" required
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                    </div>
                    <div class="space-y-1 sm:space-y-2">
                        <label for="name" class="block text-xs sm:text-sm font-semibold text-gray-700">Nama</label>
                        <input type="text" name="name" id="name" :value="editMode ? editData.name : '{{ old('name') }}'"
                            placeholder="Nama Pemilik" required
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                    </div>
                    <div class="space-y-1 sm:space-y-2">
                        <label for="agency"
                            class="block text-xs sm:text-sm font-semibold text-gray-700">Instansi</label>
                        <input type="text" name="agency" id="agency"
                            :value="editMode ? editData.agency : '{{ old('agency') }}'" placeholder="Instansi"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                    </div>
                    <div class="space-y-1 sm:space-y-2">
                        <label for="flightNumber" class="block text-xs sm:text-sm font-semibold text-gray-700">No.
                            Penerbangan</label>
                        <input type="text" name="flightNumber" id="flightNumber"
                            :value="editMode ? editData.flightNumber : '{{ old('flightNumber') }}'" placeholder="GA-XXX"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                    </div>
                    <div class="space-y-1 sm:space-y-2">
                        <label for="destination"
                            class="block text-xs sm:text-sm font-semibold text-gray-700">Tujuan</label>
                        <input type="text" name="destination" id="destination"
                            :value="editMode ? editData.destination : '{{ old('destination') }}'"
                            placeholder="Tujuan Penerbangan"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                    </div>
                    <div class="space-y-1 sm:space-y-2">
                        <label for="typeSenpi" class="block text-xs sm:text-sm font-semibold text-gray-700">Jenis
                            Senpi</label>
                        <input type="text" name="typeSenpi" id="typeSenpi"
                            :value="editMode ? editData.typeSenpi : '{{ old('typeSenpi') }}'"
                            placeholder="Jenis Senjata"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                    </div>
                    <div class="space-y-1 sm:space-y-2">
                        <label for="quantitySenpi" class="block text-xs sm:text-sm font-semibold text-gray-700">Jml
                            Senpi</label>
                        <input type="number" name="quantitySenpi" id="quantitySenpi"
                            :value="editMode ? editData.quantitySenpi : '{{ old('quantitySenpi') }}'" placeholder="0"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                    </div>
                    <div class="space-y-1 sm:space-y-2">
                        <label for="quantityMagazine" class="block text-xs sm:text-sm font-semibold text-gray-700">Jml
                            Magazen</label>
                        <input type="number" name="quantityMagazine" id="quantityMagazine"
                            :value="editMode ? editData.quantityMagazine : '{{ old('quantityMagazine') }}'"
                            placeholder="0"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                    </div>
                    <div class="space-y-1 sm:space-y-2">
                        <label for="quantityBullet" class="block text-xs sm:text-sm font-semibold text-gray-700">Jml
                            Peluru</label>
                        <input type="number" name="quantityBullet" id="quantityBullet"
                            :value="editMode ? editData.quantityBullet : '{{ old('quantityBullet') }}'" placeholder="0"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                    </div>
                    <div class="space-y-1 sm:space-y-2 sm:col-span-2">
                        <label for="licenseNumber" class="block text-xs sm:text-sm font-semibold text-gray-700">No.
                            Surat Izin</label>
                        <input type="text" name="licenseNumber" id="licenseNumber"
                            :value="editMode ? editData.licenseNumber : '{{ old('licenseNumber') }}'"
                            placeholder="Nomor Surat Izin"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                    </div>
                </div>

                <div
                    class="flex flex-col sm:flex-row justify-end sticky bottom-0 bg-white px-3 sm:px-6 pt-3 sm:pt-6 pb-3 space-y-2 sm:space-y-0 sm:space-x-4">
                    <button type="button" @click="closeModal()"
                        class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm sm:text-base">
                        Batal
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto px-4 sm:px-8 py-2 sm:py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-lg text-sm sm:text-base">
                        <span x-text="editMode ? 'Perbarui Data' : 'Simpan Data'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Mobile: Card Layout --}}
    <div class="block lg:hidden space-y-3 mb-6">
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-3">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-lg font-bold text-gray-800">Data Checklist Senjata Api</h2>
                <button @click="openAddModal()"
                    class="px-3 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow text-sm">
                    Tambah
                </button>
            </div>
        </div>

        @forelse($senpi as $index => $item)
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-bold mr-2">
                        {{ $index + 1 }}
                    </span>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $item->formatted_date }}</p>
                    </div>
                </div>
                @if(auth()->user()->role->name === 'superadmin')
                <div class="flex space-x-1">
                    <button @click="openEditModal(JSON.parse('{{ json_encode([
                            'id' => $item->id,
                            'date' => $item->date,
                            'name' => $item->name ?? '',
                            'agency' => $item->agency ?? '',
                            'flightNumber' => $item->flightNumber ?? '',
                            'destination' => $item->destination ?? '',
                            'typeSenpi' => $item->typeSenpi ?? '',
                            'quantitySenpi' => $item->quantitySenpi ?? 0,
                            'quantityMagazine' => $item->quantityMagazine ?? 0,
                            'quantityBullet' => $item->quantityBullet ?? 0,
                            'licenseNumber' => $item->licenseNumber ?? ''
                        ], JSON_HEX_APOS | JSON_HEX_QUOT) }}'))"
                        class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-full transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <form method="POST" action="{{ route('checklist.senpi.destroy', $item->id) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-full transition-colors"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <div class="space-y-2 text-xs">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="text-gray-500">Instansi</p>
                        <p class="font-medium">{{ $item->agency ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Penerbangan</p>
                        <p class="font-medium">{{ $item->flightNumber ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="text-gray-500">Tujuan</p>
                        <p class="font-medium">{{ $item->destination ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Jenis Senpi</p>
                        <p class="font-medium">{{ $item->typeSenpi ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100">
                    <div class="text-center">
                        <p class="text-gray-500 text-xs">Senpi</p>
                        <p class="font-bold text-blue-600">{{ $item->quantitySenpi ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-500 text-xs">Magazen</p>
                        <p class="font-bold text-blue-600">{{ $item->quantityMagazine ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-500 text-xs">Peluru</p>
                        <p class="font-bold text-blue-600">{{ $item->quantityBullet ?? 0 }}</p>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100">
                    <p class="text-gray-500 text-xs">No. Surat Izin</p>
                    <p class="font-medium break-words">{{ $item->licenseNumber ?? '-' }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-8 text-center">
            <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <h3 class="text-base font-semibold text-gray-500 mb-1">Belum Ada Data</h3>
            <p class="text-xs text-gray-400">Data checklist akan muncul di sini setelah dibuat</p>
        </div>
        @endforelse
    </div>

    {{-- Desktop: Table Layout --}}
    <div class="hidden lg:block bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200 p-5">
        <div class="px-4 sm:px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Data Checklist Senjata Api</h2>
            <button @click="openAddModal()"
                class="px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                Tambah Data
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="w-full">
                    <tr class="bg-blue-600 text-white text-sm">
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider" rowspan="2">
                            No</th>
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider" rowspan="2">
                            Tanggal</th>
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider" rowspan="2">
                            Nama</th>
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider" rowspan="2">
                            Instansi</th>
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider" rowspan="2">
                            No. Penerbangan</th>
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider" rowspan="2">
                            Tujuan</th>
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider" rowspan="2">
                            Jenis Senpi</th>
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider" colspan="3">
                            Jumlah</th>
                        @if(auth()->user()->role->name === 'superadmin')
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider" rowspan="2">
                            No. Surat Izin Kepemilikan</th>
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider" rowspan="2">
                            Aksi</th>
                        @else
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider" rowspan="2"
                            colspan="2">No. Surat Izin Kepemilikan</th>
                        @endif
                    </tr>
                    <tr class="bg-blue-500 text-white text-sm">
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider">Senpi</th>
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider">Magazine
                        </th>
                        <th class="border-2 border-gray-400 px-3 py-3 text-center uppercase tracking-wider">Peluru</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($senpi as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">{{ $index + 1 }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">{{
                            $item->formatted_date }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->name }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->agency }}
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->flightNumber
                            }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->destination
                            }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->typeSenpi }}
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">{{
                            $item->quantitySenpi }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">{{
                            $item->quantityMagazine }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">{{
                            $item->quantityBullet }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">{{
                            $item->licenseNumber }}</td>
                        <td class="px-3 py-3 text-center">
                            <div class="flex justify-center space-x-2">
                                @if(auth()->user()->role->name === 'superadmin')
                                <button @click="openEditModal(JSON.parse('{{ json_encode([
                                        'id' => $item->id,
                                        'date' => $item->date,
                                        'name' => $item->name ?? '',
                                        'agency' => $item->agency ?? '',
                                        'flightNumber' => $item->flightNumber ?? '',
                                        'destination' => $item->destination ?? '',
                                        'typeSenpi' => $item->typeSenpi ?? '',
                                        'quantitySenpi' => $item->quantitySenpi ?? 0,
                                        'quantityMagazine' => $item->quantityMagazine ?? 0,
                                        'quantityBullet' => $item->quantityBullet ?? 0,
                                        'licenseNumber' => $item->licenseNumber ?? ''
                                    ], JSON_HEX_APOS | JSON_HEX_QUOT) }}'))"
                                    class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-full transition-colors"
                                    title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('checklist.senpi.destroy', $item->id) }}"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-full transition-colors"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                        title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-6 py-8 text-center text-sm text-gray-500">
                            <svg class="mx-auto w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-500 mb-2">Belum Ada Data</h3>
                            <p class="text-gray-400">Data checklist akan muncul di sini setelah dibuat</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .max-w-6xl {
            max-width: 100%;
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

        /* Button sizing for mobile */
        .px-3.py-2 {
            padding: 0.5rem 0.75rem;
        }

        .px-4.py-2 {
            padding: 0.5rem 1rem;
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
</style>
@endsection
