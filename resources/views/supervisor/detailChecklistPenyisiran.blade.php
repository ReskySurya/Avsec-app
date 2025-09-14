@extends('layouts.app')

@section('title', 'Checklist Pengecekan Harian Penyisiran Ruang Tunggu')

@section('content')
<div class="mx-auto p-2 sm:p-4 min-h-screen pt-5 sm:pt-20">
    {{-- Alert Sukses --}}
    @if(session('success'))
    <div
        class="bg-green-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg text-sm sm:text-base">
        {{ session('success') }}
    </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
    <div class="bg-red-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg text-sm sm:text-base">
        {{ session('error') }}
    </div>
    @endif
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200" id="printable-area">

        {{-- Header Section - Responsive --}}
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-3 sm:px-6 py-4 sm:py-6 text-white">
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                    class="w-12 h-12 sm:w-20 sm:h-20 order-first sm:order-none">
                <div class="text-center flex-1 px-2">
                    <h1 class="text-lg sm:text-2xl font-bold leading-tight">CHECK LIST PENGECEKAN HARIAN</h1>
                    <h2 class="text-base sm:text-xl font-semibold mt-1">CHECK LIST PENYISIRAN DAERAH STERIL RUANG TUNGGU
                    </h2>
                    <p class="text-blue-100 text-xs sm:text-sm mt-1">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
                </div>
                <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo"
                    class="w-16 h-16 sm:w-24 sm:h-24 order-last sm:order-none">
            </div>
        </div>

        <div class="p-3 sm:p-6 space-y-4 sm:space-y-6">
            {{-- Header Information - Mobile Responsive --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-6 bg-gray-50 p-3 sm:p-4 rounded-lg">
                <div class="space-y-1">
                    <p class="block text-xs sm:text-sm font-semibold text-gray-700">Hari/Tanggal:</p>
                    <p class="text-sm sm:text-base text-gray-900">{{ $checklist->date->format('d F Y') }}</p>
                </div>
                <div class="space-y-1">
                    <p class="block text-xs sm:text-sm font-semibold text-gray-700">Jam:</p>
                    <p class="text-sm sm:text-base text-gray-900">{{
                        \Carbon\Carbon::parse($checklist->time)->format('H:i') }}
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="block text-xs sm:text-sm font-semibold text-gray-700">Grup:</p>
                    <p class="text-sm sm:text-base text-gray-900">Grup {{ $checklist->grup }}</p>
                </div>
                <div class="space-y-1">
                    <p class="block text-xs sm:text-sm font-semibold text-gray-700">Nama Petugas:</p>
                    <p class="text-sm sm:text-base text-gray-900">{{ $checklist->sender->name ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Mobile: Card Layout --}}
            <div class="block lg:hidden space-y-4 mt-6">
                @php $no = 1; @endphp
                @forelse($groupedItems as $category => $details)
                <div class="bg-blue-100 p-3 rounded-lg font-bold text-blue-800 text-sm">
                    {{ strtoupper($category) }}
                </div>
                @foreach($details as $detail)
                <div class="border border-gray-300 rounded-lg p-4 bg-white shadow-sm">
                    <div class="font-medium text-gray-800 mb-3 text-sm">{{ $no++ }}. {{ $detail->item->name }}</div>

                    {{-- Temuan --}}
                    <div class="mb-2">
                        <label class="text-xs font-semibold text-gray-700">Temuan:</label>
                        <div class="flex space-x-6 mt-1">
                            <div class="flex items-center">
                                @if($detail->isfindings === true)
                                <span class="text-green-600 font-bold text-lg">✓</span>
                                <span class="ml-1 text-sm">Ya</span>
                                @elseif($detail->isfindings === false)
                                <span class="text-red-600 font-bold text-lg">✓</span>
                                <span class="ml-1 text-sm">Tidak</span>
                                @else
                                <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Kondisi --}}
                    <div class="mb-2">
                        <label class="text-xs font-semibold text-gray-700">Kondisi:</label>
                        <div class="flex space-x-6 mt-1">
                            <div class="flex items-center">
                                @if($detail->iscondition === true)
                                <span class="text-green-600 font-bold text-lg">✓</span>
                                <span class="ml-1 text-sm">Baik</span>
                                @elseif($detail->iscondition === false)
                                <span class="text-red-600 font-bold text-lg">✓</span>
                                <span class="ml-1 text-sm">Rusak</span>
                                @else
                                <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @empty
                <div class="text-center py-4 text-gray-500">Tidak ada data detail checklist.</div>
                @endforelse
            </div>

            {{-- Desktop: Table Layout --}}
            <div class="hidden lg:block mt-6 overflow-x-auto">
                <table class="w-full border-collapse border-2 border-gray-400">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold w-10 sm:w-12 text-sm"
                                rowspan="2">
                                NO</th>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold min-w-[150px] text-sm"
                                rowspan="2">
                                KETERANGAN</th>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold text-xs sm:text-sm"
                                colspan="2">
                                TEMUAN</th>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold text-xs sm:text-sm"
                                colspan="2">
                                KONDISI</th>
                        </tr>
                        <tr>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold w-10 sm:w-12 text-sm">
                                YA</th>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold w-10 sm:w-12 text-sm">
                                TIDAK</th>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold w-10 sm:w-12 text-sm">
                                BAIK</th>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold w-10 sm:w-12 text-sm">
                                RUSAK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($groupedItems as $category => $details)
                        <tr class="bg-blue-100">
                            <td colspan="6"
                                class="border-2 border-gray-400 px-3 sm:px-4 py-2 font-bold text-blue-800 text-left text-sm">
                                {{ strtoupper($category) }}
                            </td>
                        </tr>
                        @foreach($details as $detail)
                        <tr class="hover:bg-blue-50/50">
                            <td class="border-2 border-gray-400 p-2 text-center font-bold text-sm">{{ $no++ }}.</td>
                            <td class="border-2 border-gray-400 p-2 font-medium text-sm">{{ $detail->item->name }}</td>
                            <td class="border-2 border-gray-400 p-2 text-center">
                                @if($detail->isfindings === true)
                                <span class="text-green-600 font-bold text-lg">✓</span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="border-2 border-gray-400 p-2 text-center">
                                @if($detail->isfindings === false)
                                <span class="text-red-600 font-bold text-lg">✓</span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="border-2 border-gray-400 p-2 text-center">
                                @if($detail->iscondition === true)
                                <span class="text-green-600 font-bold text-lg">✓</span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="border-2 border-gray-400 p-2 text-center">
                                @if($detail->iscondition === false)
                                <span class="text-red-600 font-bold text-lg">✓</span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">Tidak ada data detail checklist.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Keterangan Section --}}
            <div>
                <div class="p-4">
                    <div class="mb-6">
                        <h3 class="font-bold text-sm mb-2">KETERANGAN :</h3>
                        <div class="space-y-1 text-sm">
                            <li>No. 1, 2 dan 3 di isi tanda centang pada kolom TEMUAN</li>
                            <li>No. 4 di isi tanda centang pada kolom KONDISI</li>
                            <li>Tulis hasil temuan pada kolom CATATAN</li>
                        </div>
                    </div>

                    {{-- CATATAN Section - Final Version --}}
                    <div class="mb-6">
                        <h3 class="font-bold text-sm mb-2">CATATAN :</h3>
                        <div class="space-y-1">
                            @php
                            // Kumpulkan semua notes dari checklist kendaraan details yang tidak kosong
                            $allNotes = [];

                            // Loop melalui semua detail checklist untuk mengambil notes
                            if(isset($checklist) && $checklist->details) {
                            foreach ($checklist->details as $detail) {
                            if (!empty($detail->notes) && !is_null($detail->notes)) {
                            $allNotes[] = [
                            'item_name' => $detail->item->name ?? 'Item tidak ditemukan',
                            'notes' => $detail->notes,
                            'category' => $detail->item->category ?? 'lainlain'
                            ];
                            }
                            }
                            }
                            @endphp

                            @forelse($allNotes as $index => $noteData)
                            <div class="flex">
                                <span class="w-4 text-sm">{{ $index + 1 }}</span>
                                <div class="border-b border-dotted border-black flex-1 min-h-[20px] flex items-end pb-1">
                                    <span class="text-sm">
                                        <strong>{{ $noteData['item_name'] }}:</strong> {{ $noteData['notes'] }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            {{-- Jika tidak ada notes, tampilkan satu baris kosong --}}
                            <div class="flex">
                                <span class="w-4 text-sm">1</span>
                                <div class="border-b border-dotted border-black flex-1 min-h-[20px]">
                                    {{-- Baris kosong --}}
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Signature Section --}}
        <div class=" p-4">
            {{-- Date Section --}}
            <div class="text-center mb-6">
                @php
                $bulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                @endphp
                <p class="text-sm">Yogyakarta,
                    @if(isset($checklist))
                    {{ $checklist->created_at->format('d') }} {{ $bulan[(int)$checklist->created_at->format('n')] }} {{
                    $checklist->created_at->format('Y') }}
                    @else
                    {{ date('d') }} {{ $bulan[(int)date('n')] }} {{ date('Y') }}
                    @endif
                </p>
            </div>

            {{-- Signature Grid Layout (Sender & Receiver) --}}
            <div class="grid grid-cols-2 gap-4 text-center">
                {{-- Kolom Kiri: Yang Menyerahkan (Sender) --}}
                <div>
                    <p class="text-sm font-semibold">Yang Menyerahkan</p>
                    <div class="w-48 mx-auto my-2 h-28 flex flex-col items-center justify-center">
                        @if($checklist->senderSignature)
                        <img src="data:image/png;base64,{{ $checklist->senderSignature }}" alt="TTD Yang Menyerahkan"
                            class="max-h-24 max-w-full object-contain">
                        @else
                         <div
                                class="mx-auto mt-2 h-16 sm:h-24 w-24 sm:w-32 border rounded flex items-center justify-center text-xs sm:text-sm text-gray-500">
                                Menunggu TTD</div>
                        @endif
                    </div>
                    <p class="text-sm font-semibold h-4">
                        ({{ $checklist->sender->name ?? 'Belum ada Pengirim' }})
                    </p>
                    <p class="text-xs text-gray-600">
                        Petugas Dinas {{ ucfirst($checklist->shift) }}
                    </p>
                </div>

                {{-- Kolom Kanan: Yang Menerima (Receiver) --}}
                <div>
                    <p class="text-sm font-semibold">Yang Menerima</p>
                    <div class="w-48 mx-auto my-2 h-28 flex flex-col items-center justify-center">
                        @if($checklist->receivedSignature)
                        <img src="data:image/png;base64,{{ $checklist->receivedSignature }}" alt="TTD Yang Menerima"
                            class="max-h-24 max-w-full object-contain">
                        @else
                         <div
                                class="mx-auto mt-2 h-16 sm:h-24 w-24 sm:w-32 border rounded flex items-center justify-center text-xs sm:text-sm text-gray-500">
                                Menunggu TTD</div>
                        @endif
                    </div>
                    <p class="text-sm font-semibold h-4">
                        ({{ $checklist->receiver->name ?? 'Belum ada Penerima' }})
                    </p>
                    <p class="text-xs text-gray-600">
                        Petugas Dinas {{ $checklist->shift == 'pagi' ? 'Malam' : 'Pagi' }}
                    </p>
                </div>
            </div>

            <div class="mt-8 text-center">
                <p class="text-sm font-semibold">Mengetahui,</p>

                {{-- Cek apakah sudah ada tanda tangan approver --}}
                @if($checklist->approvedSignature)
                {{-- JIKA SUDAH: Tampilkan gambar tanda tangan dan nama --}}
                <div class="w-48 mx-auto my-2 h-28 flex flex-col items-center justify-center">
                    <img src="data:image/png;base64,{{ $checklist->approvedSignature }}" alt="TTD Mengetahui"
                        class="max-h-24 max-w-full object-contain">
                </div>
                <p class="text-sm font-semibold h-4">
                    ({{ $checklist->approver->name ?? '...' }})
                </p>
                @else
                {{-- JIKA BELUM: Tampilkan form dengan canvas untuk tanda tangan --}}
                <form action="{{ route('supervisor.checklist-penyisiran.signature', $checklist->id) }}" method="POST"
                    onsubmit="return validateApproverSignature(event)">
                    @csrf
                    <div class="w-48 mx-auto mb-2 h-28 flex flex-col items-center justify-center border rounded-lg bg-gray-50">
                        {{-- Canvas untuk TTD --}}
                        <canvas id="signature-canvas-approver" class="w-full h-full"></canvas>
                    </div>

                    {{-- Hidden input untuk menyimpan data base64 ttd --}}
                    <input type="hidden" name="approvedSignature" id="signature-data-approver">

                    <div class="flex items-center justify-center gap-4 mt-2">
                        <button type="button" onclick="clearSignatureApprover()"
                            class="text-sm text-blue-600 hover:text-blue-800">
                            Clear
                        </button>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-2 rounded-lg">
                            Simpan & Setujui
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>


