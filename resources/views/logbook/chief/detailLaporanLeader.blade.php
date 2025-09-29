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
        openAddPersonil: false,
        openEditPersonil: false,
        openAddFasilitas: false,
        openEditFasilitas: false,
        openAddKemajuan: false,
        openEditKemajuan: false,
        openEditDetailUraian: false,
        openAddDetailUraian: false,
        openFinishDialog: false,
        editDetailData: {
            id: null,
            start_time: '',
            end_time: '',
            summary: '',
            description: ''
        },
        editPersonilData: {
            id: null,
            staffID: null,
            nama: '',
            classification: '',
            description: ''
        },
        editFacilityData: {
            id: null,
            facility: '',
            quantity: '',
            description: ''
        },
        editKemajuanData: {
            id: null,
            jml_personil: '',
            jml_hadir: '',
            materi: '',
            keterangan: ''
        },
        openEditDetailFn(id, startTime, endTime, summary, description) {
            // Function yang sudah ada...
            this.editDetailData.id = id;
            if (startTime) {
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
            this.openEditDetailUraian = true;
        },
        openEditPersonilFn(id, staffID, nama, classification, description) {
            this.editPersonilData.id = id;
            this.editPersonilData.staffID = staffID;
            this.editPersonilData.nama = nama || '';
            this.editPersonilData.classification = classification || '';
            this.editPersonilData.description = description || '';
            this.openEditPersonil = true;
        },
        openEditFasilitasFn(id, facility, quantity, description) {
            this.editFacilityData.id = id;
            this.editFacilityData.facility = facility;
            this.editFacilityData.quantity = quantity;
            this.editFacilityData.description = description;
            this.openEditFasilitas = true;
        },
        openEditKemajuanFn(id, jml_personil, jml_hadir, materi, keterangan) {
            this.editKemajuanData.id = id;
            this.editKemajuanData.jml_personil = jml_personil;
            this.editKemajuanData.jml_hadir = jml_hadir;
            this.editKemajuanData.materi = materi;
            this.editKemajuanData.keterangan = keterangan;
            this.openEditKemajuan = true;
        }
    }">
    <div class="mb-4">
        <a href="{{ route('logbook.chief.index')}}"
            class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Tampilan Information -->
    <div class="overflow-hidden mb-8">
        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 md:hidden">
            @if(isset($logbook))
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 cursor-pointer">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">{{
                            \Carbon\Carbon::parse($logbook->tanggal)->locale('id')->translatedFormat('l,
                            d F Y') }}</span>
                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-teal-100 rounded-full">{{
                            $logbook->shift ?? 'N/A' }}</span>
                    </div>
                    <div class="mt-4 space-y-2 text-sm text-gr</div>ay-600">
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

        <!-- Desktop Table View  -->
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hidden md:block">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Informasi Logbook Chief</h3>
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class=" rounded-lg ">
                    <ul class="text-blue-600">
                        <li><strong>ID:</strong> {{ $logbook->logbookID ?? 'N/A' }}</li>
                        <li><strong>Tanggal:</strong> {{
                            \Carbon\Carbon::parse($logbook->tanggal)->locale('id')->translatedFormat('l,
                            d F Y') }}</li>
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

    <!-- SECTION KEMAJUAN PERSONIL -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-sky-500 to-indigo-600 px-4 py-4 sm:px-6 sm:py-6 text-white">
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-1">Laporan Kemajuan Personil</h3>
                </div>
                @if (strtolower($logbook->status) == 'draft')
                <div class="flex flex-col gap-2 sm:flex-row sm:gap-2 w-full sm:w-auto">
                    <button @click="openAddKemajuan = true"
                        class="w-full sm:w-auto px-4 py-2 bg-blue-400 hover:bg-blue-500 text-white rounded-xl text-sm font-semibold shadow transition">
                        + Tambah
                    </button>
                </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 md:hidden p-4">
            @forelse($kemajuanPersonil ?? [] as $index => $items)
            <div
                class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow duration-200">
                <!-- Header Card -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4">
                    <div class="flex items-center justify-between text-white">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Kemajuan Personil #{{ $index + 1 }}</h3>
                                <p class="text-blue-100 text-sm">Data Kehadiran Personil</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="bg-white bg-opacity-25 px-3 py-1 rounded-full">
                                <span class="font-bold text-lg">{{ $items->jml_hadir }}/{{ $items->jml_personil
                                    }}</span>
                            </div>
                            <p class="text-xs text-blue-100 mt-1">Hadir/Total</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <!-- Data Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <!-- Jumlah Personil -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center space-x-2 mb-2">
                                <div class="bg-blue-100 p-1 rounded">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-700">Jumlah Personil</h4>
                            </div>
                            <p class="text-2xl font-bold text-blue-600">{{ $items->jml_personil }}</p>
                        </div>

                        <!-- Jumlah Hadir -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center space-x-2 mb-2">
                                <div class="bg-green-100 p-1 rounded">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-700">Jumlah Hadir</h4>
                            </div>
                            <p class="text-2xl font-bold text-green-600">{{ $items->jml_hadir }}</p>
                        </div>
                    </div>

                    <!-- Isi Materi -->
                    @if($items->materi)
                    <div class="mb-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="bg-purple-100 p-1 rounded">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-700">Isi Materi</h4>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3">
                            <p class="text-gray-800 text-sm">{{ $items->materi }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Keterangan -->
                    @if($items->keterangan)
                    <div class="mb-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="bg-orange-100 p-1 rounded">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-700">Keterangan</h4>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-3">
                            <p class="text-gray-800 text-sm">{{ $items->keterangan }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    @if (strtolower($logbook->status) == 'draft')
                    <div class="flex justify-end space-x-2">
                        <!-- Edit Button -->
                        <button type="button"
                            class="flex items-center space-x-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200"
                            @click="openEditKemajuanFn({{ $items->id }}, '{{ $items->jml_personil }}', '{{ $items->jml_hadir }}', '{{ addslashes($items->materi ?? '') }}', '{{ addslashes($items->keterangan ?? '') }}')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            <span class="text-sm">Edit</span>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('logbook.chief.deleteKemajuan', $items->id) }}" method="POST"
                            class="inline-block"
                            onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus data kemajuan personil ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex items-center space-x-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                <span class="text-sm">Hapus</span>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="bg-blue-100 p-6 rounded-2xl mb-4 inline-block">
                    <svg class="w-12 h-12 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Data Kemajuan Personil</h3>
                <p class="text-gray-500 mb-4">Tambahkan data kemajuan personil untuk logbook ini</p>
            </div>
            @endforelse
        </div>

        <!-- Desktop Table View Kemajuan Personil -->
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hidden md:block">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jumlah
                            Personil</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jumlah
                            Hadir</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Isi
                            Materi</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Keterangan</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($kemajuanPersonil ?? [] as $index => $items)
                    <tr class="hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
                        <td class="px-5 py-4 text-sky-600 font-bold">{{ $index + 1 }}</td>
                        <td class="px-5 py-4">{{ $items->jml_personil }}</td>
                        <td class="px-5 py-4">{{ $items->jml_hadir }}</td>
                        <td class="px-5 py-4">{{ $items->materi ?? 'N/A' }}</td>
                        <td class="px-5 py-4">{{ $items->keterangan ?? 'N/A' }}</td>
                        <td class="px-5 py-4 flex justify-center space-x-2" @click.stop>
                            @if (strtolower($logbook->status) == 'draft')
                            <!-- Edit button -->
                            <button type="button"
                                class="p-2 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors duration-200"
                                title="Edit Kemajuan"
                                @click="openEditKemajuanFn({{ $items->id }}, '{{ $items->jml_personil }}', '{{ $items->jml_hadir }}', '{{ addslashes($items->materi ?? '') }}', '{{ addslashes($items->keterangan ?? '') }}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>

                            <!-- Delete button -->
                            <form action="{{ route('logbook.chief.deleteKemajuan',$items->id) }}" method="POST"
                                class="inline-block"
                                onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus Kemajuan Personil ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200"
                                    title="Hapus Kemajuan Personil">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12">
                            <p class="text-gray-500 text-lg">Belum ada fasilitas</p>
                            <p class="text-gray-400">Tambahkan fasilitas pertama Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Laporan Kemajuan Personil -->
    <div x-show="openAddKemajuan" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openAddKemajuan = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-sky-500 to-indigo-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Tambah Kemajuan Personil</h2>
                <p class="text-blue-100">Masukkan Data Kemajuan Personil baru</p>
            </div>

            <form action="{{ route('logbook.chief.addKemajuan') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="logbook_chief_id" value="{{ $logbook->logbookID ?? '' }}">

                <div class="mb-2 sm:mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Personil</label>
                    <input type="number" name="jml_personil" value="{{ old('jml_personil') }}"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('jml_personil') border-red-500 @enderror"
                        placeholder="Masukkan Jumlah Personil" required>
                    @error('jml_personil')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-2 sm:mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Hadir</label>
                    <input type="number" name="jml_hadir" value="{{ old('jml_hadir') }}"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('jml_hadir') border-red-500 @enderror"
                        placeholder="Masukkan Jumlah Hadir" required>
                    @error('jml_hadir')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-2 sm:mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Materi Apel</label>
                    <textarea name="materi" rows="3"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('materi') border-red-500 @enderror"
                        placeholder="Masukkan Materi Apel" required>{{ old('materi') }}</textarea>
                    @error('materi')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-2 sm:mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="3"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('keterangan') border-red-500 @enderror"
                        placeholder="Masukkan Keterangan Kegiatan" required>{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openAddKemajuan = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 text-white rounded-xl hover:from-sky-600 hover:to-indigo-700 transition-all duration-200 font-medium shadow-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openEditKemajuan" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEditKemajuan = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-sky-500 to-indigo-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Edit Kemajuan Personil</h2>
                <p class="text-blue-100">Perbarui Data Kemajuan Personil</p>
            </div>

            <form x-bind:action="`/logbook/chief/update-kemajuan/${editKemajuanData.id}`" method="POST" class="p-6">
                @csrf
                @method('PUT') {{-- Gunakan PUT atau PATCH untuk update --}}

                <input type="hidden" name="logbook_chief_id" value="{{ $logbook->logbookID ?? '' }}">

                <div class="mb-4">
                    <label for="edit_jml_personil" class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Total
                        Personil</label>
                    <input type="number" id="edit_jml_personil" name="jml_personil"
                        x-model="editKemajuanData.jml_personil"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('jml_personil') border-red-500 @enderror"
                        placeholder="Masukkan jumlah total personil" required>
                    @error('jml_personil')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="edit_jml_hadir" class="block text-sm font-semibold text-gray-700 mb-2">Jumlah
                        Hadir</label>
                    <input type="number" id="edit_jml_hadir" name="jml_hadir" x-model="editKemajuanData.jml_hadir"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('jml_hadir') border-red-500 @enderror"
                        placeholder="Masukkan jumlah personil yang hadir" required>
                    @error('jml_hadir')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="edit_materi" class="block text-sm font-semibold text-gray-700 mb-2">Materi</label>
                    <textarea id="edit_materi" name="materi" rows="3" x-model="editKemajuanData.materi"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('materi') border-red-500 @enderror"
                        placeholder="Masukkan materi yang disampaikan" required></textarea>
                    @error('materi')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="edit_keterangan"
                        class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <textarea id="edit_keterangan" name="keterangan" rows="3" x-model="editKemajuanData.keterangan"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('keterangan') border-red-500 @enderror"
                        placeholder="Masukkan keterangan tambahan" required></textarea>
                    @error('keterangan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openEditKemajuan = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-medium shadow-lg">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SECTION PERSONIL -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-sky-500 to-indigo-600 px-4 py-4 sm:px-6 sm:py-6 text-white">
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-1">Personil</h3>
                </div>
                @if (strtolower($logbook->status) == 'draft')
                <div class="flex flex-col gap-2 sm:flex-row sm:gap-2 w-full sm:w-auto">
                    <button @click="openAddPersonil = true"
                        class="w-full sm:w-auto px-4 py-2 bg-blue-400 hover:bg-blue-500 text-white rounded-xl text-sm font-semibold shadow transition">
                        + Tambah Personil
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Mobile Card View Personil -->
        <div class="grid grid-cols-1 gap-3 md:hidden p-4">
            @forelse($personil ?? [] as $index => $items)
            <div
                class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow duration-200">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                </path>
                            </svg>
                            <span class="font-semibold">{{ $items->classification }}</span>
                        </div>
                        <span class="text-blue-200 text-sm">#{{ $index + 1 }}</span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <span class="px-3 py-1 text-sm font-medium text-blue-800 bg-blue-100 rounded-lg">
                                {{$items->user->name}}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-sm font-medium text-indigo-800 bg-indigo-100 rounded-lg">
                                {{ $items->description }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if (strtolower($logbook->status) == 'draft')
                    <div class="flex justify-end space-x-2">
                        <!-- Edit Button -->
                        <button type="button"
                            class="flex items-center space-x-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200"
                            @click="openEditPersonilFn({{ $items->id }}, {{ $items->staffID }}, '{{ addslashes($items->user->name ?? 'N/A') }}', '{{ addslashes($items->classification ?? 'N/A') }}', '{{ $items->description }}')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            <span class="text-sm">Edit</span>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('logbook.chief.deletePersonil', $items->id) }}" method="POST"
                            class="inline-block"
                            onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus personil ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex items-center space-x-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                <span class="text-sm">Hapus</span>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="bg-blue-100 p-6 rounded-2xl mb-4 inline-block">
                    <svg class="w-12 h-12 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Personil</h3>
                <p class="text-gray-500 mb-4">Tambahkan personil untuk logbook ini</p>
                <button @click="openAddPersonil = true"
                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-md">
                    Tambah Personil
                </button>
            </div>
            @endforelse
        </div>

        <!-- Desktop Table View Personil -->
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hidden md:block">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Klasifikasi</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Keterangan</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($personil ?? [] as $index => $items)
                    <tr class="hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
                        <td class="px-5 py-4 text-sky-600 font-bold">{{ $index + 1 }}</td>
                        <td class="px-5 py-4">{{ $items->user->name ?? 'N/A' }}</td>
                        <td class="px-5 py-4">{{ $items->classification ?? 'N/A' }}</td>
                        <td class="px-5 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($items->description == 'hadir') bg-green-100 text-green-800
                                @elseif($items->description == 'izin') bg-yellow-100 text-yellow-800
                                @elseif($items->description == 'sakit') bg-red-100 text-red-800
                                @elseif($items->description == 'cuti') bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($items->description) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 flex justify-center space-x-2" @click.stop>
                            @if (strtolower($logbook->status) == 'draft')
                            <!-- Edit button -->
                            <button type="button"
                                class="p-2 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors duration-200"
                                title="Edit Personil"
                                @click="openEditPersonilFn({{ $items->id }}, {{ $items->staffID }}, '{{ addslashes($items->user->name ?? 'N/A') }}', '{{ addslashes($items->classification ?? 'N/A') }}', '{{ $items->description }}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>
                            <!-- Delete button -->
                            <form action="{{ route('logbook.chief.deletePersonil',$items->id) }}" method="POST"
                                class="inline-block"
                                onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus personil ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200"
                                    title="Hapus Personil">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12">
                            <p class="text-gray-500 text-lg">Belum ada personil</p>
                            <p class="text-gray-400">Tambahkan personil pertama Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Personil -->
    <div x-show="openAddPersonil" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openAddPersonil = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-sky-500 to-indigo-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Tambah Personil</h2>
                <p class="text-blue-100">Masukkan data personil baru</p>
            </div>

            <form action="{{ route('logbook.chief.addPersonil') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="logbook_chief_id" value="{{ $logbook->logbookID ?? '' }}">

                <div class="mb-2 sm:mb-4">
                    <label for="staffID" class="block text-gray-700 font-semibold text-sm sm:text-sm mb-1 sm:mb-2">
                        Nama
                    </label>

                    <select name="staffID" id="staffID"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-sky-500 focus:outline-none transition-colors duration-200 @error('staffID') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Officer</option>
                        @foreach($availableOfficers as $officer)
                        <option value="{{ $officer->id }}" data-lisensi="{{ $officer->lisensi }}"
                            @if(old('staffID')==$officer->id) selected @endif>
                            {{ $officer->name }} - {{ $officer->lisensi }}
                        </option>
                        @endforeach
                    </select>

                    @error('staffID')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold text-sm sm:text-sm mb-1 sm:mb-2">Keterangan</label>
                    <select name="description" id="description"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-sky-500 focus:outline-none transition-colors duration-200 @error('description') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Keterangan</option>
                        <option value="hadir" {{ old('description')=='hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="izin" {{ old('description')=='izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ old('description')=='sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="cuti" {{ old('description')=='cuti' ? 'selected' : '' }}>Cuti</option>
                    </select>
                    @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openAddPersonil = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 text-white rounded-xl hover:from-sky-600 hover:to-indigo-700 transition-all duration-200 font-medium shadow-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Personil -->
    <div x-show="openEditPersonil" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEditPersonil = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-sky-500 to-indigo-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Edit Personil</h2>
                <p class="text-blue-100">Ubah informasi personil</p>
            </div>

            <form x-bind:action="`{{ url('/logbook/chief/update-personil') }}/${editPersonilData.id}`" method="POST"
                class="p-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="logbook_chief_id" value="{{ $logbook->logbookID ?? '' }}">

                <div class="mb-2 sm:mb-4">
                    <label for="edit_staffID" class="block text-gray-700 font-semibold text-sm sm:text-sm mb-1 sm:mb-2">
                        Nama
                    </label>

                    <select name="staffID" id="edit_staffID" x-model="editPersonilData.staffID"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-sky-500 focus:outline-none transition-colors duration-200"
                        required>
                        <option value="">Pilih Officer</option>

                        @foreach($allOfficers as $officer)
                        <option value="{{ $officer->id }}" data-lisensi="{{ $officer->lisensi }}"
                            :disabled="{{ $personil->pluck('staffID')->toJson() }}.includes({{ $officer->id }}) && {{ $officer->id }} != editPersonilData.staffID">
                            {{ $officer->name }} - {{ $officer->lisensi }}
                        </option>
                        @endforeach
                    </select>

                    @error('staffID')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <select name="description" id="edit_description" x-model="editPersonilData.description"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-sky-500 focus:outline-none transition-colors duration-200"
                        required>
                        <option value="">Pilih Keterangan</option>
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="cuti">Cuti</option>
                    </select>
                    @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openEditPersonil = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 text-white rounded-xl hover:from-sky-600 hover:to-indigo-700 transition-all duration-200 font-medium shadow-lg">
                        Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SECTION FASILITAS -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-sky-500 to-indigo-600 px-4 py-4 sm:px-6 sm:py-6 text-white">
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-1">Fasilitas</h3>
                </div>
                @if (strtolower($logbook->status) == 'draft')
                <div class="flex flex-col gap-2 sm:flex-row sm:gap-2 w-full sm:w-auto">
                    <button @click="openAddFasilitas = true"
                        class="w-full sm:w-auto px-4 py-2 bg-blue-400 hover:bg-blue-500 text-white rounded-xl text-sm font-semibold shadow transition">
                        + Tambah Fasilitas
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Mobile Card View Fasilitas -->
        <div class="grid grid-cols-1 gap-3 md:hidden p-4">
            @forelse($facility ?? [] as $index => $items)
            <div
                class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow duration-200">
                <!-- Header Card -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4">
                    <div class="flex items-center justify-between text-white">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 008 10.172V5L8 4z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">{{ $items->facility ?? 'Unknown Equipment' }}
                                </h3>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="bg-white bg-opacity-25 px-3 py-1 rounded-full">
                                <span class="font-bold text-lg">{{ $items->quantity }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <!-- Description -->
                    <div class="mb-4">
                        <p class="text-gray-800 text-sm">{{ $items->description ?: 'Tidak ada keterangan' }}</p>
                    </div>

                    <!-- Action Buttons -->
                    @if (strtolower($logbook->status) == 'draft')
                    <div class="flex justify-end space-x-2">
                        <!-- Edit Button -->
                        <button type="button"
                            class="flex items-center space-x-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200"
                            @click="openEditFasilitasFn({{ $items->id }}, '{{ $items->facility ?? '' }}', {{ $items->quantity }}, '{{ addslashes($items->description ?? '') }}')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            <span class="text-sm">Edit</span>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('logbook.chief.deleteFacility', $items->id) }}" method="POST"
                            class="inline-block"
                            onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus fasilitas ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex items-center space-x-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                <span class="text-sm">Hapus</span>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="bg-blue-100 p-6 rounded-2xl mb-4 inline-block">
                    <svg class="w-12 h-12 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 008 10.172V5L8 4z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Fasilitas</h3>
                <p class="text-gray-500 mb-4">Tambahkan fasilitas untuk logbook ini</p>
                <button @click="openAddFasilitas = true"
                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-md">
                    Tambah Fasilitas
                </button>
            </div>
            @endforelse
        </div>

        <!-- Desktop Table View Fasilitas -->
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hidden md:block">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Fasilitas</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jumlah
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Keterangan</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($facility ?? [] as $index => $items)
                    <tr class="hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
                        <td class="px-5 py-4 text-sky-600 font-bold">{{ $index + 1 }}</td>
                        <td class="px-5 py-4">{{ $items->facility ?? 'N/A' }}</td>
                        <td class="px-5 py-4">{{ $items->quantity }}</td>
                        <td class="px-5 py-4">{{ $items->description }}</td>
                        <td class="px-5 py-4 flex justify-center space-x-2" @click.stop>
                            @if (strtolower($logbook->status) == 'draft')
                            <!-- Edit button -->
                            <button type="button"
                                class="p-2 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors duration-200"
                                title="Edit Fasilitas"
                                @click="openEditFasilitasFn({{ $items->id }}, '{{ $items->facility ?? '' }}', {{ $items->quantity }}, '{{ addslashes($items->description ?? '') }}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>

                            <!-- Delete button -->
                            <form action="{{ route('logbook.chief.deleteFacility',$items->id) }}" method="POST"
                                class="inline-block"
                                onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus fasilitas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200"
                                    title="Hapus Fasilitas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12">
                            <p class="text-gray-500 text-lg">Belum ada fasilitas</p>
                            <p class="text-gray-400">Tambahkan fasilitas pertama Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Fasilitas -->
    <div x-show="openAddFasilitas" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openAddFasilitas = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-sky-500 to-indigo-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Tambah Fasilitas</h2>
                <p class="text-blue-100">Masukkan data fasilitas baru</p>
            </div>

            <form action="{{ route('logbook.chief.addFacility') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="logbook_chief_id" value="{{ $logbook->logbookID ?? '' }}">

                <div class="mb-2 sm:mb-4">
                    <label for="facility" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                        Fasilitas</label>
                    <input type="text" name="facility" id="facility"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('facilityName') border-red-500 @enderror"
                        value="{{ old('facility') }}" placeholder="Masukkan nama fasilitas" required>
                    @error('facility')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-2 sm:mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah</label>
                    <input type="number" name="quantity" value="{{ old('quantity') }}"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('quantity') border-red-500 @enderror"
                        placeholder="Masukkan jumlah fasilitas" required>
                    @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-2 sm:mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <textarea name="description" rows="3"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('description') border-red-500 @enderror"
                        placeholder="Masukkan keterangan fasilitas" required>{{ old('description') }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openAddFasilitas = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 text-white rounded-xl hover:from-sky-600 hover:to-indigo-700 transition-all duration-200 font-medium shadow-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Fasilitas -->
    <div x-show="openEditFasilitas" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEditFasilitas = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-sky-500 to-indigo-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Edit Fasilitas</h2>
                <p class="text-blue-100">Perbarui data fasilitas</p>
            </div>

            <form x-bind:action="`{{ url('/logbook/chief/update-facility') }}/${editFacilityData.id}`" method="POST"
                class="p-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="logbook_chief_id" value="{{ $logbook->logbookID ?? '' }}">

                <div class="mb-2 sm:mb-4">
                    <label for="facility" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                        Fasilitas</label>
                    <input type="text" name="facility" x-model="editFacilityData.facility"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('facility') border-red-500 @enderror"
                        value="{{ old('facility') }}" placeholder="Masukkan nama fasilitas" required>
                    @error('facility')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-2 sm:mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah</label>
                    <input type="number" name="quantity" x-model="editFacilityData.quantity"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('quantity') border-red-500 @enderror"
                        placeholder="Masukkan jumlah fasilitas" required>
                    @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-2 sm:mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <textarea name="description" rows="3" x-model="editFacilityData.description"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('description') border-red-500 @enderror"
                        placeholder="Masukkan keterangan fasilitas" required></textarea>
                    @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openEditFasilitas = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-medium shadow-lg">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Uraian Kegiatan Section -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-sky-500 to-indigo-600 px-4 py-4 sm:px-6 sm:py-6 text-white">
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-1">{{ 'Uraian Kegiatan'}}</h3>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:gap-2 w-full sm:w-auto">
                    @if (strtolower($logbook->status) == 'draft')
                    <button @click="openAddDetailUraian = true"
                        class="w-full sm:w-auto px-4 py-2 bg-blue-400 hover:bg-blue-500 text-white rounded-xl text-sm font-semibold shadow transition">
                        + Tambah Uraian Kegiatan
                    </button>
                    @endif
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('logbook.chief.review.laporan.leader', $logbook->logbookID) }}"
                            class="w-full inline-flex items-center justify-center px-3 py-1.5 border border-blue-300 text-sm font-medium rounded-xl text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview
                        </a>
                    </div>
                    @if (strtolower($logbook->status) == 'draft')
                    <button @click="openFinishDialog = true; $nextTick(() => initializeSignaturePad())"
                        class="w-full sm:w-auto px-4 py-2 bg-gray-200 hover:bg-gray-300 text-blue-800 rounded-xl text-sm font-semibold shadow transition text-center">
                        Selesai
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Selesai -->
        <div x-show="openFinishDialog" x-transition:enter="transition ease-out duration-300"
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

                <form action="{{ route('logbook.chief.signature.send', $logbook->logbookID ?? '') }}" method="POST"
                    class="p-6" onsubmit="return handleSignatureSubmit(event)">
                    @csrf
                    @method('POST')
                    <div class="mb-6">
                        <p class="text-gray-700 text-lg mb-4">Apakah Anda yakin sudah menyelesaikan logbook shift hari
                            ini?</p>

                        <div class="border-2 border-gray-200 rounded-xl p-4 mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan Digital</label>

                            <!-- Container untuk signature pad -->
                            <div class="relative w-full h-48 border border-gray-300 rounded-lg bg-white">
                                @if($logbook->senderSignature)
                                <!-- Tampilkan tanda tangan yang sudah ada -->
                                <img src="data:image/png;base64,{{ $logbook->senderSignature }}"
                                    alt="Tanda tangan sudah ada" class="w-full h-full object-contain">
                                @else
                                <!-- Canvas untuk tanda tangan baru -->
                                <canvas id="signature-canvas" class="w-full h-full"></canvas>
                                @endif
                            </div>

                            <input type="hidden" name="signature" id="signature-data"
                                value="{{ $logbook->senderSignature ?? '' }}">

                            <div class="flex justify-between items-center mt-2">
                                <span id="signature-status" class="text-xs text-gray-500">
                                    {{ $logbook->senderSignature ? 'Tanda tangan sudah ada' : 'Belum ada tanda tangan'
                                    }}
                                </span>
                                @if(!$logbook->senderSignature)
                                <button type="button"
                                    class="text-sm text-blue-600 hover:text-blue-800 transition-colors"
                                    onclick="clearSignature()">
                                    Clear
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-2 sm:mb-4">
                        <label for="approved_by"
                            class="block text-gray-700 font-bold text-xs sm:text-base mb-1 sm:mb-2">Pilih
                            Supervisor yang Mengetahui:
                        </label>
                        <select name="approved_by" id="approved_by"
                            class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base" required>
                            <option value="">Pilih Supervisor</option>
                            @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}" {{ old('approved_by', $logbook->approved_by ?? '') ==
                                $supervisor->id ? 'selected' : '' }}>
                                {{ $supervisor->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="openFinishDialog = false"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">
                            {{ $logbook->senderSignature ? 'Update Konfirmasi' : 'Konfirmasi & Kirim' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mobile Card View Uraian Kegiatan -->
        <div class="grid grid-cols-1 gap-3 md:hidden p-4">
            @forelse($uraianKegiatan ?? [] as $index => $items)
            <div
                class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow duration-200">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">
                                {{ \Carbon\Carbon::parse($items->start_time)->format('H:i') }}
                                {{ $items->end_time ? ' - ' . \Carbon\Carbon::parse($items->end_time)->format('H:i') :
                                '' }}
                            </span>
                        </div>
                        <span class="text-blue-200 text-sm">#{{ $index + 1 }}</span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">{{ $items->summary }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ $items->description }}</p>

                    <!-- Action Buttons -->
                    @if (strtolower($logbook->status) == 'draft')
                    <div class="flex justify-end space-x-2">
                        <!-- Edit Button -->
                        <button type="button"
                            class="flex items-center space-x-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200"
                            @click="openEditDetailFn({{ $items->id }}, '{{ \Carbon\Carbon::parse($items->start_time)->format('H:i') }}', '{{ $items->end_time ? \Carbon\Carbon::parse($items->end_time)->format('H:i') : '' }}', '{{ addslashes($items->summary ?? '') }}', '{{ addslashes($items->description ?? '') }}')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            <span class="text-sm">Edit</span>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('logbook.chief.deleteUraian', $items->id) }}" method="POST"
                            class="inline-block"
                            onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus uraian kegiatan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex items-center space-x-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                <span class="text-sm">Hapus</span>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="bg-blue-100 p-6 rounded-2xl mb-4 inline-block">
                    <svg class="w-12 h-12 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Uraian Kegiatan</h3>
                <p class="text-gray-500 mb-4">Tambahkan uraian kegiatan untuk logbook ini</p>
                <button @click="openAddDetailUraian = true"
                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-md">
                    Tambah Uraian Kegiatan
                </button>
            </div>
            @endforelse
        </div>

        <!-- Desktop Table View Uraian Kegiatan -->
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hidden md:block">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Waktu</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Uraian Kegiatan</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Keterangan</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($uraianKegiatan ?? [] as $index => $items)
                    <tr class="hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
                        <td class="px-5 py-4">
                            {{ \Carbon\Carbon::parse($items->start_time)->format('H:i') }}
                            {{ $items->end_time ? ' - ' . \Carbon\Carbon::parse($items->end_time)->format('H:i') : '' }}
                        </td>
                        <td class="px-5 py-4">{{ $items->summary }}</td>
                        <td class="px-5 py-4">{{ $items->description }}</td>
                        <td class="px-5 py-4 flex justify-center space-x-2" @click.stop>
                            @if (strtolower($logbook->status) == 'draft')
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
                            <form action="{{ route('logbook.chief.deleteUraian', $items->id) }}" method="POST"
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
                            @endif
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
    </div>

    <!-- Modal Tambah Uraian Kegiatan -->
    <div x-show="openAddDetailUraian" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openAddDetailUraian = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-sky-500 to-indigo-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Tambah Uraian Kegiatan</h2>
                <p class="text-blue-100">Masukkan uraian kegiatan baru</p>
            </div>

            <form action="{{ route('logbook.chief.addUraian') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="logbook_chief_id" value="{{ $logbook->logbookID ?? '' }}">

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
                    <textarea type="text" name="summary" value="{{ old('summary') }}"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('summary') border-red-500 @enderror"
                        placeholder="Masukkan uraian kegiatan" required></textarea>
                    @error('summary')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <input name="description" rows="3"
                        class="w-full border-2 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200 @error('description') border-red-500 @enderror"
                        placeholder="Masukkan keterangan" required>{{ old('description') }}</input>
                    @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openAddDetailUraian = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 text-white rounded-xl hover:from-sky-600 hover:to-indigo-700 transition-all duration-200 font-medium shadow-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Uraian Kegiatan -->
    <div x-show="openEditDetailUraian" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEditDetailUraian = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-sky-500 to-indigo-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Edit Uraian Kegiatan</h2>
                <p class="text-blue-100">Ubah informasi uraian kegiatan</p>
            </div>
            <form x-bind:action="`/logbook/chief/update-uraian/${editDetailData.id}`" method="POST" class="p-6">
                @csrf
                @method('PUT')
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
                    <button type="button" @click="openEditDetailUraian = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 text-white rounded-xl hover:from-sky-600 hover:to-indigo-700 transition-all duration-200 font-medium shadow-lg">
                        Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let signaturePadInstance;

    function initializeSignaturePad() {
        // Jika sudah ada tanda tangan di database, tidak perlu inisialisasi
        const existingSignature = document.getElementById('signature-data').value;
        if (existingSignature && existingSignature.startsWith('data:image')) {
            return;
        }

        // Hindari inisialisasi ulang jika sudah ada
        if (signaturePadInstance) {
            signaturePadInstance.clear();
            return;
        }

        const canvas = document.getElementById('signature-canvas');
        if (!canvas) {
            console.error('Elemen canvas untuk tanda tangan tidak ditemukan!');
            return;
        }

        // Sesuaikan ukuran canvas untuk layar HiDPI (Retina)
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        // Inisialisasi SignaturePad
        signaturePadInstance = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)' // Penting agar background tidak transparan
        });

        const statusEl = document.getElementById('signature-status');

        // Update status saat pengguna selesai menulis
        signaturePadInstance.addEventListener("endStroke", () => {
            if (statusEl) {
                statusEl.textContent = 'Tanda tangan tersimpan';
                statusEl.className = 'text-xs text-green-600 font-semibold';
            }
        });
    }

    function clearSignature() {
        // Jika signature pad belum diinisialisasi, inisialisasi dulu
        if (!signaturePadInstance) {
            initializeSignaturePad();
        }

        if (signaturePadInstance) {
            signaturePadInstance.clear();
            const statusEl = document.getElementById('signature-status');
            if (statusEl) {
                statusEl.textContent = 'Belum ada tanda tangan';
                statusEl.className = 'text-xs text-gray-500';
            }
            document.getElementById('signature-data').value = '';
        }
    }

    function handleSignatureSubmit(event) {
        const signatureDataEl = document.getElementById('signature-data');
        const existingSignature = signatureDataEl.value;

        // 1. Validasi pemilihan officer dan supervisor (selalu dilakukan)
        const approved_by = document.getElementById('approved_by').value;

        if (!approved_by) {
            alert('Mohon pilih Supervisor yang mengetahui.');
            event.preventDefault();
            return false;
        }

        // 2. Cek apakah sudah ada tanda tangan di database
        if (existingSignature && existingSignature.trim() !== '') {
            // Jika sudah ada tanda tangan di database, langsung submit
            const confirmSubmit = confirm('Apakah Anda yakin ingin memperbarui konfirmasi logbook ini?');
            if (!confirmSubmit) {
                event.preventDefault();
            }
            return confirmSubmit;
        }

        // 3. Jika tidak ada tanda tangan di database, validasi signature pad
        if (!signaturePadInstance) {
            initializeSignaturePad();
        }

        if (signaturePadInstance.isEmpty()) {
            alert('Mohon berikan tanda tangan Anda sebelum melanjutkan.');
            event.preventDefault();
            return false;
        }

        // 4. Proses tanda tangan baru
        try {
            const signatureData = signaturePadInstance.toDataURL('image/png');
            if (!signatureData.startsWith('data:image/png;base64,')) {
                throw new Error('Format tanda tangan tidak valid');
            }

            signatureDataEl.value = signatureData;

            const confirmSubmit = confirm('Apakah Anda yakin ingin menyelesaikan logbook ini? Tindakan ini tidak dapat dibatalkan.');
            if (!confirmSubmit) {
                event.preventDefault();
            }
            return confirmSubmit;
        } catch (error) {
            alert('Terjadi kesalahan saat memproses tanda tangan: ' + error.message);
            event.preventDefault();
            return false;
        }
    }

    function confirmDelete(message) {
        return confirm(message);
    }

    // Inisialisasi saat modal dibuka
    document.addEventListener('alpine:init', () => {
        Alpine.data('logbook', () => ({
            openFinishDialog: false,
            // ... data alpine lainnya

            initFinishDialog() {
                this.$watch('openFinishDialog', (value) => {
                    if (value) {
                        // Tunggu hingga modal selesai render
                        setTimeout(initializeSignaturePad, 100);
                    }
                });
            }
        }));
    });
</script>

@endsection