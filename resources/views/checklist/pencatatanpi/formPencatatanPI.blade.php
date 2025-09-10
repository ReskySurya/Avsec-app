@extends('layouts.app')

@section('title', 'Form Pencatatan PI')
@section('content')
<div x-data="{
        openPencatatanPI: false,
    }" class="mx-auto p-2 sm:p-4 min-h-screen pt-5 sm:pt-20">

    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg border-l-4 border-blue-600 text-sm sm:text-base">
        <div class="flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition class="bg-gradient-to-r from-red-400 to-red-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg border-l-4 border-red-600 text-sm sm:text-base">
        <div class="flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    {{-- Mobile: Card Layout --}}
    <div class="block lg:hidden space-y-3 mb-6">
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-3">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Form Pencatatan PI</h3>
                    <p class="text-xs text-gray-500">Catatan aktivitas Prohibited Items</p>
                </div>
                <button @click="openPencatatanPI = true" class="bg-blue-500 text-white px-3 py-2 rounded-lg font-semibold text-sm hover:bg-blue-600 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah
                </button>
            </div>
        </div>

        @forelse($pencatatanPI ?? [] as $index => $item)
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-bold mr-2">
                        {{ $index + 1 }}
                    </span>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $item->name_person ?? '-' }}</h3>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') ?? '-'}}</p>
                    </div>
                </div>
                @if ($item->status == 'draft')
                <div class="flex space-x-1">
                    <a href="{{ route('checklist.pencatatanpi.edit', $item->id) }}"
                        class="p-1.5 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors"
                        title="Edit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    <form action="{{ route('checklist.pencatatanpi.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors" title="Hapus">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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
                        <p class="text-gray-500">Jenis PI</p>
                        <p class="font-medium">{{ $item->jenis_PI ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="text-gray-500">Jam Masuk</p>
                        <p class="font-medium">{{ $item->in_time ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Jam Keluar</p>
                        <p class="font-medium">{{ $item->out_time ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
                    <div class="text-center">
                        <p class="text-gray-500 text-xs">Jml Masuk</p>
                        <p class="font-bold text-blue-600">{{ $item->in_quantity ?? '-' }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-500 text-xs">Jml Keluar</p>
                        <p class="font-bold text-blue-600">{{ $item->out_quantity ?? '-' }}</p>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100">
                    <p class="text-gray-500 text-xs">Keterangan</p>
                    <p class="font-medium break-words">{{ $item->summary ?? '-' }}</p>
                </div>

                <div class="pt-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                        @if($item->status == 'draft') bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800 @endif">
                        {{ ucfirst($item->status ?? '-') }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-8 text-center">
            <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-base font-semibold text-gray-500 mb-1">Belum Ada Data</h3>
            <p class="text-xs text-gray-400">Data pencatatan akan muncul di sini setelah dibuat</p>
        </div>
        @endforelse
    </div>

    {{-- Desktop: Table Layout --}}
    <div class="hidden lg:block bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-4 sm:px-6 py-4 sm:py-6 text-white">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-1">Form Pencatatan PI</h3>
                    <p class="text-blue-100 text-sm sm:text-base">Catatan aktivitas Prohibited Items harian</p>
                </div>
                <button @click="openPencatatanPI = true" class="bg-white text-blue-600 hover:bg-blue-50 px-4 sm:px-6 py-2 sm:py-3 mt-3 sm:mt-0 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto text-sm sm:text-base">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Data
                </button>
            </div>
        </div>

        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-2">
                    <tr class="border-2">
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">No</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Tanggal</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" colspan="2">Jam</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Nama Pemilik</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Instansi</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Jenis PI</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" colspan="2">Jumlah</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Ket</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Status</th>
                        @forelse($pencatatanPI ?? [] as $index => $item)
                        @if ($item->status == 'draft')
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Aksi</th>
                        @endif
                        @empty
                        @endforelse
                    </tr>
                    <tr class="border-2">
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Masuk</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Keluar</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Masuk</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Keluar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pencatatanPI ?? [] as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-sm">{{ $index + 1 }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') ?? '-'}}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-center text-sm">{{ $item->in_time ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-center text-sm">{{ $item->out_time ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-sm">{{ $item->name_person ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-sm">{{ $item->agency ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-sm">{{ $item->jenis_PI ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-center text-sm">{{ $item->in_quantity ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-center text-sm">{{ $item->out_quantity ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 text-sm">{{ $item->summary ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 text-sm">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                @if($item->status == 'draft') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($item->status ?? '-') }}
                            </span>
                        </td>

                        @if ($item->status == 'draft')
                        <td class="px-3 sm:px-4 py-2 sm:py-3 flex justify-center space-x-1" @click.stop>
                            <a href="{{ route('checklist.pencatatanpi.edit', $item->id) }}"
                                class="p-1.5 sm:p-2 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors duration-200"
                                title="Edit Pencatatan PI">
                                <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>

                            <form action="{{ route('checklist.pencatatanpi.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 sm:p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200" title="Hapus Pencatatan PI">
                                    <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-8 sm:py-12">
                            <p class="text-gray-500 text-base sm:text-lg">Belum ada data.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah Data --}}
    <div x-show="openPencatatanPI" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4" style="display: none;">
        <div @click.away="openPencatatanPI = false" class="bg-white w-full max-w-xs sm:max-w-2xl lg:max-w-3xl rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-4 sm:p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg sm:text-2xl font-bold">Tambah Data Pencatatan PI</h2>
                    <button @click="openPencatatanPI = false" class="text-white hover:text-gray-200 text-2xl">&times;</button>
                </div>
            </div>
            <form action="{{ route('checklist.pencatatanpi.store') }}" method="POST" class="p-3 sm:p-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    <div>
                        <label for="date" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Tanggal</label>
                        <input type="date" id="date" name="date" required class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                    </div>
                    <div>
                        <label for="grup" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Grup</label>
                        <select id="grup" name="grup" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                            <option value="">Pilih Grup</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                        </select>
                    </div>
                    <div>
                        <label for="name_person" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Nama Pemilik</label>
                        <input required type="text" id="name_person" name="name_person" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base" placeholder="nama pemilik">
                    </div>
                    <div>
                        <label for="agency" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Instansi</label>
                        <input required type="text" id="agency" name="agency" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base" placeholder="nama instansi">
                    </div>
                    <div>
                        <label for="in_time" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Jam Masuk</label>
                        <input required type="time" id="in_time" name="in_time" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                    </div>
                    <div>
                        <label for="out_time" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Jam Keluar</label>
                        <input type="time" id="out_time" name="out_time" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                    </div>
                    <div>
                        <label for="jenis_PI" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Jenis PI</label>
                        <input required type="text" id="jenis_PI" name="jenis_PI" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base">
                    </div>
                    <div>
                        <label for="in_quantity" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Jumlah Masuk</label>
                        <input required type="text" id="in_quantity" name="in_quantity" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base" placeholder="0">
                    </div>
                    <div>
                        <label for="out_quantity" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Jumlah Keluar</label>
                        <input type="text" id="out_quantity" name="out_quantity" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base" placeholder="0">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label for="summary" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Keterangan</label>
                        <textarea id="summary" name="summary" rows="2" class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 text-sm sm:text-base" placeholder="Masukkan Keterangan"></textarea>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-4 sm:mt-6">
                    <button type="button" @click="openPencatatanPI = false" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium text-sm sm:text-base">Batal</button>
                    <button type="submit" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg text-sm sm:text-base">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('date');
        if (dateInput) {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            dateInput.value = `${year}-${month}-${day}`;
        }
    });
</script>

<style>
    @media (max-width: 768px) {
        .max-w-6xl {
            max-width: 100%;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
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

        .text-2xl {
            font-size: 1.5rem;
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

    /* Custom scrollbar */
    .scrollbar-thin::-webkit-scrollbar {
        height: 6px;
    }

    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endsection