{{-- Action Buttons (if needed for editing/printing) --}}
@if(isset($checklist))
<div class="mt-4 sm:mt-6 text-center space-y-2 sm:space-y-0 sm:space-x-4 flex flex-col sm:flex-row justify-center">
    <a href="{{ route('supervisor.checklist-penyisiran.list') }}"
        class="bg-gray-600 hover:bg-gray-700 text-white px-4 sm:px-6 py-2 rounded-lg w-full sm:w-auto inline-block text-center">
        Kembali ke Daftar
    </a>
</div>
@endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cari canvas approver di dalam dokumen
        const canvasApprover = document.getElementById('signature-canvas-approver');

        // Hanya jalankan script jika canvas-nya ada di halaman
        if (canvasApprover) {
            const signaturePadApprover = new SignaturePad(canvasApprover, {
                backgroundColor: 'rgb(249, 250, 251)', // Warna background canvas (abu-abu muda)
                penColor: 'rgb(0, 0, 0)' // Warna tinta
            });

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvasApprover.width = canvasApprover.offsetWidth * ratio;
                canvasApprover.height = canvasApprover.offsetHeight * ratio;
                canvasApprover.getContext("2d").scale(ratio, ratio);
                signaturePadApprover.clear(); // Hapus tanda tangan setelah resize
            }

            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();

            // Simpan data ttd ke hidden input setiap kali selesai menulis
            signaturePadApprover.onEnd = () => {
                const signatureDataInput = document.getElementById('signature-data-approver');
                if (!signaturePadApprover.isEmpty()) {
                    signatureDataInput.value = signaturePadApprover.toDataURL('image/png');
                } else {
                    signatureDataInput.value = '';
                }
            };

            // Fungsi untuk membersihkan canvas
            window.clearSignatureApprover = function() {
                signaturePadApprover.clear();
                document.getElementById('signature-data-approver').value = '';
            }

            // Fungsi untuk validasi sebelum submit
            window.validateApproverSignature = function(event) {
                if (signaturePadApprover.isEmpty()) {
                    alert("Tanda tangan persetujuan tidak boleh kosong.");
                    event.preventDefault(); // Mencegah form dikirim
                    return false;
                }
                return true;
            }
        }
    });
