@extends('layouts.app')

@section('title', 'Checklist Pengecekan Harian Motor Patroli')

@section('content')
<div class="mx-auto p-4 sm:p-6 min-h-screen pt-5 sm:pt-20">
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-6 py-6 text-white">
            <div class="flex items-center justify-between">
                <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                    class="w-20 h-20 mb-2 sm:mb-0">
                <div class="text-center">
                    <h1 class="text-2xl font-bold">CHECK LIST PENGECEKAN HARIAN</h1>
                    <h2 class="text-xl font-semibold">KENDARAAN MOTOR PATROLI</h2>
                    <p class="text-blue-100">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
                </div>
                <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo" class="w-24 h-24 mt-2 sm:mt-0">
            </div>
        </div>

        {{-- Main Table --}}
        <div class="border-2 border-t-0 border-black overflow-x-auto">
            <table class="w-full border-collapse min-w-[640px]">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-black px-1 sm:px-2 py-2 text-xs sm:text-sm font-bold w-8 sm:w-12">
                            NO
                        </th>
                        <th
                            class="border border-black px-1 sm:px-2 py-2 text-xs sm:text-sm font-bold min-w-[200px] sm:w-64">
                            PEMERIKSAAN KONDISI KENDARAAN
                        </th>
                        {{-- Judul kolom diubah menjadi dinamis berdasarkan nilai shift --}}
                        <th class="border border-black px-1 sm:px-2 py-2 text-xs sm:text-sm font-bold text-center"
                            colspan="2">
                            KONDISI SHIFT {{ strtoupper($checklist->shift) }}
                        </th>
                    </tr>
                    <tr class="bg-gray-100">
                        <th class="border border-black px-1 sm:px-2 py-1 text-xs"></th>
                        <th class="border border-black px-1 sm:px-2 py-1 text-xs"></th>
                        <th class="border border-black px-1 sm:px-2 py-1 text-xs font-bold w-12 sm:w-16">BAIK</th>
                        <th class="border border-black px-1 sm:px-2 py-1 text-xs font-bold w-12 sm:w-16">TIDAK</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($checklist->details ?? [] as $index => $detail)
                    <tr>
                        <td class="border border-black px-1 sm:px-2 py-2 text-center text-xs sm:text-sm">
                            {{ $index + 1 }}
                        </td>
                        <td class="border border-black px-1 sm:px-2 py-2 text-xs sm:text-sm">
                            {{ $detail->item->name ?? 'Item tidak ditemukan' }}
                        </td>
                        {{-- Kondisi pengecekan shift dihilangkan karena sudah tidak relevan --}}
                        <td class="border border-black px-1 sm:px-2 py-2 text-center">
                            @if($detail->is_ok)
                            <span class="text-green-600 font-bold text-sm sm:text-base">✓</span>
                            @endif
                        </td>
                        <td class="border border-black px-1 sm:px-2 py-2 text-center">
                            @if(!$detail->is_ok)
                            <span class="text-red-600 font-bold text-sm sm:text-base">✓</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" {{-- Colspan disesuaikan menjadi 4 --}}
                            class="border border-black px-1 sm:px-2 py-4 text-center text-gray-500 text-xs sm:text-sm">
                            Tidak ada item pengecekan ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Keterangan Section --}}
        <div class="border-2 border-t-0 border-black">
            <div class="p-4">
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

    <form action="{{ route('checklist.receivedSignature', $checklist->id) }}" method="POST">
        @csrf

        <div class="border-2 border-t-0 border-black">
            <div class="flex">

                {{-- Kolom Kiri (Petugas Dinas Pagi) --}}
                <div class="flex-1 text-center p-6">
                    <div class="mb-2">
                        @php
                        $bulan = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        @endphp
                        <p class="text-sm">Yogyakarta,
                            @if(isset($checklist))
                            {{ $checklist->created_at->format('d') }} {{
                            $bulan[(int)$checklist->created_at->format('n')] }} {{ $checklist->created_at->format('Y')
                            }}
                            @else
                            {{ date('d') }} {{ $bulan[(int)date('n')] }} {{ date('Y') }}
                            @endif
                        </p>
                        <p class="text-sm font-semibold">Petugas Dinas Pagi</p>
                    </div>
                    <div class="w-48 mx-auto mb-2 h-28 flex flex-col items-center justify-center">
                        {{-- Logika untuk menampilkan TTD atau Canvas --}}
                        @if($checklist->shift == 'pagi' && $checklist->senderSignature)
                        <img src="data:image/png;base64,{{ $checklist->senderSignature }}" alt="TTD Petugas Pagi"
                            class="max-h-24 max-w-full object-contain">
                        @elseif($checklist->shift == 'malam' && $checklist->receivedSignature)
                        <img src="data:image/png;base64,{{ $checklist->receivedSignature }}" alt="TTD Petugas Pagi"
                            class="max-h-24 max-w-full object-contain">
                        @elseif($checklist->shift == 'malam' && !$checklist->receivedSignature)
                        {{-- Canvas untuk Petugas Pagi (sebagai Penerima) --}}
                        <div class="w-full h-full border border-gray-300 rounded-md bg-gray-50">
                            <canvas id="signature-canvas" class="w-full h-full"></canvas>
                        </div>
                        <button type="button" id="clear-signature"
                            class="text-xs text-blue-600 hover:text-blue-800 mt-1">Clear</button>
                        @else
                        <div class="border-b border-dotted border-black w-full h-1"></div>
                        @endif
                    </div>
                    <p class="text-xs h-4">
                        {{-- Menampilkan nama sesuai kondisi --}}
                        @if($checklist->shift == 'pagi')
                        {{ $checklist->sender->name ?? '' }}
                        @elseif($checklist->shift == 'malam' && $checklist->receiver)
                        {{-- Ganti 'receiver' dengan relasi yang sesuai jika ada --}}
                        {{ $checklist->receiver->name ?? '' }}
                        @endif
                    </p>
                </div>

                {{-- Kolom Kanan (Petugas Dinas Malam) --}}
                <div class="flex-1 text-center p-6">
                    <div class="mb-16">
                        <p class="text-sm font-semibold">Petugas Dinas Malam</p>
                    </div>
                    <div class="w-48 mx-auto mb-2 h-28 flex flex-col items-center justify-center">
                        {{-- Logika untuk menampilkan TTD atau Canvas --}}
                        @if($checklist->shift == 'malam' && $checklist->senderSignature)
                        <img src="data:image/png;base64,{{ $checklist->senderSignature }}" alt="TTD Petugas Malam"
                            class="max-h-24 max-w-full object-contain">
                        @elseif($checklist->shift == 'pagi' && $checklist->receivedSignature)
                        <img src="data:image/png;base64,{{ $checklist->receivedSignature }}" alt="TTD Petugas Malam"
                            class="max-h-24 max-w-full object-contain">
                        @elseif($checklist->shift == 'pagi' && !$checklist->receivedSignature)
                        {{-- Canvas untuk Petugas Malam (sebagai Penerima) --}}
                        <div class="w-full h-full border border-gray-300 rounded-md bg-gray-50">
                            <canvas id="signature-canvas" class="w-full h-full"></canvas>
                        </div>
                        <button type="button" id="clear-signature"
                            class="text-xs text-blue-600 hover:text-blue-800 mt-1">Clear</button>
                        @else
                        <div class="border-b border-dotted border-black w-full h-1"></div>
                        @endif
                    </div>
                    <p class="text-xs h-4">
                        {{-- Menampilkan nama sesuai kondisi --}}
                        @if($checklist->shift == 'malam')
                        {{ $checklist->sender->name ?? '' }}
                        @elseif($checklist->shift == 'pagi' && $checklist->receiver)
                        {{-- Ganti 'receiver' dengan relasi yang sesuai jika ada --}}
                        {{ $checklist->receiver->name ?? '' }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Tombol Simpan hanya muncul jika TTD kedua belum ada --}}
        @if(!$checklist->receivedSignature)
        <div class="p-4 text-center border-2 border-t-0 border-black">
            <input type="hidden" name="receivedSignature" id="receivedSignature">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded-lg">
                Simpan Tanda Tangan Penerima
            </button>
        </div>
        @endif
    </form>
