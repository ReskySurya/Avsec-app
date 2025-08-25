@extends('layouts.app')

@section('title', 'Check List Senjata Api')

@section('content')
<div class="mx-auto p-4 sm:p-6 min-h-screen pt-5 sm:pt-20"
    x-data="{ 
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
        console.log('Opening add modal');
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
        console.log('Opening edit modal with data:', data);
        this.editMode = true;
        this.editData = { ...data }; // Spread operator untuk copy data
        this.showModal = true;
        console.log('Modal should be visible:', this.showModal);
    },
    closeModal() {
        console.log('Closing modal');
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
    <div class="bg-green-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg">
        {{ session('success') }}
    </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
    <div class="bg-red-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg">
        {{ session('error') }}
    </div>
    @endif

    {{-- Menampilkan Error Validasi --}}
    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6 shadow-lg" role="alert">
        <strong class="font-bold">Oops! Terjadi kesalahan.</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Modal Tambah/Edit Data --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @keydown.escape.window="closeModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto" @click.away="closeModal()">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-6 text-white sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold" x-text="editMode ? 'Edit Data Checklist Senjata Api' : 'Tambah Data Checklist Senjata Api'"></h1>
                    <button @click="closeModal()" class="text-white hover:text-gray-200 text-3xl">&times;</button>
                </div>
            </div>

            {{-- FIXED: Form action dan method yang benar --}}
            <form method="POST" :action="editMode ? '{{ url('/checklist-senpi/update') }}/' + editData.id : '{{ route('checklist.senpi.store') }}'" class="p-6 space-y-6">
                @csrf
                {{-- FIXED: Method yang benar untuk update --}}
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="POST">
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    {{-- Form fields --}}
                    <div class="space-y-2">
                        <label for="date" class="block text-sm font-semibold text-gray-700">Tanggal</label>
                        <input type="date"
                            name="date"
                            id="date"
                            x-bind:value="editMode ? 
                            (editData.date ? editData.date.split('T')[0] : '') : 
                            '{{ old('date', now()->format('Y-m-d')) }}'"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-semibold text-gray-700">Nama</label>
                        <input type="text" name="name" id="name"
                            :value="editMode ? editData.name : '{{ old('name') }}'"
                            placeholder="Nama Pemilik" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="agency" class="block text-sm font-semibold text-gray-700">Instansi</label>
                        <input type="text" name="agency" id="agency"
                            :value="editMode ? editData.agency : '{{ old('agency') }}'"
                            placeholder="Instansi" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="flightNumber" class="block text-sm font-semibold text-gray-700">No. Penerbangan</label>
                        <input type="text" name="flightNumber" id="flightNumber"
                            :value="editMode ? editData.flightNumber : '{{ old('flightNumber') }}'"
                            placeholder="GA-XXX" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="destination" class="block text-sm font-semibold text-gray-700">Tujuan</label>
                        <input type="text" name="destination" id="destination"
                            :value="editMode ? editData.destination : '{{ old('destination') }}'"
                            placeholder="Tujuan Penerbangan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="typeSenpi" class="block text-sm font-semibold text-gray-700">Jenis Senpi</label>
                        <input type="text" name="typeSenpi" id="typeSenpi"
                            :value="editMode ? editData.typeSenpi : '{{ old('typeSenpi') }}'"
                            placeholder="Jenis Senjata" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="quantitySenpi" class="block text-sm font-semibold text-gray-700">Jml Senpi</label>
                        <input type="number" name="quantitySenpi" id="quantitySenpi"
                            :value="editMode ? editData.quantitySenpi : '{{ old('quantitySenpi') }}'"
                            placeholder="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="quantityMagazine" class="block text-sm font-semibold text-gray-700">Jml Magazen</label>
                        <input type="number" name="quantityMagazine" id="quantityMagazine"
                            :value="editMode ? editData.quantityMagazine : '{{ old('quantityMagazine') }}'"
                            placeholder="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="quantityBullet" class="block text-sm font-semibold text-gray-700">Jml Peluru</label>
                        <input type="number" name="quantityBullet" id="quantityBullet"
                            :value="editMode ? editData.quantityBullet : '{{ old('quantityBullet') }}'"
                            placeholder="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2 md:col-span-2 lg:col-span-3">
                        <label for="licenseNumber" class="block text-sm font-semibold text-gray-700">No. Surat Izin</label>
                        <input type="text" name="licenseNumber" id="licenseNumber"
                            :value="editMode ? editData.licenseNumber : '{{ old('licenseNumber') }}'"
                            placeholder="Nomor Surat Izin" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end sticky bottom-0 bg-white px-6">
                    <button type="button" @click="closeModal()" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors mr-4">
                        Batal
                    </button>
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                        <span x-text="editMode ? 'Perbarui Data' : 'Simpan Data'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200 p-5">
        <div class="px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Data Checklist Senjata Api</h2>
            <button @click="openAddModal()" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                Tambah Data
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="w-full ">
                    <tr class="bg-blue-600 text-white text-sm">
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">No</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">Tanggal</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">Nama</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">Instansi</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">No. Penerbangan</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">Tujuan</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">Jenis Senpi</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" colspan="3">Jumlah</th>
                        @if(auth()->user()->role->name === 'superadmin')
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">No. Surat Izin Kepemilikan</th>
                        <th class=" border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">Aksi</th>
                        @else
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2" colspan="2">No. Surat Izin Kepemilikan</th>
                        @endif
                    </tr>
                    <tr class="bg-blue-500 text-white text-sm">
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider">Senpi</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider">Magazine</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider">Peluru</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($senpi as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->formatted_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->agency }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->flightNumber }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->destination }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->typeSenpi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->quantitySenpi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->quantityMagazine }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->quantityBullet }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->licenseNumber }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                @if(auth()->user()->role->name === 'superadmin')
                                <button
                                    @click="openEditModal(JSON.parse('{{ json_encode([
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('checklist.senpi.destroy', $item->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-full transition-colors"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                        title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada data yang tersedia.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection