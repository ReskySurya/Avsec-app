@extends('layouts.app')
@section('title', 'Detail Checklist Penyisiran')

@section('content')
<div class="mx-auto p-4 sm:p-6 min-h-screen pt-5 sm:pt-20">

    {{-- Alert Sukses --}}
    @if(session('success'))
    <div class="bg-green-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg">
        {{ session('success') }}
    </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
    <div class="bg-red-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200" id="printable-area">
        {{-- ... (kode header dan tabel tetap sama seperti sebelumnya) ... --}}
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-4 sm:px-6 py-4 sm:py-6 text-white">
            <div class="flex flex-col sm:flex-row items-center justify-between text-center sm:text-left">
                <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                    class="w-16 h-16 sm:w-20 sm:h-20 mb-2 sm:mb-0">
                <div class="my-2 sm:my-0">
                    <h1 class="text-xl sm:text-2xl font-bold">CHECK LIST PENGECEKAN HARIAN</h1>
                    <h2 class="text-lg sm:text-xl font-semibold">CHECK LIST PENYISIRAN DAERAH STERIL RUANG TUNGGU</h2>
                    <p class="text-sm sm:text-base text-blue-100">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
                </div>
                <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo" class="w-20 h-20 sm:w-24 sm:h-24 mt-2 sm:mt-0">
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg">
                <div class="space-y-1">
                    <p class="block text-sm font-semibold text-gray-700">Hari/Tanggal:</p>
                    <p class="text-base text-gray-900">{{ $checklist->date->format('d F Y') }}</p>
                </div>
                <div class="space-y-1">
                    <p class="block text-sm font-semibold text-gray-700">Jam:</p>
                    <p class="text-base text-gray-900">{{ \Carbon\Carbon::parse($checklist->time)->format('H:i') }}</p>
                </div>
                <div class="space-y-1">
                    <p class="block text-sm font-semibold text-gray-700">Grup:</p>
                    <p class="text-base text-gray-900">Grup {{ $checklist->grup }}</p>
                </div>
                <div class="space-y-1">
                    <p class="block text-sm font-semibold text-gray-700">Nama Petugas:</p>
                    <p class="text-base text-gray-900">{{ $checklist->sender->name ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="mt-8 overflow-x-auto">
                <table class="w-full border-collapse border-2 border-gray-400 responsive-table">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th class="border-2 border-gray-400 px-4 py-3 text-center font-bold w-12">NO</th>
                            <th class="border-2 border-gray-400 px-4 py-3 text-center font-bold">KETERANGAN</th>
                            <th class="border-2 border-gray-400 px-4 py-3 text-center font-bold">TEMUAN: YA</th>
                            <th class="border-2 border-gray-400 px-4 py-3 text-center font-bold">TEMUAN: TIDAK</th>
                            <th class="border-2 border-gray-400 px-4 py-3 text-center font-bold">KONDISI: BAIK</th>
                            <th class="border-2 border-gray-400 px-4 py-3 text-center font-bold">KONDISI: RUSAK</th>
                            <th class="border-2 border-gray-400 px-4 py-3 text-center font-bold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($groupedDetails as $category => $details)
                        <tr class="bg-blue-100 category-row">
                            <td colspan="7" class="border-2 border-gray-400 px-4 py-2 font-bold text-blue-800 text-left">{{ strtoupper($category) }}</td>
                        </tr>
                        @foreach($details as $detail)
                        <tr>
                            <td data-label="NO" class="border-2 border-gray-400 p-2 text-center font-bold">{{ $no++ }}.</td>
                            <td data-label="KETERANGAN" class="border-2 border-gray-400 p-2 font-medium">{{ $detail->item->name }}</td>
                            <td data-label="TEMUAN: YA" class="border-2 border-gray-400 p-2 text-center">
                                @if($detail->isfindings === true) <span class="text-blue-600 font-bold text-xl">✓</span> @else - @endif
                            </td>
                            <td data-label="TEMUAN: TIDAK" class="border-2 border-gray-400 p-2 text-center">
                                @if($detail->isfindings === false) <span class="text-blue-600 font-bold text-xl">✓</span> @else - @endif
                            </td>
                            <td data-label="KONDISI: BAIK" class="border-2 border-gray-400 p-2 text-center">
                                @if($detail->iscondition === true) <span class="text-green-600 font-bold text-xl">✓</span> @else - @endif
                            </td>
                            <td data-label="KONDISI: RUSAK" class="border-2 border-gray-400 p-2 text-center">
                                @if($detail->iscondition === false) <span class="text-red-600 font-bold text-xl">✓</span> @else - @endif
                            </td>
                            <td data-label="CATATAN" class="border-2 border-gray-400 p-2 font-medium text-sm">
                                {{ $detail->notes ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Tidak ada data detail checklist.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Signature Section --}}
            <div class="pt-8">
                <!-- <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">Verifikasi</h3> -->
                <form id="signatureForm" action="{{ route('checklist.receivedSignature.penyisiran', $checklist->id) }}" method="POST">
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
                            <p class="text-sm">Yogyakarta,
                                @if(isset($checklist))
                                {{ $checklist->created_at->format('d') }} {{
                            $bulan[(int)$checklist->created_at->format('n')] }} {{ $checklist->created_at->format('Y')
                            }}
                                @else
                                {{ date('d') }} {{ $bulan[(int)date('n')] }} {{ date('Y') }}
                                @endif
                            </p>
                            <p class="font-semibold">Diserahkan oleh:</p>
                            <img src="data:image/png;base64,{{ $checklist->senderSignature }}" alt="TTD Pengirim" class="mx-auto mt-2 h-24 border rounded">
                            <p class="mt-2 font-medium">{{ $checklist->sender->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Petugas</p>
                        </div>

                        {{-- Disetujui oleh --}}
                        <div>
                            <br>
                            <p class="font-semibold">Disetujui oleh:</p>
                            @if($checklist->approvedSignature)
                            <img src="data:image/png;base64,{{ $checklist->approvedSignature }}" alt="TTD Supervisor" class="mx-auto mt-2 h-24 border rounded">
                            @else
                            <div class="mx-auto mt-2 h-24 w-48 border rounded flex items-center justify-center text-sm text-gray-500">Belum TTD</div>
                            @endif
                            <p class="mt-2 font-medium">{{ $checklist->approver->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Supervisor</p>
                        </div>
                        
                        {{-- Diterima oleh --}}
                        <div>
                            <br>
                            <p class="font-semibold">Diterima oleh:</p>
                            @if($checklist->receivedSignature)
                            <img src="data:image/png;base64,{{ $checklist->receivedSignature }}" alt="TTD Penerima" class="mx-auto mt-2 h-24 border rounded">
                            @else
                            {{-- Tampilkan canvas hanya jika user yang login adalah penerima --}}
                            @if(Auth::id() == $checklist->received_id)
                            <div class="w-48 mx-auto mb-2 h-28 flex flex-col items-center justify-center">
                                <canvas id="signature-canvas" class="w-full h-full"></canvas>
                            </div>
                            <button type="button" id="clear-signature" class="text-sm text-blue-600 hover:underline mt-1 no-print">Hapus</button>
                            @else
                            <div class="mx-auto mt-2 h-24 w-48 border rounded flex items-center justify-center text-sm text-gray-500">Menunggu TTD</div>
                            @endif
                            @endif
                            <p class="mt-2 font-medium">{{ $checklist->receiver->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Petugas</p>
                        </div>

                    </div>

                    {{-- Tombol Simpan TTD --}}
                    @if(!$checklist->receivedSignature && Auth::id() == $checklist->received_id)
                    <div class="mt-6 text-center no-print">
                        <input type="hidden" name="receivedSignature" id="receivedSignature">
                        <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-lg">
                            Simpan Tanda Tangan Penerima
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="mt-6 flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-4 no-print">
        <a href="{{ route('dashboard.officer') }}" class="px-8 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors shadow-lg">
            Kembali ke Dashboard
        </a>
        <button onclick="window.print()" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
            Cetak
        </button>
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
    /* ... (kode style responsive dan print tetap sama) ... */
</style>
@endsection