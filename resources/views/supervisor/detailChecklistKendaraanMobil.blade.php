@extends('layouts.app')

@section('title', 'Checklist Pengecekan Harian Mobil Patroli')

@section('content')
<div class="container mx-auto p-2 sm:p-4 max-w-6xl">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">

        {{-- Header --}}
        <div class="border-2 border-black">
            <div class="text-center py-2 sm:py-4 bg-gray-50">
                <h1 class="text-sm sm:text-lg font-bold uppercase mb-1 sm:mb-2 px-2">CHECK LIST PENGECEKAN HARIAN MOBIL
                    PATROLI</h1>
                <h2 class="text-xs sm:text-base font-bold uppercase px-2">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</h2>
            </div>
        </div>

        {{-- Main Table --}}
        <div class="border-2 border-t-0 border-black overflow-x-auto">
            <table class="w-full border-collapse lg:min-w-[800px]">
                <thead>
                    <tr class="bg-gray-100">
                        <th rowspan="2"
                            class="border border-black px-1 sm:px-2 py-2 text-xs sm:text-sm font-bold w-8 sm:w-12">NO
                        </th>
                        <th rowspan="2"
                            class="border border-black px-1 sm:px-2 py-2 text-xs sm:text-sm font-bold min-w-24 sm:w-40">
                            KETERANGAN</th>
                        <th class="border border-black px-1 sm:px-2 py-2 text-xs sm:text-sm font-bold text-center"
                            colspan="2">KONDISI SHIFT {{ ucfirst($checklist->shift) }}</th>
                    </tr>
                    <tr class="bg-gray-100">
                        <th class="border border-black px-1 sm:px-2 py-1 text-xs font-bold w-12 sm:w-16">BAIK</th>
                        <th class="border border-black px-1 sm:px-2 py-1 text-xs font-bold w-12 sm:w-16">TIDAK</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Mendefinisikan urutan dan nama kategori --}}
                    @php
                    $categories = [
                    'mesin' => ['A', 'MESIN'],
                    'mekanik' => ['B', 'MEKANIK'],
                    'lainlain' => ['C', 'LAIN-LAIN'],
                    ];
                    @endphp

                    {{-- Looping berdasarkan kategori yang sudah didefinisikan --}}
                    @foreach ($categories as $categoryKey => $categoryData)
                    {{-- Cek apakah ada item untuk kategori ini --}}
                    @if (isset($groupedItems[$categoryKey]))

                    {{-- Baris Judul Kategori --}}
                    <tr>
                        <td
                            class="border border-black px-1 sm:px-2 py-2 text-center text-xs sm:text-sm font-bold bg-gray-50">
                            {{ $categoryData[0] }}
                        </td>
                        <td
                            class="border border-black px-1 sm:px-2 py-2 text-xs sm:text-sm font-bold bg-gray-50 uppercase">
                            {{ $categoryData[1] }}
                        </td>
                        <td class="border border-black bg-gray-50"></td>
                        <td class="border border-black bg-gray-50"></td>
                    </tr>

                    {{-- Looping untuk setiap item dalam kategori ini --}}
                    @foreach ($groupedItems[$categoryKey] as $index => $detail)
                    <tr>
                        <td class="border border-black px-1 sm:px-2 py-2 text-center text-xs sm:text-sm">
                            {{ $index + 1 }}
                        </td>
                        <td class="border border-black px-1 sm:px-2 py-2 text-xs sm:text-sm pl-4">
                            {{-- Mengambil nama item dari relasi --}}
                            {{ $detail->item->name }}
                        </td>
                        <td class="border border-black px-1 sm:px-2 py-2 text-center">
                            {{-- Cek kondisi 'BAIK' --}}
                            @if ($detail->is_ok)
                            <span class="text-green-600 font-bold text-sm sm:text-base">✓</span>
                            @endif
                        </td>
                        <td class="border border-black px-1 sm:px-2 py-2 text-center">
                            {{-- Cek kondisi 'TIDAK' --}}
                            @if (!$detail->is_ok)
                            <span class="text-red-600 font-bold text-sm sm:text-base">✓</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Keterangan Section --}}
        <div class="border-2 border-t-0 border-black">
            <div class="p-4">
                <div class="mb-6">
                    <h3 class="font-bold text-sm mb-2">KETERANGAN :</h3>
                    <div class="space-y-1">
                        <div class="flex">
                            <span class="w-4 text-sm">1</span>
                            <div class="border-b border-dotted border-black flex-1 min-h-[20px]">
                                <span class="text-sm">Petugas dinas pagi melakukan pengecekan pada nomor A, B dan
                                    C</span>
                            </div>
                        </div>
                        <div class="flex">
                            <span class="w-4 text-sm">2</span>
                            <div class="border-b border-dotted border-black flex-1 min-h-[20px]">
                                <span class="text-sm">Petugas dinas pagi melakukan pengecekan pada nomor B dan C</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="font-bold text-sm mb-2">CATATAN :</h3>
                    <div class="space-y-1">
                        @for($i = 1; $i <= 2; $i++) <div class="flex">
                            <span class="w-4 text-sm">{{ $i }}</span>
                            <div class="border-b border-dotted border-black flex-1 min-h-[20px]">
                                @if(isset($checklist) && $checklist->notes && $i == 1)
                                <span class="text-sm">{{ $checklist->notes }}</span>
                                @endif
                            </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    {{-- Signature Section --}}
    <div class="border-2 border-t-0 border-black p-4">
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
                    <div class="border-b border-dotted border-black w-full h-1"></div>
                    @endif
                </div>
                <p class="text-sm font-semibold h-4">
                    ({{ $checklist->sender->name ?? '...' }})
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
                    <div class="border-b border-dotted border-black w-full h-1"></div>
                    @endif
                </div>
                <p class="text-sm font-semibold h-4">
                    ({{ $checklist->receiver->name ?? '...' }})
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
            <form action="{{ route('supervisor.checklist-kendaraan.signature', $checklist->id) }}" method="POST"
                onsubmit="return validateApproverSignature(event)">
                @csrf
                <div class="w-full max-w-sm mx-auto my-2 border-2 border-gray-300 rounded-lg bg-gray-50 p-2">
                    {{-- Canvas untuk TTD --}}
                    <canvas id="signature-canvas-approver" class="w-full h-28 rounded"></canvas>
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

{{-- Action Buttons --}}
@if(isset($checklist))
<div class="mt-4 sm:mt-6 text-center space-y-2 sm:space-y-0 sm:space-x-4 flex flex-col sm:flex-row justify-center">
    <a href="{{ route('supervisor.checklist-kendaraan.list') }}"
        class="bg-gray-600 hover:bg-gray-700 text-white px-4 sm:px-6 py-2 rounded-lg w-full sm:w-auto inline-block text-center">
        Kembali
    </a>
</div>
@endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Cari canvas approver di dalam dokumen
    const canvasApprover = document.getElementById('signature-canvas-approver');

    // Hanya jalankan script jika canvas-nya ada di halaman
    if (canvasApprover) {
        const signaturePadApprover = new SignaturePad(canvasApprover, {
            backgroundColor: 'rgb(249, 250, 251)', // Warna background canvas (abu-abu muda)
            penColor: 'rgb(0, 0, 0)' // Warna tinta
        });

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

        table {
            font-size: 10px !important;
        }

        th,
        td {
            padding: 2px 4px !important;
        }
    }

    @media (max-width: 640px) {
        .container {
            padding: 8px !important;
        }

        .min-w-\[800px\] {
            min-width: 750px;
        }

        .text-xs {
            font-size: 11px !important;
        }
    }

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
