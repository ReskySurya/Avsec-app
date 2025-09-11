@extends('layouts.app')

@section('title', 'Buku Pemeriksaan Manual')
@section('content')
<div x-data="{
    openManualBook: false,
    isContinueMode: false,
    formAction: '{{ route('checklist.manualbook.store') }}',
    defaultUserName: '{{ $loggedInUserName ?? '' }}',
    existingDetails: [],
    form: { date: new Date().toISOString().slice(0, 10), shift: '', type: '', rows: [] },

    openFinishModal: false,
    finishFormAction: '',
    signaturePad: null,

    openCreateModal() {
        this.isContinueMode = false;
        this.existingDetails = [];
        this.formAction = '{{ route('checklist.manualbook.store') }}';
        this.form.date = new Date().toISOString().slice(0, 10);
        this.form.shift = '';
        this.form.type = '';
        this.form.rows = [{ time: '', name: this.defaultUserName, pax: '', flight: '', orang: '', barang: '', temuan: '', keterangan: '' }];
        this.openManualBook = true;
    },
    openContinueModal(book) {
        this.isContinueMode = true;
        this.existingDetails = book.details;
        this.formAction = `/checklist-manual-book/add-details/${book.id}`;
        this.form.date = book.date;
        this.form.shift = book.shift;
        this.form.type = book.type;
        this.form.rows = [{ time: '', name: this.defaultUserName, pax: '', flight: '', orang: '', barang: '', temuan: '', keterangan: '' }];
        this.openManualBook = true;
    },
    addRow() {
        this.form.rows.push({ time: '', name: this.defaultUserName, pax: '', flight: '', orang: '', barang: '', temuan: '', keterangan: '' });
    },
    removeRow(index) {
        if (this.form.rows.length > 1) {
            this.form.rows.splice(index, 1);
        }
    },
    prepareFinishModal(book) {
        this.finishFormAction = `/checklist-manual-book/finish/${book.id}`;
        this.openFinishModal = true;
        this.$nextTick(() => {
            const canvas = this.$refs.signatureCanvas;
            if(canvas){
                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
                this.signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });
            }
        });
    },
    clearSignature() {
        if (this.signaturePad) {
            this.signaturePad.clear();
            this.$refs.signatureData.value = '';
        }
    },
    submitFinishForm() {
        if (this.signaturePad && !this.signaturePad.isEmpty()) {
            this.$refs.signatureData.value = this.signaturePad.toDataURL('image/png').split(',')[1];
        } else {
            alert('Tanda tangan digital wajib diisi.');
            return;
        }
        this.$refs.finishForm.submit();
    }
}" class="mx-auto p-2 sm:p-4 min-h-screen pt-5 sm:pt-20">

    {{-- Alert Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
        class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg text-sm sm:text-base">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
        class="bg-red-500 text-white p-3 sm:p-4 rounded-lg mb-4 sm:mb-6 text-sm sm:text-base">
        <strong>Whoops!</strong> Ada beberapa masalah dengan input Anda.<br><br>
        <ul class="list-disc list-inside">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
    </div>
    @endif

    {{-- Mobile: Card Layout --}}
    <div class="block lg:hidden space-y-3 mb-6">
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-3">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Buku Pemeriksaan Manual</h3>
                    <p class="text-xs text-gray-500">Catatan aktivitas harian</p>
                </div>
                <button @click="openCreateModal()"
                    class="bg-blue-500 text-white px-3 py-2 rounded-lg font-semibold text-sm hover:bg-blue-600 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah
                </button>
            </div>
        </div>

        @forelse($manualBooks ?? [] as $book)
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-bold mr-2">
                        {{ $book->id }}
                    </span>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-sm">{{ \Carbon\Carbon::parse($book->date)->format('d
                            M Y') }}</h3>
                        <p class="text-xs text-gray-500">{{ $book->type }} - {{ ucfirst($book->shift) }}</p>
                    </div>
                </div>
                <div class="text-right">
                    @if($book->status == 'draft')
                    <span
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                        Draft
                    </span>
                    @else
                    <span
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        Selesai
                    </span>
                    @endif
                </div>
            </div>

            <div class="space-y-2 text-xs mb-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Dibuat oleh:</span>
                    <span class="font-medium">{{ $book->creator->name ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="pt-2 border-t border-gray-100">
                <div class="flex space-x-2">
                    @if($book->status == 'draft')
                    <button @click="openContinueModal({{ json_encode($book) }})"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-3 rounded text-xs">
                        Lanjutkan
                    </button>
                    <button type="button" @click="prepareFinishModal({{ json_encode($book) }})"
                        class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 rounded text-xs">
                        Selesai
                    </button>
                    @else
                    <button
                        class="flex-1 bg-gray-400 text-white font-bold py-2 px-3 rounded text-xs cursor-not-allowed">
                        Selesai
                    </button>
                    @endif
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
            <p class="text-xs text-gray-400">Data manual book akan muncul di sini setelah dibuat</p>
        </div>
        @endforelse
    </div>

    {{-- Desktop: Table Layout --}}
    <div class="hidden lg:block bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-4 sm:px-6 py-4 sm:py-6 text-white">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-1">Buku Pemeriksaan Manual</h3>
                    <p class="text-blue-100 text-sm sm:text-base">Catatan aktivitas harian</p>
                </div>
                <button @click="openCreateModal()"
                    class="bg-white text-blue-600 hover:bg-blue-50 px-4 sm:px-6 py-2 sm:py-3 mt-3 sm:mt-0 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg text-sm sm:text-base">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 inline mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Data
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-5 py-2 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase">ID
                            Laporan</th>
                        <th class="px-3 sm:px-5 py-2 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase">
                            Tanggal</th>
                        <th class="px-3 sm:px-5 py-2 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase">
                            Pos Jaga</th>
                        <th class="px-3 sm:px-5 py-2 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase">
                            Shift</th>
                        <th class="px-3 sm:px-5 py-2 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase">
                            Dibuat Oleh</th>
                        <th class="px-3 sm:px-5 py-2 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase">
                            Status</th>
                        <th class="px-3 sm:px-5 py-2 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($manualBooks ?? [] as $book)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 sm:px-5 py-2 sm:py-4 text-blue-600 font-mono font-bold text-sm">{{ $book->id }}
                        </td>
                        <td class="px-3 sm:px-5 py-2 sm:py-4 whitespace-nowrap text-sm">{{
                            \Carbon\Carbon::parse($book->date)->format('d M Y') }}</td>
                        <td class="px-3 sm:px-5 py-2 sm:py-4 text-gray-600 uppercase text-sm">{{ $book->type }}</td>
                        <td class="px-3 sm:px-5 py-2 sm:py-4 text-gray-600 capitalize text-sm">{{ $book->shift }}</td>
                        <td class="px-3 sm:px-5 py-2 sm:py-4 text-gray-600 text-sm">{{ $book->creator->name ?? 'N/A' }}
                        </td>
                        <td class="px-3 sm:px-5 py-2 sm:py-4">
                            @if($book->status == 'draft')
                            <span
                                class="px-2 py-1 font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full text-xs">Draft</span>
                            @else
                            <span
                                class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full text-xs">Submitted</span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-5 py-2 sm:py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                @if($book->status == 'draft')
                                <button @click="openContinueModal({{ json_encode($book) }})"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1.5 sm:py-2 px-3 sm:px-4 rounded text-xs">Lanjutkan</button>
                                <button type="button" @click="prepareFinishModal({{ json_encode($book) }})"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-1.5 sm:py-2 px-3 sm:px-4 rounded text-xs">Selesai</button>
                                @else
                                <button
                                    class="bg-gray-400 text-white font-bold py-1.5 sm:py-2 px-3 sm:px-4 rounded text-xs cursor-not-allowed">Selesai</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 sm:py-12">
                            <p class="text-gray-500 text-base sm:text-lg">Belum ada data</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if(isset($manualBooks) && $manualBooks->hasPages())
            <div class="px-4 py-3 bg-white border-t border-gray-200">{{ $manualBooks->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Modal Tambah/Edit Data --}}
    <div x-show="openManualBook" x-transition
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4"
        style="display: none;">
        <div @click.away="openManualBook = false"
            class="bg-white w-full max-w-xs sm:max-w-2xl lg:max-w-4xl rounded-2xl shadow-2xl max-h-[90vh] flex flex-col">

            <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-4 sm:p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg sm:text-2xl font-bold"
                        x-text="isContinueMode ? 'Lanjutkan Input Laporan' : 'Buat Laporan Baru'">
                    </h2>
                    <button @click="openManualBook = false"
                        class="text-white hover:text-gray-200 text-2xl sm:text-3xl">&times;</button>
                </div>
            </div>

            <form :action="formAction" method="POST" class="p-3 sm:p-6 overflow-y-auto flex-1">
                @csrf
                <template x-if="isContinueMode"> @method('PATCH') </template>

                {{-- Input Header --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div>
                        <label for="date"
                            class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Tanggal</label>
                        <input type="date" name="date" x-model="form.date" required
                            class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 text-sm sm:text-base"
                            :readonly="isContinueMode">
                    </div>
                    <div>
                        <label for="shift"
                            class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Dinas/Shift</label>
                        <select name="shift" x-model="form.shift" required
                            class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 text-sm sm:text-base"
                            :disabled="isContinueMode">
                            <option value="">Pilih Shift</option>
                            <option value="pagi">Pagi</option>
                            <option value="siang">Siang</option>
                        </select>
                        <input x-if="isContinueMode" type="hidden" name="shift" :value="form.shift" />
                    </div>
                    <div>
                        <label for="type" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Pos
                            Jaga</label>
                        <select name="type" x-model="form.type" required
                            @change="if (form.type === 'hbscp') { form.rows.forEach(row => row.orang = '') }"
                            class="w-full border-2 border-gray-200 px-3 sm:px-4 py-2 sm:py-3 rounded-xl focus:border-blue-500 text-sm sm:text-base"
                            :disabled="isContinueMode">
                            <option value="">Pilih Pos</option>
                            <option value="hbscp">HBSCP</option>
                            <option value="pscp">PSCP</option>
                        </select>
                        <input x-if="isContinueMode" type="hidden" name="type" :value="form.type" />
                    </div>
                </div>

                {{-- Data yang Sudah Tersimpan --}}
                <div x-show="isContinueMode && existingDetails.length > 0" class="mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-2 border-b pb-2">Data yang Sudah
                        Tersimpan</h3>
                    <div class="max-h-32 sm:max-h-48 overflow-y-auto border rounded-lg">
                        <table class="min-w-full text-xs sm:text-sm">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="p-2 text-left">Jam</th>
                                    <th class="p-2 text-left">Petugas</th>
                                    <th class="p-2 text-left">Nama Pax</th>
                                    <th class="p-2 text-left">Flight</th>
                                    <th x-show="form.type === 'pscp'" class="p-2 text-left">Orang</th>
                                    <th class="p-2 text-left">Barang</th>
                                    <th class="p-2 text-left">Temuan</th>
                                    <th class="p-2 text-left">Ket</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <template x-for="detail in existingDetails" :key="detail.id">
                                    <tr class="border-t">
                                        <td class="p-2"
                                            x-text="detail.time ? detail.time.split(':').slice(0,2).join(':') : ''">
                                        </td>
                                        <td class="p-2" x-text="detail.name"></td>
                                        <td class="p-2" x-text="detail.pax"></td>
                                        <td class="p-2" x-text="detail.flight"></td>
                                        <td x-show="form.type === 'pscp'" class="p-2" x-text="detail.orang"></td>
                                        <td class="p-2" x-text="detail.barang"></td>
                                        <td class="p-2" x-text="detail.temuan"></td>
                                        <td class="p-2" x-text="detail.keterangan"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <h3 class="text-base sm:text-lg font-semibold text-gray-700 mt-3 sm:mt-4">Tambah Baris Baru</h3>
                <div class="overflow-x-auto border rounded-lg mt-2">
                    <table class="min-w-full text-xs sm:text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 sm:p-3 text-left">Jam</th>
                                <th class="p-2 sm:p-3 text-left">Petugas</th>
                                <th class="p-2 sm:p-3 text-left">Nama Pax</th>
                                <th class="p-2 sm:p-3 text-left">Flight</th>
                                <th x-show="form.type === 'pscp'" class="p-2 sm:p-3 text-left">Orang</th>
                                <th class="p-2 sm:p-3 text-left">Barang</th>
                                <th class="p-2 sm:p-3 text-left">Temuan</th>
                                <th class="p-2 sm:p-3 text-left">Ket</th>
                                <th class="p-2 sm:p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in form.rows" :key="index">
                                <tr class="border-t">
                                    <td><input type="time" :name="`rows[${index}][time]`" x-model="row.time"
                                            class="w-full p-1.5 sm:p-2 border-0 rounded text-xs sm:text-sm" required>
                                    </td>
                                    <td><input type="text" :name="`rows[${index}][name]`" x-model="row.name"
                                            class="w-full p-1.5 sm:p-2 border-0 bg-gray-100 focus:ring-0 cursor-default rounded text-xs sm:text-sm"
                                            required readonly></td>
                                    <td><input type="text" :name="`rows[${index}][pax]`" x-model="row.pax"
                                            class="w-16 sm:w-20 p-1.5 sm:p-2 border-0 rounded text-xs sm:text-sm"></td>
                                    <td><input type="text" :name="`rows[${index}][flight]`" x-model="row.flight"
                                            class="w-16 sm:w-20 p-1.5 sm:p-2 border-0 rounded text-xs sm:text-sm"></td>
                                    <td x-show="form.type === 'pscp'"><input type="text" :name="`rows[${index}][orang]`"
                                            x-model="row.orang"
                                            class="w-12 sm:w-16 p-1.5 sm:p-2 border-0 rounded text-xs sm:text-sm"
                                            min="0"></td>
                                    <td><input type="text" :name="`rows[${index}][barang]`" x-model="row.barang"
                                            class="w-12 sm:w-16 p-1.5 sm:p-2 border-0 rounded text-xs sm:text-sm"
                                            min="0"></td>
                                    <td><textarea :name="`rows[${index}][temuan]`" x-model="row.temuan"
                                            class="w-full p-1.5 sm:p-2 border-0 rounded text-xs sm:text-sm"
                                            rows="1"></textarea></td>
                                    <td><textarea :name="`rows[${index}][keterangan]`" x-model="row.keterangan"
                                            class="w-full p-1.5 sm:p-2 border-0 rounded text-xs sm:text-sm"
                                            rows="1"></textarea></td>
                                    <td class="p-2 text-center">
                                        <button type="button" @click="removeRow(index)"
                                            class="text-red-500 hover:text-red-700 disabled:opacity-50 text-lg sm:text-xl"
                                            :disabled="form.rows.length <= 1">&times;</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 sm:mt-4">
                    <button type="button" @click="addRow()"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-xs sm:text-sm font-medium">
                        + Tambah Baris
                    </button>
                </div>

                <div
                    class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-4 sm:mt-8 pt-4 sm:pt-6 border-t">
                    <button type="button" @click="openManualBook = false"
                        class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-medium text-sm sm:text-base">Batal</button>
                    <button type="submit"
                        class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 font-medium shadow-lg text-sm sm:text-base">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Konfirmasi Selesai --}}
    <div x-show="openFinishModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4"
        style="display: none;">

        <div @click.away="openFinishModal = false" class="bg-white w-full max-w-xs sm:max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-4 sm:p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg sm:text-2xl font-bold">Konfirmasi Selesai</h2>
                        <p class="text-blue-100 text-xs sm:text-sm">Konfirmasi penyelesaian Manual Book</p>
                    </div>
                    <button @click="openFinishModal = false"
                        class="text-white hover:text-gray-200 text-2xl">&times;</button>
                </div>
            </div>

            <form x-ref="finishForm" :action="finishFormAction" method="POST" class="p-3 sm:p-6">
                @csrf
                @method('PATCH')
                <div class="space-y-3 sm:space-y-4">
                    <p class="text-gray-700 text-sm sm:text-base">Apakah Anda yakin ingin menyelesaikan Manual Book ini?
                        Data tidak dapat ditambahkan lagi setelahnya.</p>

                    <div class="border-2 border-gray-200 rounded-xl p-3 sm:p-4">
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Tanda Tangan Digital
                            (Wajib)</label>
                        <div class="relative w-full h-32 sm:h-48 border border-gray-300 rounded-lg bg-gray-50">
                            <canvas x-ref="signatureCanvas" class="w-full h-full"></canvas>
                        </div>
                        <input type="hidden" name="signature" x-ref="signatureData">
                        <div class="text-right mt-2">
                            <button type="button" @click="clearSignature"
                                class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 transition-colors">Clear</button>
                        </div>
                    </div>

                    <div>
                        <label for="approvedID" class="block text-gray-700 font-bold text-xs sm:text-sm mb-2">Pilih
                            Supervisor Mengetahui:</label>
                        <select name="approvedID"
                            class="w-full border rounded px-2 sm:px-3 py-2 sm:py-3 text-xs sm:text-sm">
                            <option value="">Pilih Supervisor</option>
                            @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-4 sm:mt-6">
                        <button type="button" @click="openFinishModal = false"
                            class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-medium text-sm sm:text-base">Batal</button>
                        <button type="button" @click="submitFinishForm"
                            class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 font-medium shadow-lg text-sm sm:text-base">
                            Konfirmasi & Kirim
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('manualBook', () => ({
            openManualBook: false,
            isContinueMode: false,
            formAction: '{{ route('checklist.manualbook.store') }}',
            defaultUserName: '{{ $loggedInUserName ?? '' }}',
            existingDetails: [],
            form: { date: new Date().toISOString().slice(0, 10), shift: '', type: '', rows: [] },

            openFinishModal: false,
            finishFormAction: '',
            signaturePad: null,

            openCreateModal() {
                this.isContinueMode = false;
                this.existingDetails = [];
                this.formAction = '{{ route('checklist.manualbook.store') }}';
                this.form.date = new Date().toISOString().slice(0, 10);
                this.form.shift = '';
                this.form.type = '';
                this.form.rows = [{ time: '', name: this.defaultUserName, pax: '', flight: '', orang: '', barang: '', temuan: '', keterangan: '' }];
                this.openManualBook = true;
            },
            openContinueModal(book) {
                this.isContinueMode = true;
                this.existingDetails = book.details;
                this.formAction = `/checklist-manual-book/add-details/${book.id}`;
                this.form.date = book.date;
                this.form.shift = book.shift;
                this.form.type = book.type;
                this.form.rows = [{ time: '', name: this.defaultUserName, pax: '', flight: '', orang: '', barang: '', temuan: '', keterangan: '' }];
                this.openManualBook = true;
            },
            addRow() {
                this.form.rows.push({ time: '', name: this.defaultUserName, pax: '', flight: '', orang: '', barang: '', temuan: '', keterangan: '' });
            },
            removeRow(index) {
                if (this.form.rows.length > 1) {
                    this.form.rows.splice(index, 1);
                }
            },
            prepareFinishModal(book) {
                this.finishFormAction = `/checklist-manual-book/finish/${book.id}`;
                this.openFinishModal = true;
                this.$nextTick(() => {
                    const canvas = this.$refs.signatureCanvas;
                    if(canvas){
                        canvas.width = canvas.offsetWidth;
                        canvas.height = canvas.offsetHeight;
                        this.signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });
                    }
                });
            },
            clearSignature() {
                if (this.signaturePad) {
                    this.signaturePad.clear();
                    this.$refs.signatureData.value = '';
                }
            },
            submitFinishForm() {
                if (this.signaturePad && !this.signaturePad.isEmpty()) {
                    this.$refs.signatureData.value = this.signaturePad.toDataURL('image/png').split(',')[1];
                } else {
                    alert('Tanda tangan digital wajib diisi.');
                    return;
                }
                this.$refs.finishForm.submit();
            }
        }));
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
</style>
@endsection
