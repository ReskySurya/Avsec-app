@extends('layouts.app')

@section('title', 'Form Pencatatan PI')
@section('content')
<div x-data="{
        openPencatatanPI: false,
    }" class="mx-auto p-0 sm:p-6 min-h-screen pt-5 sm:pt-20">

    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-blue-600">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition class="bg-gradient-to-r from-red-400 to-red-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-red-600">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-6 text-white">
            <div class="flex flex-col sm:flex-row justify-between items-start">
                <div>
                    <h3 class="text-2xl font-bold mb-1">Form Pencatatan PI</h3>
                    <p class="text-blue-100">Catatan aktivitas Prohibited Items harian</p>
                </div>
                <button @click="openPencatatanPI = true" class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 mt-3 sm:mt-0 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto ">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Data
                </button>
            </div>
        </div>

        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Tanggal</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" colspan="2">Jam</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Nama Petugas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Instansi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Jenis PI</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" colspan="2">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Ket</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" rowspan="2">Aksi</th>
                    </tr>
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Masuk</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Keluar</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Masuk</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Keluar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pencatatanPI ?? [] as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">{{ $item->in_time }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">{{ $item->out_time }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $item->name_person }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $item->agency }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $item->jenis_PI }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">{{ $item->in_quantity }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">{{ $item->out_quantity }}</td>
                        <td class="px-4 py-3">{{ $item->summary }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <form action="{{ route('checklist.pencatatanpi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-100 text-red-600 hover:bg-red-200 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-12">
                            <p class="text-gray-500 text-lg">Belum ada data.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah Data --}}
    <div x-show="openPencatatanPI" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openPencatatanPI = false" class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Tambah Data Pencatatan PI</h2>
            </div>
            <form action="{{ route('checklist.pencatatanpi.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                        <input type="date" id="date" name="date" required class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                    </div>
                    <div>
                        <label for="grup" class="block text-sm font-semibold text-gray-700 mb-2">Grup</label>
                        <select id="grup" name="grup" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                            <option value="">Pilih Grup</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                        </select>
                    </div>
                    <div>
                        <label for="name_person" class="block text-sm font-semibold text-gray-700 mb-2">Nama Petugas</label>
                        <input type="text" id="name_person" name="name_person" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200" placeholder="nama penanggung jawab">
                    </div>
                    <div>
                        <label for="agency" class="block text-sm font-semibold text-gray-700 mb-2">Instansi</label>
                        <input type="text" id="agency" name="agency" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200" placeholder="nama instansi">
                    </div>
                    <div>
                        <label for="in_time" class="block text-sm font-semibold text-gray-700 mb-2">Jam Masuk</label>
                        <input type="time" id="in_time" name="in_time" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                    </div>
                    <div>
                        <label for="out_time" class="block text-sm font-semibold text-gray-700 mb-2">Jam Keluar</label>
                        <input type="time" id="out_time" name="out_time" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                    </div>
                    <div>
                        <label for="jenis_PI" class="block text-sm font-semibold text-gray-700 mb-2">Jenis PI</label>
                        <input type="text" id="jenis_PI" name="jenis_PI" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                    </div>
                    <div>
                        <label for="in_quantity" class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Masuk</label>
                        <input type="text" id="in_quantity" name="in_quantity" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 " placeholder="0">
                    </div>
                    <div>
                        <label for="out_quantity" class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Keluar</label>
                        <input type="text" id="out_quantity" name="out_quantity" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200" placeholder="0">
                    </div>
                    <div class="md:col-span-2">
                        <label for="summary" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                        <textarea id="summary" name="summary" rows="3" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200" placeholder="Masukkan Keterangan"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="openPencatatanPI = false" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">Batal</button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">Simpan</button>
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
@endsection