</div>

{{-- Action Buttons (if needed for editing/printing) --}}
@if(isset($checklist))
<div class="mt-4 sm:mt-6 text-center space-y-2 sm:space-y-0 sm:space-x-4 flex flex-col sm:flex-row justify-center">
    <a href="{{ route('dashboard.officer') }}"
        class="bg-gray-600 hover:bg-gray-700 text-white px-4 sm:px-6 py-2 rounded-lg w-full sm:w-auto inline-block text-center">
        Kembali
    </a>
</div>
@endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('signature-canvas');

    // Hanya jalankan skrip jika canvas ada di halaman
    if (canvas) {
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(249, 250, 251)',
            penColor: 'rgb(0, 0, 0)'
        });

        const clearButton = document.getElementById('clear-signature');
        const hiddenInput = document.getElementById('receivedSignature');
        const form = document.querySelector('form');

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
            hiddenInput.value = '';
        }

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        clearButton.addEventListener('click', function () {
            signaturePad.clear();
            hiddenInput.value = '';
        });

        signaturePad.onEnd = () => {
            if (!signaturePad.isEmpty()) {
                hiddenInput.value = signaturePad.toDataURL('image/png');
            } else {
                hiddenInput.value = '';
            }
        };

        form.addEventListener('submit', function (event) {
            if (signaturePad.isEmpty()) {
                alert("Harap berikan tanda tangan terlebih dahulu.");
                event.preventDefault(); // Mencegah form dikirim
            }
        });
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
