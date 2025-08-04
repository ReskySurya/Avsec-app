@extends('layouts.app')
@section('title', 'Detail Logbook Pos Jaga')
@php
// Data statis untuk uraian kegiatan
$uraianKegiatan = [
    ['waktu' => '08:00', 'uraian' => 'Pemeriksaan area pos jaga', 'keterangan' => 'Semua aman'],
    ['waktu' => '10:00', 'uraian' => 'Patroli rutin', 'keterangan' => 'Tidak ada kejadian'],
    ['waktu' => '12:00', 'uraian' => 'Istirahat makan siang', 'keterangan' => ''],
    ['waktu' => '14:00', 'uraian' => 'Pemeriksaan dokumen pengunjung', 'keterangan' => ''],
    ['waktu' => '16:00', 'uraian' => 'Penutupan pos jaga', 'keterangan' => ''],
];
@endphp
@section('content')
<!-- Alert Messages with Enhanced Design -->
@if(session('success'))
<div
    class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-blue-600 animate-pulse">
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

<div class="mx-auto p-0 sm:p-6 min-h-screen pt-5 sm:pt-20">
    <div class="mb-4">
        <a href="{{ route('logbook.index',['location'=> $location])}}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="overflow-hidden mb-8">

        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 md:hidden">
            @if(isset($logbook))
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 cursor-pointer">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">{{ $logbook->date ?? 'N/A' }}</span>
                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-teal-100 rounded-full">{{ $logbook->shift ?? 'N/A' }}</span>
                    </div>
                    <div class="mt-4 space-y-2 text-sm text-gr</div>ay-600">
                        <p><strong class="font-medium text-gray-800">Area:</strong> {{ $logbook->locationArea->name ?? 'N/A' }}</p>
                        <p><strong class="font-medium text-gray-800">Grup:</strong> {{ $logbook->grup ?? 'N/A' }}</p>
                        <p><strong class="font-medium text-gray-800">Dinas/Shift:</strong> {{ $logbook->shift ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="col-span-1 sm:col-span-2 text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 text-lg">Belum ada entry logbook</p>
                <p class="text-gray-400">Tambahkan entry pertama Anda</p>
            </div>
            @endif
        </div>

        <!-- Desktop Table View -->
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hidden md:block">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Informasi Logbook</h3>
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="bg-blue-50 rounded-lg ">
                    <ul class="text-blue-600">
                        <li><strong>Tanggal:</strong> {{ $logbook->date ?? 'N/A' }}</li>
                        <li><strong>Area:</strong> {{ $logbook->locationArea->name ?? 'N/A' }}</li>
                    </ul>
                </div>
                <div class="bg-blue-50 rounded-lg ">
                    <ul class="text-blue-600 mt-2">
                        <li><strong>Grup:</strong> {{ $logbook->grup ?? 'N/A' }}</li>
                        <li><strong>Dinas/Shift:</strong> {{ $logbook->shift ?? 'N/A' }}</li>
                    </ul>
                </div>
            </div>
            @if(isset($logbooks) && method_exists($logbooks, 'links'))
            <div class="px-4 py-3 bg-white border-t border-gray-200">
                {{ $logbooks->links() }}
            </div>
            @endif
        </div>
    </div>


    <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold mb-1">{{ 'Uraian Kegiatan'}}</h3>
                </div>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 gap-2 md:hidden p-4">
            @forelse($uraianKegiatan as $i => $item)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                            {{ $item['waktu'] }}
                        </span>
                        <span class="text-xs text-gray-400">#{{ $i+1 }}</span>
                    </div>
                    <div class="mb-2">
                        <p class="font-semibold text-gray-800 mb-1">{{ $item['uraian'] }}</p>
                        <p class="text-gray-600 text-sm">{{ $item['keterangan'] }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-1 text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 text-lg">Belum ada uraian kegiatan</p>
                <p class="text-gray-400">Tambahkan uraian kegiatan pertama Anda</p>
            </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hidden md:block">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Uraian Kegiatan</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($uraianKegiatan as $i => $item)
                    <tr class="hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
                        <td class="px-5 py-4 text-blue-600 font-bold">{{ $i+1 }}</td>
                        <td class="px-5 py-4">{{ $item['waktu'] }}</td>
                        <td class="px-5 py-4">{{ $item['uraian'] }}</td>
                        <td class="px-5 py-4">{{ $item['keterangan'] }}</td>
                        <td class="px-5 py-4 flex justify-center space-x-2" @click.stop>
                            <!-- Edit Button -->
                            <button type="button"
                                class="p-2 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors duration-200"
                                @click="openEditDetailFn({{ $i }}, '{{ $item['waktu'] }}', '{{ addslashes($item['uraian']) }}', '{{ addslashes($item['keterangan']) }}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <!-- Delete Button -->
                            <form action="#" method="POST" onsubmit="return confirm('Yakin ingin menghapus entry ini?')"></form>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="text-gray-500 text-lg">Belum ada uraian kegiatan</p>
                            <p class="text-gray-400">Tambahkan uraian kegiatan pertama Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Edit Uraian Kegiatan -->
        <div
            x-show="openEditDetail"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            style="display: none;">
            <div @click.away="openEditDetail = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
                <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                    <h2 class="text-2xl font-bold">Edit Uraian Kegiatan</h2>
                    <p class="text-blue-100">Ubah informasi uraian kegiatan</p>
                </div>
                <form action="#" method="POST" class="p-6">
                    @csrf
                    @method('PATCH')
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu</label>
                        <input type="time" name="waktu" required x-model="editDetailData.waktu"
                            class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Uraian Kegiatan</label>
                        <input type="text" name="uraian" required x-model="editDetailData.uraian"
                            class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                            placeholder="Masukkan uraian kegiatan">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" required x-model="editDetailData.keterangan"
                            class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                            placeholder="Masukkan keterangan"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="openEditDetail = false"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
