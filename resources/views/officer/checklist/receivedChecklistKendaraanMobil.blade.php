@extends('layouts.app')

@section('title', 'Checklist Pengecekan Harian Mobil Patroli')

@section('content')
<div class="mx-auto p-2 sm:p-4 lg:p-6 min-h-screen pt-2 sm:pt-5 lg:pt-20">
    <div class="bg-white shadow-lg sm:shadow-2xl rounded-lg sm:rounded-2xl overflow-hidden border border-gray-200">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 p-3 sm:px-6 sm:py-6 text-white">
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-2 sm:space-y-0">
                <!-- Logo Kiri -->
                <div class="flex justify-center sm:block">
                    <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                        class="w-12 h-12 sm:w-16 sm:h-16 lg:w-20 lg:h-20">
                </div>

                <!-- Title Section -->
                <div class="text-center flex-1 sm:mx-4">
                    <h1 class="text-sm sm:text-xl lg:text-2xl font-bold leading-tight">CHECK LIST PENGECEKAN HARIAN</h1>
                    <h2 class="text-xs sm:text-lg lg:text-xl font-semibold mt-1">KENDARAAN MOBIL PATROLI</h2>
                    <p class="text-xs sm:text-sm text-blue-100 mt-1">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
                </div>

                <!-- Logo Kanan -->
                <div class="flex justify-center sm:block">
                    <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo"
                        class="w-12 h-12 sm:w-20 sm:h-20 lg:w-24 lg:h-24">
                </div>
            </div>
        </div>

        {{-- Main Table - Mobile Card Style --}}
        <div class="block md:hidden ">
            {{-- Mobile Card Layout --}}
            <div class="p-3">
                <div class="bg-blue-50 p-2 rounded mb-3 text-center">
                    <h3 class="text-sm font-bold text-blue-800">KONDISI SHIFT {{ strtoupper($checklist->shift) }}</h3>
                </div>

                @php
                $categories = [
                'mesin' => ['A', 'MESIN'],
                'mekanik' => ['B', 'MEKANIK'],
                'lainlain' => ['C', 'LAIN-LAIN'],
                ];
                @endphp

                @foreach ($categories as $categoryKey => $categoryData)
                @if (isset($groupedItems[$categoryKey]))
                {{-- Category Header --}}
                <div class="bg-gray-100 border border-gray-300 rounded-t-lg p-2 mb-0">
                    <h4 class="font-bold text-sm">{{ $categoryData[0] }}. {{ $categoryData[1] }}</h4>
                </div>

                {{-- Category Items --}}
                <div class="border border-t-0 border-gray-300 rounded-b-lg mb-4 overflow-hidden">
                    @foreach ($groupedItems[$categoryKey] as $index => $detail)
                    <div
                        class="border-b border-gray-200 last:border-b-0 p-3 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 pr-3">
                                <div class="flex items-start">
                                    <span class="text-xs font-semibold text-gray-500 mr-2">{{ $index + 1 }}.</span>
                                    <span class="text-sm font-medium">{{ $detail->item->name }}</span>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <div class="text-center">
                                    <div class="text-xs text-gray-600 mb-1">BAIK</div>
                                    @if ($detail->is_ok)
                                    <span class="text-green-600 font-bold text-lg">✓</span>
                                    @else
                                    <div class="w-6 h-6 border border-gray-300 rounded"></div>
                                    @endif
                                </div>
                                <div class="text-center">
                                    <div class="text-xs text-gray-600 mb-1">TIDAK</div>
                                    @if (!$detail->is_ok)
                                    <span class="text-red-600 font-bold text-lg">✓</span>
                                    @else
                                    <div class="w-6 h-6 border border-gray-300 rounded"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block  overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th rowspan="2" class="border border-black px-2 py-2 text-sm font-bold w-12">NO</th>
                        <th rowspan="2" class="border border-black px-2 py-2 text-sm font-bold min-w-40">KETERANGAN</th>
                        <th class="border border-black px-2 py-2 text-sm font-bold text-center" colspan="2">
                            KONDISI SHIFT {{ strtoupper($checklist->shift) }}
                        </th>
                    </tr>
                    <tr class="bg-gray-100">
                        <th class="border border-black px-2 py-1 text-xs font-bold w-16">BAIK</th>
                        <th class="border border-black px-2 py-1 text-xs font-bold w-16">TIDAK</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $categoryKey => $categoryData)
                    @if (isset($groupedItems[$categoryKey]))
                    {{-- Category Header Row --}}
                    <tr>
                        <td class="border border-black px-2 py-2 text-center text-sm font-bold bg-gray-50">
                            {{ $categoryData[0] }}
                        </td>
                        <td class="border border-black px-2 py-2 text-sm font-bold bg-gray-50 uppercase">
                            {{ $categoryData[1] }}
                        </td>
                        <td class="border border-black bg-gray-50"></td>
                        <td class="border border-black bg-gray-50"></td>
                    </tr>

                    {{-- Category Items --}}
                    @foreach ($groupedItems[$categoryKey] as $index => $detail)
                    <tr>
                        <td class="border border-black px-2 py-2 text-center text-sm">{{ $index + 1 }}</td>
                        <td class="border border-black px-2 py-2 text-sm pl-4">{{ $detail->item->name }}</td>
                        <td class="border border-black px-2 py-2 text-center">
                            @if ($detail->is_ok)
                            <span class="text-green-600 font-bold text-base">✓</span>
                            @endif
                        </td>
                        <td class="border border-black px-2 py-2 text-center">
                            @if (!$detail->is_ok)
                            <span class="text-red-600 font-bold text-base">✓</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Keterangan & Catatan Section --}}
        <div class="">
            <div class="p-3 sm:p-4">
                {{-- Keterangan --}}
                <div class="mb-4 sm:mb-6">
                    <h3 class="font-bold text-sm mb-2">KETERANGAN :</h3>
                    <div class="space-y-1">
                        <div class="flex">
                            <span class="w-4 text-sm flex-shrink-0">1</span>
                            <div class="border-b border-dotted border-black flex-1 min-h-[20px] ml-1">
                                <span class="text-xs sm:text-sm">Petugas dinas pagi melakukan pengecekan pada nomor A, B
                                    dan C</span>
                            </div>
                        </div>
                        <div class="flex">
                            <span class="w-4 text-sm flex-shrink-0">2</span>
                            <div class="border-b border-dotted border-black flex-1 min-h-[20px] ml-1">
                                <span class="text-xs sm:text-sm">Petugas dinas pagi melakukan pengecekan pada nomor B
                                    dan C</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CATATAN Section --}}
                <div class="mb-4 sm:mb-6">
                    <h3 class="font-bold text-sm mb-2">CATATAN :</h3>
                    <div class="space-y-1">
                        @php
                        $allNotes = [];
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
                            <span class="w-4 text-sm flex-shrink-0">{{ $index + 1 }}</span>
                            <div
                                class="border-b border-dotted border-black flex-1 min-h-[20px] flex items-end pb-1 ml-1">
                                <span class="text-xs sm:text-sm">
                                    <strong>{{ $noteData['item_name'] }}:</strong> {{ $noteData['notes'] }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="flex">
                            <span class="w-4 text-sm flex-shrink-0">1</span>
                            <div class="border-b border-dotted border-black flex-1 min-h-[20px] ml-1"></div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Signature Section --}}
        <form action="{{ route('checklist.receivedSignature', $checklist->id) }}" method="POST">
            @csrf
            <div class="">
                <div class="flex flex-col sm:flex-row">
                    {{-- Petugas Dinas Pagi --}}
                    <div class="flex-1 text-center p-4 sm:p-6 ">
                        <div class="mb-2">
                            @php
                            $bulan = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                            @endphp
                            <p class="text-xs sm:text-sm">Yogyakarta,
                                @if(isset($checklist))
                                {{ $checklist->created_at->format('d') }} {{
                                $bulan[(int)$checklist->created_at->format('n')] }} {{
                                $checklist->created_at->format('Y') }}
                                @else
                                {{ date('d') }} {{ $bulan[(int)date('n')] }} {{ date('Y') }}
                                @endif
                            </p>
                            <p class="text-xs sm:text-sm font-semibold">Petugas Dinas Pagi</p>
                        </div>
                        <div class="w-32 sm:w-48 mx-auto mb-2 h-20 sm:h-28 flex flex-col items-center justify-center">
                            @if($checklist->shift == 'pagi' && $checklist->senderSignature)
                            <img src="data:image/png;base64,{{ $checklist->senderSignature }}" alt="TTD Petugas Pagi"
                                class="max-h-16 sm:max-h-24 max-w-full object-contain">
                            @elseif($checklist->shift == 'malam' && $checklist->receivedSignature)
                            <img src="data:image/png;base64,{{ $checklist->receivedSignature }}" alt="TTD Petugas Pagi"
                                class="max-h-16 sm:max-h-24 max-w-full object-contain">
                            @elseif($checklist->shift == 'malam' && !$checklist->receivedSignature)
                            <div class="w-full h-full border border-gray-300 rounded-md bg-gray-50">
                                <canvas id="signature-canvas" class="w-full h-full touch-action-none"></canvas>
                            </div>
                            <button type="button" id="clear-signature"
                                class="text-xs text-blue-600 hover:text-blue-800 mt-1 px-2 py-1 bg-blue-100 rounded">Clear</button>
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

                    {{-- Petugas Dinas Malam --}}
                    <div class="flex-1 text-center p-4 sm:p-6">
                        <div class="mb-2 sm:mb-16">
                            <p class="text-xs sm:text-sm font-semibold">Petugas Dinas Malam</p>
                        </div>
                        <div class="w-32 sm:w-48 mx-auto mb-2 h-20 sm:h-28 flex flex-col items-center justify-center">
                            @if($checklist->shift == 'malam' && $checklist->senderSignature)
                            <img src="data:image/png;base64,{{ $checklist->senderSignature }}" alt="TTD Petugas Malam"
                                class="max-h-16 sm:max-h-24 max-w-full object-contain">
                            @elseif($checklist->shift == 'pagi' && $checklist->receivedSignature)
                            <img src="data:image/png;base64,{{ $checklist->receivedSignature }}" alt="TTD Petugas Malam"
                                class="max-h-16 sm:max-h-24 max-w-full object-contain">
                            @elseif($checklist->shift == 'pagi' && !$checklist->receivedSignature)
                            <div class="w-full h-full border border-gray-300 rounded-md bg-gray-50">
                                <canvas id="signature-canvas" class="w-full h-full touch-action-none"></canvas>
                            </div>
                            <button type="button" id="clear-signature"
                                class="text-xs text-blue-600 hover:text-blue-800 mt-1 px-2 py-1 bg-blue-100 rounded">Clear</button>
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

            {{-- Submit Button --}}
            @if(!$checklist->receivedSignature)
            <div class="p-3 sm:p-4 text-center">
                <input type="hidden" name="receivedSignature" id="receivedSignature">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 sm:px-6 py-2 rounded-lg w-full sm:w-auto text-sm">
                    Simpan Tanda Tangan Penerima
                </button>
            </div>
            @endif
        </form>
    </div>

    {{-- Action Buttons --}}
    @if(isset($checklist))
    <div class="mt-4 text-center">
        <a href="{{ route('dashboard.officer') }}"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 sm:px-6 py-2 rounded-lg w-full sm:w-auto inline-block text-center text-sm">
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
            penColor: 'rgb(0, 0, 0)',
            minWidth: 1,
            maxWidth: 3,
            throttle: 16,
            minDistance: 5,
        });

        const clearButton = document.getElementById('clear-signature');
        const hiddenInput = document.getElementById('receivedSignature');
        const form = document.querySelector('form');

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width * ratio;
            canvas.height = rect.height * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            canvas.style.width = rect.width + 'px';
            canvas.style.height = rect.height + 'px';
            signaturePad.clear();
            hiddenInput.value = '';
        }

        // Initial canvas setup
        setTimeout(resizeCanvas, 100);
        window.addEventListener("resize", resizeCanvas);

        // Handle orientation change on mobile
        window.addEventListener("orientationchange", function() {
            setTimeout(resizeCanvas, 200);
        });

        if (clearButton) {
            clearButton.addEventListener('click', function () {
                signaturePad.clear();
                hiddenInput.value = '';
            });
        }

        signaturePad.onEnd = () => {
            if (!signaturePad.isEmpty()) {
                hiddenInput.value = signaturePad.toDataURL('image/png').split(',')[1];
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

{{-- Responsive Styles --}}
<style>
    /* Base responsive styles */
    @media (max-width: 768px) {

        /* Ensure table scrolls horizontally on very small screens */
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Touch-friendly signature pad */
        canvas {
            touch-action: none;
            user-select: none;
            -webkit-user-select: none;
        }

        /* Better button spacing on mobile */
        button,
        .btn {
            min-height: 44px;
            font-size: 14px;
        }
    }

    /* Print styles */
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
        .bg-gray-600,
        .bg-indigo-600 {
            display: none !important;
        }

        /* Hide mobile layout in print */
        .block.md\\:hidden {
            display: none !important;
        }

        /* Show desktop table in print */
        .hidden.md\\:block {
            display: block !important;
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

        /* Print signature areas */
        canvas {
            border: 1px solid #000 !important;
            height: 80px !important;
        }
    }

    /* High DPI display support */
    @media (-webkit-min-device-pixel-ratio: 2),
    (min-resolution: 192dpi) {
        canvas {
            image-rendering: -webkit-optimize-contrast;
            image-rendering: optimize-contrast;
        }
    }

    /* Landscape orientation adjustments */
    @media screen and (orientation: landscape) and (max-height: 500px) {
        .pt-2 {
            padding-top: 0.25rem;
        }

        .h-20 {
            height: 3rem;
        }

        .mb-2 {
            margin-bottom: 0.25rem;
        }
    }

    /* Very small screens */
    @media (max-width: 320px) {
        .text-sm {
            font-size: 0.75rem;
        }

        .text-xs {
            font-size: 0.625rem;
        }

        .p-3 {
            padding: 0.5rem;
        }
    }

    /* Accessibility improvements */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>

@endsection
