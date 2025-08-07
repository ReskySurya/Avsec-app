@extends('layouts.app')
@section('title', 'Detail Logbook Pos Jaga')

@section('content')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@endpush
<!-- Alert Success -->
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
    class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd" />
        </svg>
        {{ session('success') }}
        <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</div>
@endif

<!-- Alert Error -->
@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)"
    class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd" />
        </svg>
        {{ session('error') }}
        <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</div>
@endif

<div class="mx-auto p-0 sm:p-6 min-h-screen pt-5 sm:pt-20" x-data="{
        openEditDetail: false,
        openAddDetail: false,
        openFinishDialog: false,
        editDetailData: {
            id: null,
            start_time: '',
            end_time: '',
            summary: '',
            description: ''
        },
         openEditDetailFn(id, startTime, endTime, summary, description) {
            this.editDetailData.id = id;
            // Handle berbagai format waktu yang mungkin
            if (startTime) {
                // Jika format datetime (2024-01-01 08:30:00) atau time (08:30:00)
                if (startTime.includes(' ')) {
                    this.editDetailData.start_time = startTime.split(' ')[1].substring(0, 5);
                } else if (startTime.length > 5) {
                    this.editDetailData.start_time = startTime.substring(0, 5);
                } else {
                    this.editDetailData.start_time = startTime;
                }
            } else {
                this.editDetailData.start_time = '';
            }
            if (endTime) {
                // Jika format datetime (2024-01-01 17:30:00) atau time (17:30:00)
                if (endTime.includes(' ')) {
                    this.editDetailData.end_time = endTime.split(' ')[1].substring(0, 5);
                } else if (endTime.length > 5) {
                    this.editDetailData.end_time = endTime.substring(0, 5);
                } else {
                    this.editDetailData.end_time = endTime;
                }
            } else {
                this.editDetailData.end_time = '';
            }

            this.editDetailData.summary = summary;
            this.editDetailData.description = description;
            this.openEditDetail = true;
        }
     }">
    <div class="mb-4">
        <a href="{{ route('logbook.index',['location'=> $location])}}"
            class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition">
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
                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">{{
                            $logbook->date ?? 'N/A' }}</span>
                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-teal-100 rounded-full">{{
                            $logbook->shift ?? 'N/A' }}</span>
                    </div>
                    <div class="mt-4 space-y-2 text-sm text-gr</div>ay-600">
                        <p><strong class="font-medium text-gray-800">Area:</strong> {{ $logbook->locationArea->name ??
                            'N/A' }}</p>
                        <p><strong class="font-medium text-gray-800">Grup:</strong> {{ $logbook->grup ?? 'N/A' }}</p>
                        <p><strong class="font-medium text-gray-800">Dinas/Shift:</strong> {{ $logbook->shift ?? 'N/A'
                            }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="col-span-1 sm:col-span-2 text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <p class="text-gray-500 text-lg">Belum ada data logbook</p>
                <p class="text-gray-400">Tambahkan entry pertama Anda</p>
            </div>
            @endif
        </div>

        <!-- Desktop Table View -->
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hidden md:block">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Informasi Logbook</h3>
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class=" rounded-lg ">
                    <ul class="text-blue-600">
                        <li><strong>ID:</strong> {{ $logbook->logbookID ?? 'N/A' }}</li>
                        <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($logbook->tanggal)->translatedFormat('l,
                            d F Y') }}</li>
                        <li><strong>Area:</strong> {{ $logbook->locationArea->name ?? 'N/A' }}</li>
                    </ul>
                </div>
                <div class=" rounded-lg ">
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
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-4 py-4 sm:px-6 sm:py-6 text-white">
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-1">{{ 'Uraian Kegiatan'}}</h3>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:gap-2 w-full sm:w-auto">
                    <button @click="openAddDetail = true"
                        class="w-full sm:w-auto px-4 py-2 bg-green-500 hover:bg-blue-600 text-white rounded-xl text-sm font-semibold shadow transition">
                        + Tambah Uraian Kegiatan
                    </button>
                    <button @click="openFinishDialog = true"
                        class="w-full sm:w-auto px-4 py-2 bg-gray-200 hover:bg-gray-300 text-blue-800 rounded-xl text-sm font-semibold shadow transition text-center">
                        Selesai
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Selesai -->
        <div x-show="openFinishDialog"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            style="display: none;">

            <div @click.away="openFinishDialog = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
                <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                    <h2 class="text-2xl font-bold">Konfirmasi Selesai</h2>
                    <p class="text-blue-100">Konfirmasi penyelesaian logbook shift hari ini</p>
                </div>

                <form action="#" method="POST" class="p-6" @submit.prevent="handleFinishSubmit">
                    @csrf
                    <div class="mb-6">
                        <p class="text-gray-700 text-lg mb-4">Apakah Anda yakin sudah menyelesaikan logbook shift hari
                            ini?</p>

                        <div class="border-2 border-gray-200 rounded-xl p-4 mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan Digital</label>

                            <!-- Container untuk signature pad -->
                            <div class="relative">
                                <div id="signature-pad"
                                    class="w-full h-48 border border-gray-300 rounded-lg bg-white relative overflow-hidden">
                                    <!-- Placeholder text - akan dihapus oleh JavaScript -->
                                    <div id="signature-placeholder"
                                        class="absolute inset-0 flex flex-col items-center justify-center text-gray-500 pointer-events-none">
                                        <p class="text-sm">Tanda tangan di sini</p>
                                        <p class="text-xs">(Gunakan jari atau mouse untuk menandatangani)</p>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="signature" id="signature-data">

                            <div class="flex justify-between items-center mt-2">
                                <span id="signature-status" class="text-xs text-gray-500">Belum ada tanda tangan</span>
                                <button type="button"
                                    class="text-sm text-blue-600 hover:text-blue-800 transition-colors"
                                    onclick="clearSignature()">
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="openFinishDialog = false"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">
                            Konfirmasi & Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 gap-2 md:hidden p-4">
            @forelse($uraianKegiatan ?? [] as $index => $items)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                            {{ \Carbon\Carbon::parse($items->start_time)->format('H:i') }}
                            {{ $items->end_time ? ' - ' . \Carbon\Carbon::parse($items->end_time)->format('H:i') : '' }}
                        </span>
                        <span class="text-xs text-gray-400">#{{ $index+1 }}</span>
                    </div>
                    <div class="mb-2">
                        <p class="font-semibold text-gray-800 mb-1">{{ $items->summary }}</p>
                        <p class="text-gray-600 text-sm">{{ $items->description }}</p>
                    </div>
                    <div class="flex justify-end space-x-2 mt-3">
                        <!-- Edit button for mobile -->
                        <button type="button"
                            class="p-2 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors duration-200"
                            title="Edit Uraian Kegiatan"
                            @click="openEditDetailFn({{ $items->id }}, '{{ \Carbon\Carbon::parse($items->start_time)->format('H:i') }}', '{{ $items->end_time ? \Carbon\Carbon::parse($items->end_time)->format('H:i') : '' }}', '{{ addslashes($items->summary ?? '') }}', '{{ addslashes($items->description ?? '') }}')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                        </button>

                        <!-- Delete button for mobile -->
                        <form action="{{ route('logbook.detail.delete', $items->id) }}" method="POST"
                            class="inline-block"
                            onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus uraian kegiatan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200"
                                title="Hapus Uraian Kegiatan">
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
            @empty
            <div class="col-span-1 text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
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
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Waktu</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Uraian Kegiatan</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Keterangan</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($uraianKegiatan ?? [] as $index => $items)
                    <tr class="hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
                        <td class="px-5 py-4 text-blue-600 font-bold">{{ $index + 1 }}</td>
                        <td class="px-5 py-4">
                            {{ \Carbon\Carbon::parse($items->start_time)->format('H:i') }}
                            {{ $items->end_time ? ' - ' . \Carbon\Carbon::parse($items->end_time)->format('H:i') : '' }}
                        </td>
                        <td class="px-5 py-4">{{ $items->summary }}</td>
                        <td class="px-5 py-4">{{ $items->description }}</td>
                        <td class="px-5 py-4 flex justify-center space-x-2" @click.stop>
                            <!-- Edit button -->
                            <button type="button"
                                class="p-2 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors duration-200"
                                title="Edit Uraian Kegiatan"
                                @click="openEditDetailFn({{ $items->id }}, '{{ \Carbon\Carbon::parse($items->start_time)->format('H:i') }}', '{{ $items->end_time ? \Carbon\Carbon::parse($items->end_time)->format('H:i') : '' }}', '{{ addslashes($items->summary ?? '') }}', '{{ addslashes($items->description ?? '') }}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>

                            <!-- Delete button with better confirmation -->
                            <form action="{{ route('logbook.detail.delete', $items->id) }}" method="POST"
                                class="inline-block"
                                onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus uraian kegiatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200"
                                    title="Hapus Uraian Kegiatan">
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
                        <td colspan="5" class="text-center py-12">
                            <p class="text-gray-500 text-lg">Belum ada uraian kegiatan</p>
                            <p class="text-gray-400">Tambahkan uraian kegiatan pertama Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <!-- Modal Tambah Uraian Kegiatan -->
        <div x-show="openAddDetail" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            style="display: none;">
            <div @click.away="openAddDetail = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
                <div class="bg-gradient-to-r from-blue-500 to-emerald-600 text-white p-6 rounded-t-2xl">
                    <h2 class="text-2xl font-bold">Tambah Uraian Kegiatan</h2>
                    <p class="text-blue-100">Masukkan uraian kegiatan baru</p>
                </div>

                <form action="{{ route('logbook.detail.store') }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="logbookID" value="{{ $logbook->logbookID ?? '' }}">

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu</label>
                        <div class="flex space-x-4">
                            <input type="time" name="start_time" value="{{ old('start_time') }}"
                                class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('start_time') border-red-500 @enderror"
                                required>

                            <input type="time" name="end_time" value="{{ old('end_time') }}"
                                class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('start_time') border-red-500 @enderror"
                                required>
                        </div>
                        @error('start_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @error('end_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Uraian Kegiatan</label>
                        <input type="text" name="summary" value="{{ old('summary') }}"
                            class="w-full border-2 px-4 py-3 rounded-xl focus:border-green-500 focus:outline-none transition-colors duration-200 @error('summary') border-red-500 @enderror"
                            placeholder="Masukkan uraian kegiatan" required>
                        @error('summary')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                        <textarea name="description" rows="3"
                            class="w-full border-2 px-4 py-3 rounded-xl focus:border-green-500 focus:outline-none transition-colors duration-200 @error('description') border-red-500 @enderror"
                            placeholder="Masukkan keterangan" required>{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="openAddDetail = false"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 font-medium shadow-lg">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <!-- Modal Edit Uraian Kegiatan -->
        <div x-show="openEditDetail" x-transition:enter="transition ease-out duration-300"
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
                <form x-bind:action="`/logbook/detail/update/${editDetailData.id}`" method="POST" class="p-6">
                    @csrf
                    @method('POST')
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu</label>
                        <div class="flex space-x-4">
                            <div class="w-full">
                                <label class="block text-xs text-gray-500 mb-1">Waktu Mulai</label>
                                <input type="time" name="start_time" required x-model="editDetailData.start_time"
                                    class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                            </div>
                            <div class="w-full">
                                <label class="block text-xs text-gray-500 mb-1">Waktu Selesai</label>
                                <input type="time" name="end_time" x-model="editDetailData.end_time"
                                    class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                            </div>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Uraian Kegiatan</label>
                        <input type="text" name="summary" required x-model="editDetailData.summary"
                            class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                            placeholder="Masukkan uraian kegiatan">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                        <textarea name="description" required x-model="editDetailData.description"
                            class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                            placeholder="Masukkan keterangan" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="openEditDetail = false"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">
                            Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let signaturePad;
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;

    function initSignaturePad() {
        const signatureDiv = document.getElementById('signature-pad');
        const placeholder = document.getElementById('signature-placeholder');
        const statusEl = document.getElementById('signature-status');

        if (!signatureDiv) {
            console.error('Element signature-pad tidak ditemukan');
            return;
        }

        // Hapus placeholder
        if (placeholder) {
            placeholder.remove();
        }

        // Buat canvas
        const canvas = document.createElement('canvas');
        canvas.style.position = 'absolute';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        canvas.style.touchAction = 'none';
        canvas.style.cursor = 'crosshair';

        signatureDiv.appendChild(canvas);

        // Set canvas size dengan benar
        const rect = signatureDiv.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;

        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;

        const ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);
        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        // Event listeners
        canvas.addEventListener('touchstart', handleStart, { passive: false });
        canvas.addEventListener('touchmove', handleMove, { passive: false });
        canvas.addEventListener('touchend', handleEnd, { passive: false });
        canvas.addEventListener('mousedown', handleStart);
        canvas.addEventListener('mousemove', handleMove);
        canvas.addEventListener('mouseup', handleEnd);
        canvas.addEventListener('mouseout', handleEnd);

        function handleStart(e) {
            e.preventDefault();
            isDrawing = true;
            const pos = getPosition(e);
            lastX = pos.x;
            lastY = pos.y;

            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            updateStatus();
        }

        function handleMove(e) {
            e.preventDefault();
            if (!isDrawing) return;

            const pos = getPosition(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            lastX = pos.x;
            lastY = pos.y;
        }

        function handleEnd(e) {
            e.preventDefault();
            if (isDrawing) {
                isDrawing = false;
                ctx.closePath();
                updateStatus();
            }
        }

        function getPosition(e) {
            const rect = canvas.getBoundingClientRect();
            let x, y;

            if (e.type.includes('touch')) {
                const touch = e.touches[0] || e.changedTouches[0];
                x = touch.clientX - rect.left;
                y = touch.clientY - rect.top;
            } else {
                x = e.clientX - rect.left;
                y = e.clientY - rect.top;
            }

            return { x, y };
        }

        function updateStatus() {
            if (statusEl) {
                statusEl.textContent = isEmpty() ? 'Belum ada tanda tangan' : 'Tanda tangan tersimpan';
                statusEl.className = isEmpty() ? 'text-xs text-gray-500' : 'text-xs text-green-600';
            }
        }

        function isEmpty() {
            const pixels = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
            return !pixels.some(pixel => pixel !== 0);
        }

        // Simpan ke global scope
        signaturePad = {
            canvas: canvas,
            ctx: ctx,
            clear: function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                updateStatus();
            },
            isEmpty: isEmpty,
            toDataURL: function() {
                return canvas.toDataURL('image/png');
            }
        };

        console.log('Signature pad initialized successfully');
    }

    function clearSignature() {
        if (signaturePad) {
            signaturePad.clear();
        }
    }

    // Initialize when Alpine is ready
    document.addEventListener('alpine:initialized', () => {
        console.log('Alpine initialized');

        // Watch for modal changes
        const modalElement = document.querySelector('[x-data]');
        if (modalElement) {
            // Use MutationObserver to watch for style changes
            const observer = new MutationObserver(() => {
                const modal = document.querySelector('[x-show="openFinishDialog"]');
                if (modal && modal.style.display !== 'none') {
                    setTimeout(() => {
                        initSignaturePad();
                    }, 300);
                }
            });

            observer.observe(modalElement, {
                attributes: true,
                attributeFilter: ['style']
            });
        }
    });

    // Form submit handler
    async function handleFinishSubmit(e) {
        e.preventDefault();
        console.log('Form submit triggered');

        if (!signaturePad) {
            alert('Signature pad belum diinisialisasi. Silakan coba lagi.');
            return false;
        }

        if (signaturePad.isEmpty()) {
            alert('Mohon berikan tanda tangan Anda sebelum melanjutkan.');
            return false;
        }

        try {
            const signatureData = signaturePad.toDataURL();
            const hiddenInput = document.getElementById('signature-data');
            const form = e.target;
            const formData = new FormData(form);

            if (hiddenInput) {
                hiddenInput.value = signatureData;
                formData.append('signature', signatureData);
            }

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                window.location.href = "{{ route('logbook.index', ['location' => $location]) }}";
            } else {
                alert(result.message || 'Terjadi kesalahan saat menyimpan tanda tangan');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses tanda tangan');
        }
    }

    // Definisikan function di global scope
    async function submitDetail(event) {
        event.preventDefault();

        // Gunakan ID yang sesuai dengan HTML
        const waktuMulai = document.getElementById('start_time').value;
        const waktuSelesai = document.getElementById('end_time').value;
        const uraian = document.getElementById('summary').value;
        const keterangan = document.getElementById('description').value;
        const logbookID = document.getElementById('logbookID').value;

        // Validasi sederhana
        if (!waktu || !uraian || !keterangan || !logbookID) {
            alert("Semua field wajib diisi.");
            return;
        }

        try {
            // Ambil CSRF token dari form atau meta tag
            let csrfToken = document.querySelector('input[name="_token"]')?.value;
            if (!csrfToken) {
                csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            }

            const response = await fetch("{{ route('logbook.detail.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    logbookID: logbookID,
                    start_time: waktuMulai, // Jika hanya pakai 1 input waktu, bisa pakai sama
                    end_time: waktuSelesai, // Jika hanya pakai 1 input waktu, bisa pakai sama
                    summary: uraian,
                    description: keterangan
                })
            });

            const result = await response.json();

            if (response.ok) {
                // Reset form
                document.getElementById('formDetail').reset();
                document.querySelector('[x-data]').__x.$data.openAddDetail = false;

                // Atau reload halaman untuk menampilkan data baru
                window.location.reload();
            } else {
                alert("Gagal menyimpan data: " + (result.message ?? 'Terjadi kesalahan.'));
            }

        } catch (error) {
            console.error("Error:", error);
            alert("Terjadi kesalahan pada server.");
        }
    }

    async function submitEditDetail(event) {
        event.preventDefault();

        // Ambil data dari Alpine.js
        const alpineData = document.querySelector('[x-data]').__x.$data;
        const editData = alpineData.editDetailData;
        // const id = editData.id;

        // Validasi sederhana
        if (!editData.start_time || !editData.summary || !editData.description || !editData.id) {
            alert("Semua field wajib diisi.");
            return;
        }

        try {
            // Ambil CSRF token dari form atau meta tag
            let csrfToken = document.querySelector('input[name="_token"]')?.value;
            if (!csrfToken) {
                csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            }

            // Show loading state
            const submitButton = event.target.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.textContent = 'Memperbarui...';
            submitButton.disabled = true;


            const response = await fetch(`/logbook/detail/update/${editData.id}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    start_time: editData.start_time,
                    end_time: editData.end_time,
                    summary: editData.summary,
                    description: editData.description
                })
            });

            const result = await response.json();

            if (response.ok) {
                // Reset form dan tutup modal
                alpineData.openEditDetail = false;

                // Show success message
                alert("Data berhasil diupdate!");

                // Reload halaman untuk menampilkan data terbaru
                window.location.reload();
            } else {
                alert("Gagal mengupdate data: " + (result.message ?? 'Terjadi kesalahan.'));
            }

        } catch (error) {
            console.error("Error:", error);
            alert("Terjadi kesalahan pada server.");
        } finally {
            // Reset button state
            const submitButton = event.target.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            }
        }
    }

    function confirmDelete(message) {
        return confirm(message);
    }

</script>

@endsection
