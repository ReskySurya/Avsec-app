@extends('layouts.app')

@section('title', 'Detail Laporan Manual Book - ' . $manualBook->id)

@section('content')
<div class="container mx-auto p-3 sm:p-4 lg:p-8">
    {{-- Tombol Kembali --}}
    <div class="lg:pt-20 mb-4">
        <a href="{{ route('supervisor.checklist-manualbook.list') }}"
            class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="hidden sm:inline">Kembali ke Daftar</span>
            <span class="sm:hidden">Kembali</span>
        </a>
    </div>

    {{-- Bungkus Utama Form --}}
    <div class="bg-white shadow-lg rounded-lg border border-gray-200 overflow-hidden">

        {{-- Header Card --}}
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-200">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Detail Laporan Manual Book</h2>
            <p class="text-sm text-gray-600 mt-1">ID: {{ $manualBook->id }}</p>
        </div>

        {{-- Bagian Header Laporan --}}
        <div class="p-4 sm:p-6">
            {{-- Mobile: Stack layout, Desktop: Grid layout --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6 pb-4 border-b">
                <div class="bg-gray-50 p-3 rounded-md">
                    <dt class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">HARI/TANGGAL</dt>
                    <dd class="mt-1 text-sm sm:text-base text-gray-800 font-semibold">
                        {{ \Carbon\Carbon::parse($manualBook->date)->isoFormat('dddd, D MMMM YYYY') }}
                    </dd>
                </div>
                <div class="bg-gray-50 p-3 rounded-md">
                    <dt class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">DINAS/SHIFT</dt>
                    <dd class="mt-1 text-sm sm:text-base text-gray-800 font-semibold">
                        {{ ucfirst($manualBook->shift) }}
                    </dd>
                </div>
                <div class="bg-gray-50 p-3 rounded-md">
                    <dt class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">POS JAGA</dt>
                    <dd class="mt-1 text-sm sm:text-base text-gray-800 font-semibold">
                        {{ strtoupper($manualBook->type) }}
                    </dd>
                </div>
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th rowspan="2" class="border p-2 w-12 text-center">NO</th>
                            <th rowspan="2" class="border p-2">JAM</th>
                            <th rowspan="2" class="border p-2">NAMA PETUGAS</th>
                            <th colspan="4" class="border p-2 text-center">PEMERIKSAAN</th>
                            <th rowspan="2" class="border p-2">TEMUAN</th>
                            <th rowspan="2" class="border p-2">KET</th>
                        </tr>
                        <tr>
                            <th class="border p-2">PAX</th>
                            <th class="border p-2">FLIGHT</th>
                            <th class="border p-2">ORANG</th>
                            <th class="border p-2">BARANG</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($manualBook->details as $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="border p-2 text-center font-medium">{{ $loop->iteration }}</td>
                            <td class="border p-2 text-center font-mono">
                                {{ \Carbon\Carbon::parse($detail->time)->format('H:i') }}
                            </td>
                            <td class="border p-2 font-medium">{{ $detail->name }}</td>
                            <td class="border p-2">{{ $detail->pax }}</td>
                            <td class="border p-2">{{ $detail->flight }}</td>
                            <td class="border p-2 text-center">{{ $detail->orang }}</td>
                            <td class="border p-2 text-center">{{ $detail->barang }}</td>
                            <td class="border p-2">{{ $detail->temuan ?: '-' }}</td>
                            <td class="border p-2">{{ $detail->keterangan ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="p-6 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada data detail</p>
                                    <p class="text-sm">Belum ada data yang tercatat untuk laporan ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile/Tablet Card View --}}
            <div class="lg:hidden space-y-4">
                @forelse ($manualBook->details as $detail)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    {{-- Card Header --}}
                    <div class="bg-gray-50 px-4 py-3 rounded-t-lg border-b">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-800">
                                #{{ $loop->iteration }} - {{ $detail->name }}
                            </span>
                            <span class="text-sm font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                {{ \Carbon\Carbon::parse($detail->time)->format('H:i') }}
                            </span>
                        </div>
                    </div>

                    {{-- Card Content --}}
                    <div class="p-4">
                        {{-- Pemeriksaan Section --}}
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Pemeriksaan
                            </h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 p-2 rounded">
                                    <dt class="text-xs text-gray-600">PAX</dt>
                                    <dd class="text-sm font-semibold text-gray-800">{{ $detail->pax ?: '-' }}</dd>
                                </div>
                                <div class="bg-gray-50 p-2 rounded">
                                    <dt class="text-xs text-gray-600">FLIGHT</dt>
                                    <dd class="text-sm font-semibold text-gray-800">{{ $detail->flight ?: '-' }}</dd>
                                </div>
                                <div class="bg-gray-50 p-2 rounded">
                                    <dt class="text-xs text-gray-600">ORANG</dt>
                                    <dd class="text-sm font-semibold text-gray-800">{{ $detail->orang ?: '-' }}</dd>
                                </div>
                                <div class="bg-gray-50 p-2 rounded">
                                    <dt class="text-xs text-gray-600">BARANG</dt>
                                    <dd class="text-sm font-semibold text-gray-800">{{ $detail->barang ?: '-' }}</dd>
                                </div>
                            </div>
                        </div>

                        {{-- Temuan & Keterangan --}}
                        @if($detail->temuan || $detail->keterangan)
                        <div class="space-y-3">
                            @if($detail->temuan)
                            <div>
                                <dt class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Temuan</dt>
                                <dd
                                    class="mt-1 text-sm text-gray-800 bg-yellow-50 border-l-4 border-yellow-400 p-2 rounded">
                                    {{ $detail->temuan }}
                                </dd>
                            </div>
                            @endif

                            @if($detail->keterangan)
                            <div>
                                <dt class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Keterangan</dt>
                                <dd
                                    class="mt-1 text-sm text-gray-800 bg-blue-50 border-l-4 border-blue-400 p-2 rounded">
                                    {{ $detail->keterangan }}
                                </dd>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                {{-- Empty State untuk Mobile --}}
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada data detail</h3>
                    <p class="text-sm text-gray-600">Belum ada data yang tercatat untuk laporan ini.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Section Tanda Tangan --}}
        <div x-data="manualBook" x-init="initSupervisorPad()" class="border-t border-gray-200 p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                {{-- Tanda Tangan Petugas (Tidak Berubah) --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-2">
                        Tanda Tangan Petugas
                    </h3>
                    <div
                        class="border-2 border-dashed border-gray-300 rounded-lg p-4 min-h-[120px] flex flex-col justify-between">
                        @if($manualBook->senderSignature)
                        <div class="flex-1 flex items-center justify-center">
                            <img src="data:image/png;base64,{{ $manualBook->senderSignature }}"
                                alt="Tanda Tangan Petugas" class="max-h-20 max-w-full object-contain">
                        </div>
                        @else
                        <div class="flex-1 flex items-center justify-center">
                            <span class="text-gray-400 text-sm italic">Belum ditandatangani</span>
                        </div>
                        @endif
                        <div class="border-t pt-3 mt-3">
                            <div class="text-center">
                                <div class="text-sm font-semibold text-gray-800">{{ $manualBook->creator->name ??
                                    'Petugas' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-2">
                        Mengetahui {{ $manualBook->approver->name ?? 'Supervisor' }}
                    </h3>

                    {{-- Cek jika tanda tangan sudah ada --}}
                    @if($manualBook->approvedSignature)
                    {{-- Tampilkan gambar tanda tangan jika sudah ada --}}
                    <div
                        class="border-2 border-dashed border-gray-300 rounded-lg p-4 min-h-[120px] flex flex-col justify-between">
                        <div class="flex-1 flex items-center justify-center">
                            <img src="data:image/png;base64,{{ $manualBook->approvedSignature }}"
                                alt="Tanda Tangan Supervisor" class="max-h-20 max-w-full object-contain">
                        </div>
                        <div class="border-t pt-3 mt-3">
                            <div class="text-center">
                                <div class="text-sm font-semibold text-gray-800">{{ $manualBook->approver->name ??
                                    'Supervisor' }}</div>
                            </div>
                        </div>
                    </div>
                    @else
                    {{-- Jika belum ada, tampilkan form dengan canvas --}}
                    {{-- Hanya tampilkan form jika user yang login adalah approver yang ditunjuk --}}
                    @if(Auth::id() === $manualBook->approved_by)
                    <form x-ref="approvalForm" action="{{ route('supervisor.checklist-manualbook.signature', $manualBook->id) }}"
                        method="POST">
                        @csrf
                        @method('PATCH')
                        <div
                            class="border-2 border-dashed border-gray-300 rounded-lg p-4 bg-white flex flex-col justify-between">
                            {{-- Canvas untuk Tanda Tangan --}}
                            <div class="relative w-full h-48 border border-gray-300 rounded-lg bg-gray-50">
                                <canvas x-ref="supervisorCanvas" class="w-full h-full"></canvas>
                            </div>
                            <input type="hidden" name="approvedSignature" x-ref="supervisorSignatureData">

                            {{-- Tombol Aksi --}}
                            <div class="flex justify-between items-center mt-3">
                                <button type="button" @click="clearSupervisorSignature"
                                    class="text-sm text-blue-600 hover:text-blue-800">Clear</button>
                                <button type="button" @click="submitApprovalForm"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Simpan Tanda Tangan
                                </button>
                            </div>
                        </div>
                    </form>
                    @else
                    {{-- Tampilan jika user biasa yang melihat (bukan approver) --}}
                    <div
                        class="border-2 border-dashed border-gray-300 rounded-lg p-4 min-h-[120px] flex flex-col justify-between">
                        <div class="flex-1 flex items-center justify-center">
                            <span class="text-gray-400 text-sm italic">Menunggu persetujuan dari {{
                                $manualBook->approver->name ?? 'Supervisor' }}</span>
                        </div>
                        <div class="border-t pt-3 mt-3">
                            <div class="text-center">
                                <div class="text-sm font-semibold text-gray-800">{{ $manualBook->approver->name ??
                                    'Supervisor' }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer dengan informasi tambahan --}}
        <div class="bg-gray-50 px-4 py-3 sm:px-6 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center text-xs sm:text-sm text-gray-600">
                <span>
                    @if($manualBook->details->count() > 0)
                    Total {{ $manualBook->details->count() }} data tercatat
                    @else
                    Belum ada data tercatat
                    @endif
                </span>
                <span class="mt-1 sm:mt-0">Terakhir diperbarui: {{ $manualBook->updated_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('manualBook', () => ({
            supervisorSignaturePad: null,

            initSupervisorPad() {
                // Cek jika canvas ada di halaman
                if (this.$refs.supervisorCanvas) {
                    const canvas = this.$refs.supervisorCanvas;
                    canvas.width = canvas.offsetWidth;
                    canvas.height = canvas.offsetHeight;
                    this.supervisorSignaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)'
                    });
                }
            },

            clearSupervisorSignature() {
                if (this.supervisorSignaturePad) {
                    this.supervisorSignaturePad.clear();
                }
            },

            submitApprovalForm() {
                if (this.supervisorSignaturePad && !this.supervisorSignaturePad.isEmpty()) {
                    // Ambil data base64 dan masukkan ke input hidden
                    this.$refs.supervisorSignatureData.value = this.supervisorSignaturePad.toDataURL('image/png').split(',')[1];
                    // Submit form
                    this.$refs.approvalForm.submit();
                } else {
                    alert('Tanda tangan supervisor wajib diisi.');
                }
            }
        }));
    });
</script>

{{-- Custom CSS untuk fine-tuning --}}
<style>
    @media (max-width: 640px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }

    /* Smooth transitions */
    .transition-colors {
        transition-property: background-color, border-color, color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }

    /* Table responsive scroll */
    .overflow-x-auto {
        scrollbar-width: thin;
        scrollbar-color: #CBD5E0 #F7FAFC;
    }

    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #F7FAFC;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #CBD5E0;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #A0AEC0;
    }
</style>
@endsection