@extends('layouts.app')

@section('title', 'Logbook Pos Jaga ')
@section('content')
<div x-data="{
        openLogbook: false,
        openEditLogbook: false,
        editLogbookData: {
            logbookID: null,
            date: '',
            grup: '',
            shift: ''
        },
    }" class="mx-auto p-0 sm:p-6 min-h-screen pt-5 sm:pt-20">


    <!-- Alert Messages with Enhanced Design -->
    @if(session('success'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 2000)"
        x-show="show"
        x-transition
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
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 2000)"
        x-show="show"
        x-transition
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

    <!-- Logbook Section -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-6 text-white">
            <div class="flex flex-col sm:flex-row justify-between items-start">
                <div>
                    <h3 class="text-2xl font-bold mb-1">{{ 'Logbook Pos Jaga ' }}</h3>
                    <p class="text-blue-100">Catatan aktivitas pos jaga harian</p>
                </div>
                <button @click="openLogbook = true"
                    class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 mt-3 sm:mt-0 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto ">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Data
                </button>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 md:hidden">
            @forelse($logbooks ?? [] as $logbook)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200" @click="window.location.href='{{ route('logbook.detail', ['id' => $logbook->logbookID]) }}'">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">{{ \Carbon\Carbon::parse($logbook->date)->format('d M Y') }}</span>
                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-teal-100 rounded-full">{{ $logbook->shift ?? 'N/A' }}</span>
                    </div>
                    <div class="mt-4 space-y-2 text-sm text-gray-600">
                        <p><strong class="font-medium text-gray-800">Area:</strong> {{ $logbook->locationArea->name ?? 'N/A' }}</p>
                        <p><strong class="font-medium text-gray-800">Group:</strong> {{ $logbook->grup ?? 'N/A' }}</p>
                        <p><strong class="font-medium text-gray-800">Dinas/Shift:</strong> {{ $logbook->shift ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 flex justify-end space-x-2">
                    <button type="button"
                        class="p-2 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors duration-200"
                        @click.stop="
                            openEditLogbook = true;
                            editLogbookData.logbookID = '{{ $logbook->logbookID }}';
                            editLogbookData.date = '{{ $logbook->date->format('Y-m-d') }}';
                            editLogbookData.grup = '{{ addslashes($logbook->grup ?? '') }}';
                            editLogbookData.shift = '{{ addslashes($logbook->shift ?? '') }}';
                        ">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <form action="{{ route('logbook.destroy', $logbook->logbookID) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200" @click.stop>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-1 sm:col-span-2 text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 text-lg">Belum ada data logbook</p>
                <p class="text-gray-400">Tambahkan data pertama Anda</p>
            </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hidden md:block">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            No</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tanggal</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Area</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Grup</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Dinas/Shift</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logbooks ?? [] as $index => $logbook)
                    <tr @click="window.location.href='{{ route('logbook.detail', ['id' => $logbook->logbookID]) }}'"
                        class="hover:bg-gray-50 transition-colors duration-200 cursor-pointer">

                        <td class="px-5 py-4 text-blue-600 font-bold">{{ $index + 1 }}</td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3a1 1 0 012 0v4m0 0V3a1 1 0 012 0v4m0 0a1 1 0 011 1v3H7V8a1 1 0 011-1zM3 19a2 2 0 002 2h6a2 2 0 002-2v-5H3v5zM15 7a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z">
                                        </path>
                                    </svg>
                                </div>
                                <span
                                    class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($logbook->date)->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $logbook->locationArea->name ?? 'N/A' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $logbook->grup ?? 'N/A' }}</td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-semibold text-teal-800 bg-teal-100 rounded-full">
                                {{ $logbook->shift ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap flex space-x-2">
                            <button type="button"
                                class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-4 py-2 rounded-lg font-medium transition-colors duration-200"
                                @click.stop="
                                    openEditLogbook = true;
                                    editLogbookData.logbookID = '{{ $logbook->logbookID }}';
                                    editLogbookData.date = '{{ $logbook->date->format('Y-m-d') }}';
                                    editLogbookData.location_area_id = '{{ $logbook->location_area_id }}';
                                    editLogbookData.grup = '{{ addslashes($logbook->grup ?? '') }}';
                                    editLogbookData.shift = '{{ addslashes($logbook->shift ?? '') }}';
                                ">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Edit
                            </button>
                            <form action="{{ route('logbook.destroy', $logbook->logbookID) }}"
                                method="POST"
                                onsubmit="event.stopPropagation(); return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-100 text-red-600 hover:bg-red-200 px-4 py-2 rounded-lg font-medium transition-colors duration-200"
                                    @click.stop>
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
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="text-gray-500 text-lg">Belum ada data logbook</p>
                            <p class="text-gray-400">Tambahkan data pertama Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if(isset($logbooks) && method_exists($logbooks, 'links'))
            <div class="px-4 py-3 bg-white border-t border-gray-200">
                {{ $logbooks->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Modal Tambah Logbook --}}
    <div x-show="openLogbook" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openLogbook = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Tambah Entry Logbook</h2>
                <p class="text-blue-100">Tambahkan catatan pos jaga baru</p>
            </div>
            <form action="{{ route('logbook.store') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" id="date" name="date" required
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                        readonly>
                    @error('date')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Dropdown Location -->
                <div class="mb-6">
                    <label for="location_area_id" class="block text-sm font-semibold text-gray-700 mb-2">Area Pos Jaga</label>
                    <select id="location_area_id" name="location_area_id" required
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                        <option value="">Pilih Lokasi</option>
                        @if(isset($locations))
                        @foreach($locations as $location)
                        <option value="{{ $location->id }}">
                            {{ $location->name }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                    @error('location_area_id')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="grup" class="block text-sm font-semibold text-gray-700 mb-2">Grup</label>
                    <select id="grup" name="grup" required
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                        <option value="">Pilih Grup</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </select>
                    @error('grup')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="shift" class="block text-sm font-semibold text-gray-700 mb-2">Dinas / Shift</label>
                    <select id="shift" name="shift" required
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                        <option value="">Pilih Dinas/Shift</option>
                        <option value="Pagi">Pagi</option>
                        <option value="Malam">Malam</option>
                    </select>
                    @error('shift')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openLogbook = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Logbook --}}
    <div x-show="openEditLogbook" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEditLogbook = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Edit Entry Logbook</h2>
                <p class="text-blue-100">Ubah informasi logbook</p>
            </div>
            <form :action="'{{ url('/logbook/posjaga') }}/' + editLogbookData.logbookID" method="POST" class="p-6">
                @csrf
                @method('PATCH')
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="date" required x-model="editLogbookData.date"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                    @error('date')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="edit_location_area_id" class="block text-sm font-semibold text-gray-700 mb-2">Area Pos Jaga</label>
                    <select id="edit_location_area_id" name="location_area_id" required x-model="editLogbookData.location_area_id"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                        <option value="">Pilih Lokasi</option>
                        @if(isset($locations))
                        @foreach($locations as $location)
                        <option value="{{ $location->id }}">
                            {{ $location->name }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                    @error('location_area_id')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Grup</label>
                    <select name="grup" required x-model="editLogbookData.grup"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                        <option value="">Pilih Grup</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </select>
                    @error('grup')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dinas / Shift</label>
                    <select name="shift" required x-model="editLogbookData.shift"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200">
                        <option value="">Pilih Dinas/Shift</option>
                        <option value="Pagi">Pagi</option>
                        <option value="Malam">Malam</option>
                    </select>
                    @error('shift')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openEditLogbook = false"
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set current date for tanggal input
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${day}`;
        const dateInput = document.getElementById('date');
        if (dateInput) {
            dateInput.value = formattedDate;
        }

        // Form validation
        const routeLogbookStore = "{{ route('logbook.store') }}";
        const form = document.querySelector(`form[action="${routeLogbookStore}"]`);
        if (form) {
            form.addEventListener('submit', function(e) {
                const grup = form.querySelector('#grup').value;
                const shift = form.querySelector('#shift').value;

                if (!grup || !shift) {
                    e.preventDefault();
                    alert('Silakan lengkapi semua field yang diperlukan');
                    return false;
                }
            });
        }

        // Reset form when modal is closed
        const resetForm = () => {
            if (form) {
                form.reset();
                dateInput.value = formattedDate;
            }
        };

        // Add event listener for modal close button
        const closeButtons = document.querySelectorAll('[x-on\\:click="openLogbook = false"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', resetForm);
        });
    });
</script>
@endsection
