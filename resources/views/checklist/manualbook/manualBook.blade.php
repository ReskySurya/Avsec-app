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

    // State BARU untuk modal konfirmasi
    openFinishModal: false,
    finishFormAction: '',
    signaturePad: null,

    // Fungsi untuk modal utama
    openCreateModal() { /* ... (Tidak berubah) ... */ },
    openContinueModal(book) { /* ... (Tidak berubah) ... */ },
    addRow() { /* ... (Tidak berubah) ... */ },
    removeRow(index) { /* ... (Tidak berubah) ... */ },

    // Fungsi BARU untuk modal konfirmasi
    prepareFinishModal(book) {
        this.finishFormAction = `/checklist/manual-book/${book.id}/finish`;
        this.openFinishModal = true;
        // Inisialisasi signature pad setelah modal muncul
        this.$nextTick(() => {
            const canvas = this.$refs.signatureCanvas;
            // Atur ukuran canvas agar sesuai dengan displaynya
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            this.signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)'
            });
        });
    },
    clearSignature() {
        if (this.signaturePad) {
            this.signaturePad.clear();
            this.$refs.signatureData.value = '';
        }
    },
    submitFinishForm() {
        // Cek apakah ada tanda tangan
        if (this.signaturePad && !this.signaturePad.isEmpty()) {
            // Simpan data base64 dari tanda tangan ke input hidden
            this.$refs.signatureData.value = this.signaturePad.toDataURL('image/png');
        } else {
            alert('Tanda tangan digital wajib diisi.');
            return; // Hentikan proses jika tidak ada ttd
        }

        // Submit form secara manual
        this.$refs.finishForm.submit();
    }

        }" x-init="
            openCreateModal = () => { /* ... (definisi lengkap) ... */ };
            openContinueModal = (book) => { /* ... (definisi lengkap) ... */ };
            addRow = () => { /* ... (definisi lengkap) ... */ };
            removeRow = (index) => { /* ... (definisi lengkap) ... */ };
        " class="mx-auto p-0 sm:p-6 min-h-screen pt-5 lg:pt-20">

    <div x-data="manualBook">
        {{-- Alert Messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
            class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg">{{
            session('success') }}</div>
        @endif
        @if($errors->any())
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
            class="bg-red-500 text-white p-4 rounded-lg mb-6">
            <strong>Whoops!</strong> Ada beberapa masalah dengan input Anda.<br><br>
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
        @endif

        {{-- Header & Tombol Tambah --}}
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
            <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-6 text-white">
                <div class="flex flex-col sm:flex-row justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold mb-1">Buku Pemeriksaan Manual</h3>
                        <p class="text-blue-100">Catatan aktivitas harian</p>
                    </div>
                    <button @click="openCreateModal()"
                        class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 mt-3 sm:mt-0 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Tambah Data
                    </button>
                </div>
            </div>

            {{-- Tabel Utama --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">ID Laporan
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Pos Jaga</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Shift</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Dibuat Oleh
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($manualBooks ?? [] as $book)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 text-blue-600 font-mono font-bold">{{ $book->id }}</td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($book->date)->format('d M
                                Y') }}</td>
                            <td class="px-5 py-4 text-gray-600 uppercase">{{ $book->type }}</td>
                            <td class="px-5 py-4 text-gray-600 capitalize">{{ $book->shift }}</td>
                            <td class="px-5 py-4 text-gray-600">{{ $book->creator->name ?? 'N/A' }}</td>
                            <td class="px-5 py-4">
                                @if($book->status == 'draft')
                                <span
                                    class="px-2 py-1 font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full">Draft</span>
                                @else
                                <span
                                    class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Submitted</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    @if($book->status == 'draft')
                                    <button @click="openContinueModal({{ json_encode($book) }})"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-xs">Lanjutkan</button>
                                    {{-- Tombol "Selesai" diubah untuk memanggil modal --}}
                                    <button type="button" @click="prepareFinishModal({{ json_encode($book) }})"
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-xs">Selesai</button>
                                    @else
                                    <button
                                        class="bg-gray-400 text-white font-bold py-2 px-4 rounded text-xs cursor-not-allowed">Selesai</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <p class="text-gray-500 text-lg">Belum ada data</p>
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

        <div x-show="openManualBook" x-transition
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            style="display: none;">
            <div @click.away="openManualBook = false"
                class="bg-white w-full max-w-6xl rounded-2xl shadow-2xl max-h-[90vh] flex flex-col">

                <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                    <h2 class="text-2xl font-bold"
                        x-text="isContinueMode ? 'Lanjutkan Input Laporan' : 'Buat Laporan Baru'">
                    </h2>
                </div>

                <form :action="formAction" method="POST" class="p-6 overflow-y-auto">
                    @csrf
                    <template x-if="isContinueMode"> @method('PATCH') </template>

                    {{-- Input Header --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                            <input type="date" name="date" x-model="form.date" required
                                class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500"
                                :readonly="isContinueMode">
                        </div>
                        <div>
                            <label for="shift"
                                class="block text-sm font-semibold text-gray-700 mb-2">Dinas/Shift</label>
                            <select name="shift" x-model="form.shift" required
                                class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500"
                                :disabled="isContinueMode">
                                <option value="">Pilih Shift</option>
                                <option value="pagi">Pagi</option>
                                <option value="siang">Siang</option>
                            </select>
                            <input x-if="isContinueMode" type="hidden" name="shift" :value="form.shift" />
                        </div>
                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Pos Jaga</label>
                            <select name="type" x-model="form.type" required
                                @change="if (form.type === 'hbscp') { form.rows.forEach(row => row.orang = '') }"
                                class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500"
                                :disabled="isContinueMode">
                                <option value="">Pilih Pos</option>
                                <option value="hbscp">HBSCP</option>
                                <option value="pscp">PSCP</option>
                            </select>
                            <input x-if="isContinueMode" type="hidden" name="type" :value="form.type" />
                        </div>
                    </div>

                    {{-- ===== BAGIAN BARU: MENAMPILKAN DATA LAMA ===== --}}
                    <div x-show="isContinueMode && existingDetails.length > 0" class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2 border-b pb-2">Data yang Sudah Tersimpan
                        </h3>
                        <div class="max-h-48 overflow-y-auto border rounded-lg">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="p-2 text-left">Jam</th>
                                        <th class="p-2 text-left">Nama Petugas</th>
                                        <th class="p-2 text-left">Pax</th>
                                        <th class="p-2 text-left">Flight</th>
                                        <th x-show="form.type === 'pscp'" class="p-2 text-left">Orang</th>
                                        <th class="p-2 text-left">Barang</th>
                                        <th class="p-2 text-left">Temuan</th>
                                        <th class="p-2 text-center">keterangan</th>
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

                    <h3 class="text-lg font-semibold text-gray-700 mt-4">Tambah Baris Baru</h3>
                    <div class="overflow-x-auto border rounded-lg mt-2">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-3 text-left">Jam</th>
                                    <th class="p-3 text-left">Nama Petugas</th>
                                    <th class="p-3 text-left">Pax</th>
                                    <th class="p-3 text-left">Flight</th>
                                    <th x-show="form.type === 'pscp'" class="p-3 text-left">Orang</th>
                                    <th class="p-3 text-left">Barang</th>
                                    <th class="p-3 text-left">Temuan</th>
                                    <th class="p-3 text-left">Ket</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in form.rows" :key="index">
                                    <tr class="border-t">
                                        <td><input type="time" :name="`rows[${index}][time]`" x-model="row.time"
                                                class="w-full p-2 border-0 rounded" required></td>
                                        <td><input type="text" :name="`rows[${index}][name]`" x-model="row.name"
                                                class="w-full p-2 border-0 bg-gray-100 focus:ring-0 cursor-default rounded"
                                                required readonly></td>
                                        <td><input type="text" :name="`rows[${index}][pax]`" x-model="row.pax"
                                                class="w-24 p-2 border-0 rounded"></td>
                                        <td><input type="text" :name="`rows[${index}][flight]`" x-model="row.flight"
                                                class="w-24 p-2 border-0 rounded"></td>
                                        <td x-show="form.type === 'pscp'"><input type="text"
                                                :name="`rows[${index}][orang]`" x-model="row.orang"
                                                class="w-20 p-2 border-0 rounded" min="0"></td>
                                        <td><input type="text" :name="`rows[${index}][barang]`" x-model="row.barang"
                                                class="w-20 p-2 border-0 rounded" min="0"></td>
                                        <td><textarea :name="`rows[${index}][temuan]`" x-model="row.temuan"
                                                class="w-full p-2 border-0 rounded" rows="1"></textarea></td>
                                        <td><textarea :name="`rows[${index}][keterangan]`" x-model="row.keterangan"
                                                class="w-full p-2 border-0 rounded" rows="1"></textarea></td>
                                        <td class="p-2 text-center">
                                            <button type="button" @click="removeRow(index)"
                                                class="text-red-500 hover:text-red-700 disabled:opacity-50"
                                                :disabled="form.rows.length <= 1">&times;</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <button type="button" @click="addRow()"
                            class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-sm font-medium">+
                            Tambah Baris</button>
                    </div>

                    <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                        <button type="button" @click="openManualBook = false"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-medium">Batal</button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 font-medium shadow-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openFinishModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            style="display: none;">

            <div @click.away="openFinishModal = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
                <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-6 rounded-t-2xl">
                    <h2 class="text-2xl font-bold">Konfirmasi Selesai</h2>
                    <p class="text-blue-100">Konfirmasi penyelesaian Catatan Manual Book ini</p>
                </div>

                {{-- Form untuk Konfirmasi --}}
                <form x-ref="finishForm" :action="finishFormAction" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="p-6">
                        <p class="text-gray-700 mb-4">Apakah Anda yakin ingin menyelesaikan Manual Book ini? Data tidak
                            dapat ditambahkan lagi setelahnya.</p>
                        <div class="border-2 border-gray-200 rounded-xl p-4 mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan Digital
                                (Wajib)</label>
                            <div class="relative w-full h-48 border border-gray-300 rounded-lg bg-gray-50">
                                <canvas x-ref="signatureCanvas" class="w-full h-full"></canvas>
                            </div>
                            <input type="hidden" name="signature" x-ref="signatureData">
                            <div class="text-right mt-2">
                                <button type="button" @click="clearSignature"
                                    class="text-sm text-blue-600 hover:text-blue-800 transition-colors">Clear</button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="approvedID" class="block text-gray-700 font-bold text-sm mb-2">Pilih Supervisor
                                Mengetahui:</label>
                            <select name="approvedID" class="w-full border rounded px-2 py-1 text-sm" required>
                                <option value="">Pilih Supervisor</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" @click="openFinishModal = false"
                                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-medium">Batal</button>
                            <button type="button" @click="submitFinishForm"
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-600 text-white rounded-xl hover:from-blue-600 font-medium shadow-lg">
                                Konfirmasi & Kirim
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
@endsection
