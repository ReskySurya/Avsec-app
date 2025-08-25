@extends('layouts.app')

@section('title', 'Check List Senjata Api')

@section('content')
<div class="mx-auto p-4 sm:p-6 min-h-screen pt-5 sm:pt-20" x-data="{ showModal: false }">

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

    {{-- Modal Tambah Data --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @keydown.escape.window="showModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto" @click.away="showModal = false">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-6 text-white sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold">Tambah Data Checklist Senjata Api</h1>
                    <button @click="showModal = false" class="text-white hover:text-gray-200 text-3xl">&times;</button>
                </div>
            </div>

            <form method="POST" action="{{ route('checklist.senpi.store') }}" class="p-6 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Form fields --}}
                    <div class="space-y-2">
                        <label for="date" class="block text-sm font-semibold text-gray-700">Tanggal</label>
                        <input type="date" name="date" id="date" value="{{ old('date', now()->format('Y-m-d')) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-semibold text-gray-700">Nama</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Nama Pemilik" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="agency" class="block text-sm font-semibold text-gray-700">Instansi</label>
                        <input type="text" name="agency" id="agency" value="{{ old('agency') }}" placeholder="Instansi" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="flightNumber" class="block text-sm font-semibold text-gray-700">No. Penerbangan</label>
                        <input type="text" name="flightNumber" id="flightNumber" value="{{ old('flightNumber') }}" placeholder="GA-XXX" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="destination" class="block text-sm font-semibold text-gray-700">Tujuan</label>
                        <input type="text" name="destination" id="destination" value="{{ old('destination') }}" placeholder="Tujuan Penerbangan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="typeSenpi" class="block text-sm font-semibold text-gray-700">Jenis Senpi</label>
                        <input type="text" name="typeSenpi" id="typeSenpi" value="{{ old('typeSenpi') }}" placeholder="Jenis Senjata" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="quantitySenpi" class="block text-sm font-semibold text-gray-700">Jml Senpi</label>
                        <input type="number" name="quantitySenpi" id="quantitySenpi" value="{{ old('quantitySenpi') }}" placeholder="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="quantityMagazine" class="block text-sm font-semibold text-gray-700">Jml Magazen</label>
                        <input type="number" name="quantityMagazine" id="quantityMagazine" value="{{ old('quantityMagazine') }}" placeholder="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="quantityBullet" class="block text-sm font-semibold text-gray-700">Jml Peluru</label>
                        <input type="number" name="quantityBullet" id="quantityBullet" value="{{ old('quantityBullet') }}" placeholder="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2 md:col-span-2 lg:col-span-3">
                        <label for="licenseNumber" class="block text-sm font-semibold text-gray-700">No. Surat Izin</label>
                        <input type="text" name="licenseNumber" id="licenseNumber" value="{{ old('licenseNumber') }}" placeholder="Nomor Surat Izin" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end pt-4 sticky bottom-0 bg-white py-4 px-6">
                    <button type="button" @click="showModal = false" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors mr-4">
                        Batal
                    </button>
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200 p-5">
        <div class="px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Data Checklist Senjata Api</h2>
            <button @click="showModal = true" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                Tambah Data
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="w-full border-collapse border-2 border-gray-400">
                    <tr class="bg-blue-600 text-white text-sm">
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">No</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">Tanggal</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">Nama</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">Instansi</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">No. Penerbangan</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">Tujuan</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">Jenis Senpi</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" colspan="3">Jumlah</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">No. Surat Izin Kepemilikan</th>
                        <th class="border-2 border-gray-400 py-3 text-center uppercase tracking-wider" rowspan="2">Aksi</th>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->formatted_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->agency }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->flightNumber }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->destination }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->typeSenpi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantitySenpi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantityMagazine }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantityBullet }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->licenseNumber }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-4 text-center text-sm text-gray-500">
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
