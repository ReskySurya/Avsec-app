@extends('layouts.app')
@section('title', 'Detail Checklist Penyisiran')

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
                        \Carbon\Carbon::parse($checklist->time)->format('H:i') }}</p>
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
                @forelse($groupedDetails as $category => $details)
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
                                <span class="text-blue-600 font-bold text-lg">✓</span>
                                <span class="ml-1 text-sm">Ya</span>
                                @elseif($detail->isfindings === false)
                                <span class="text-gray-400 text-sm">Tidak</span>
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

                    {{-- Catatan --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Catatan:</label>
                        <p class="text-sm mt-1">{{ $detail->notes ?? '-' }}</p>
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
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold w-10 sm:w-12 text-sm">
                                NO</th>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold min-w-[150px] text-sm">
                                KETERANGAN</th>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold text-xs sm:text-sm">
                                TEMUAN: YA</th>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold text-xs sm:text-sm">
                                TEMUAN: TIDAK</th>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold text-xs sm:text-sm">
                                KONDISI: BAIK</th>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold text-xs sm:text-sm">
                                KONDISI: RUSAK</th>
                            <th
                                class="border-2 border-gray-400 px-2 sm:px-4 py-2 sm:py-3 text-center font-bold min-w-[120px] text-sm">
                                Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($groupedDetails as $category => $details)
                        <tr class="bg-blue-100">
                            <td colspan="7"
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
                                <span class="text-blue-600 font-bold text-lg">✓</span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="border-2 border-gray-400 p-2 text-center">
                                @if($detail->isfindings === false)
                                <span class="text-blue-600 font-bold text-lg">✓</span>
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
                            <td class="border-2 border-gray-400 p-2 font-medium text-xs sm:text-sm">
                                {{ $detail->notes ?? '-' }}
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

            {{-- Signature Section --}}
            <div class="pt-6 sm:pt-8">
                <form id="signatureForm" action="{{ route('checklist.receivedSignature.penyisiran', $checklist->id) }}"
                    method="POST">
                    @csrf
                    <div class="flex flex-col sm:flex-row justify-around text-center space-y-6 sm:space-y-0">
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
                                $checklist->created_at->format('Y')
                                }}
                                @else
                                {{ date('d') }} {{ $bulan[(int)date('n')] }} {{ date('Y') }}
                                @endif
                            </p>
                            <p class="font-semibold text-sm sm:text-base">Diserahkan oleh:</p>
                            @if($checklist->senderSignature)
                            <img src="data:image/png;base64,{{ $checklist->senderSignature }}" alt="TTD Pengirim"
                                class="mx-auto mt-2 h-16 sm:h-24 border rounded">
                            @else
                            <div
                                class="mx-auto mt-2 h-16 sm:h-24 w-24 sm:w-32 border rounded flex items-center justify-center text-xs sm:text-sm text-gray-500">
                                No Signature</div>
                            @endif
                            <p class="mt-2 font-medium text-sm">{{ $checklist->sender->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">Petugas</p>
                        </div>

                        {{-- Disetujui oleh --}}
                        <div>
                            <br>
                            <p class="font-semibold text-sm sm:text-base">Disetujui oleh:</p>
                            @if($checklist->approvedSignature)
                            <img src="data:image/png;base64,{{ $checklist->approvedSignature }}" alt="TTD Supervisor"
                                class="mx-auto mt-2 h-16 sm:h-24 border rounded">
                            @else
                            <div
                                class="mx-auto mt-2 h-16 sm:h-24 w-24 sm:w-32 border rounded flex items-center justify-center text-xs sm:text-sm text-gray-500">
                                Belum TTD</div>
                            @endif
                            <p class="mt-2 font-medium text-sm">{{ $checklist->approver->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">Supervisor</p>
                        </div>

                        {{-- Diterima oleh --}}
                        <div>
                            <br>
                            <p class="font-semibold text-sm sm:text-base">Diterima oleh:</p>
                            @if($checklist->receivedSignature)
                            <img src="data:image/png;base64,{{ $checklist->receivedSignature }}" alt="TTD Penerima"
                                class="mx-auto mt-2 h-16 sm:h-24 border rounded">
                            @else
                            {{-- Tampilkan canvas hanya jika user yang login adalah penerima --}}
                            @if(Auth::id() == $checklist->received_id)
                            <div
                                class="w-32 sm:w-48 mx-auto mb-2 h-20 sm:h-28 flex flex-col items-center justify-center">
                                <canvas id="signature-canvas" class="w-full h-full"></canvas>
                            </div>
                            <button type="button" id="clear-signature"
                                class="text-xs sm:text-sm text-blue-600 hover:underline mt-1 no-print">Hapus</button>
                            @else
                            <div
                                class="mx-auto mt-2 h-16 sm:h-24 w-24 sm:w-32 border rounded flex items-center justify-center text-xs sm:text-sm text-gray-500">
                                Menunggu TTD</div>
                            @endif
                            @endif
                            <p class="mt-2 font-medium text-sm">{{ $checklist->receiver->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">Petugas</p>
                        </div>
                    </div>

                    {{-- Tombol Simpan TTD --}}
                    @if(!$checklist->receivedSignature && Auth::id() == $checklist->received_id)
                    <div class="mt-6 text-center no-print">
                        <input type="hidden" name="receivedSignature" id="receivedSignature">
                        <button type="submit"
                            class="w-full sm:w-auto px-6 sm:px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-lg text-sm sm:text-base">
                            Simpan Tanda Tangan Penerima
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-4 no-print">
        <a href="{{ route('dashboard.officer') }}"
            class="px-6 sm:px-8 py-3 bg-gray-600 text-white text-center rounded-lg hover:bg-gray-700 transition-colors shadow-lg text-sm sm:text-base">
            Kembali ke Dashboard
        </a>
    </div>
</div>

{{-- Signature Pad Library --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('signature-canvas');
        // Hanya inisialisasi jika canvas ada di halaman
        if (canvas) {
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(249, 250, 251)' // bg-gray-50
            });

            const clearButton = document.getElementById('clear-signature');
            const hiddenInput = document.getElementById('receivedSignature');
            const form = document.getElementById('signatureForm');

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
            }
            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();

            clearButton.addEventListener('click', () => signaturePad.clear());

            form.addEventListener('submit', function(event) {
                if (signaturePad.isEmpty()) {
                    alert("Harap berikan tanda tangan Anda terlebih dahulu.");
                    event.preventDefault();
                    return;
                }
                hiddenInput.value = signaturePad.toDataURL('image/png');
            });
        }
    });
</script>

<style>
    @media print {
        .no-print {
            display: none !important;
        }

        #printable-area {
            box-shadow: none;
            border: none;
        }
    }

    @media (max-width: 768px) {
        .text-sm {
            font-size: 0.8rem;
        }

        .text-base {
            font-size: 0.9rem;
        }

        input,
        textarea,
        select {
            font-size: 1rem;
        }
    }
</style>
@endsection