</script>

{{-- Print Styles --}}
<style>
    @media print {
        .container {
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .bg-gray-50,
        .bg-gray-100 {
            background-color: #f9f9f9 !important;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }

        .border-black {
            border-color: #000 !important;
        }

        button,
        .bg-blue-600,
        .bg-green-600,
        .bg-gray-600 {
            display: none !important;
        }

        .text-xs {
            font-size: 10px !important;
        }

        .text-sm {
            font-size: 12px !important;
        }

        /* Ensure table fits on page */
        table {
            font-size: 10px !important;
        }

        th,
        td {
            padding: 2px 4px !important;
        }
    }

    /* Mobile specific styles */
    @media (max-width: 640px) {
        .container {
            padding: 8px !important;
        }

        /* Make sure table doesn't overflow on very small screens */
        .min-w-\[640px\] {
            min-width: 600px;
        }

        /* Adjust signature section for mobile */
        .signature-mobile {
            flex-direction: column;
        }

        /* Ensure text is readable on mobile */
        .text-xs {
            font-size: 11px !important;
        }
    }

    /* Landscape mobile optimization */
    @media (max-width: 768px) and (orientation: landscape) {
        .container {
            padding: 4px !important;
        }

        h1,
        h2 {
            line-height: 1.2;
        }
    }
</style>

@endsection