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
                        <th rowspan="2" class="border border-black px-1 sm:px-2 py-2 text-xs sm:text-sm font-bold w-8 sm:w-12">NO
                        </th>
                        <th  rowspan="2" class="border border-black px-1 sm:px-2 py-2 text-xs sm:text-sm font-bold min-w-24 sm:w-40">
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

    {{-- Signature Section (keeping existing logic) --}}
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
                        {{-- Existing signature logic --}}
                        @if($checklist->shift == 'pagi' && $checklist->senderSignature)
                        <img src="data:image/png;base64,{{ $checklist->senderSignature }}" alt="TTD Petugas Pagi"
                            class="max-h-24 max-w-full object-contain">
                        @elseif($checklist->shift == 'malam' && $checklist->receivedSignature)
                        <img src="data:image/png;base64,{{ $checklist->receivedSignature }}" alt="TTD Petugas Pagi"
                            class="max-h-24 max-w-full object-contain">
                        @elseif($checklist->shift == 'malam' && !$checklist->receivedSignature)
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
                        @if($checklist->shift == 'pagi')
                        {{ $checklist->sender->name ?? '' }}
                        @elseif($checklist->shift == 'malam' && $checklist->receiver)
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
                        {{-- Existing signature logic --}}
                        @if($checklist->shift == 'malam' && $checklist->senderSignature)
                        <img src="data:image/png;base64,{{ $checklist->senderSignature }}" alt="TTD Petugas Malam"
                            class="max-h-24 max-w-full object-contain">
                        @elseif($checklist->shift == 'pagi' && $checklist->receivedSignature)
                        <img src="data:image/png;base64,{{ $checklist->receivedSignature }}" alt="TTD Petugas Malam"
                            class="max-h-24 max-w-full object-contain">
                        @elseif($checklist->shift == 'pagi' && !$checklist->receivedSignature)
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
                        @if($checklist->shift == 'malam')
                        {{ $checklist->sender->name ?? '' }}
                        @elseif($checklist->shift == 'pagi' && $checklist->receiver)
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

{{-- Action Buttons --}}
@if(isset($checklist))
<div class="mt-4 sm:mt-6 text-center space-y-2 sm:space-y-0 sm:space-x-4 flex flex-col sm:flex-row justify-center">
    <button onclick="window.print()"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 rounded-lg w-full sm:w-auto">
        Print
    </button>
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
                event.preventDefault();
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
