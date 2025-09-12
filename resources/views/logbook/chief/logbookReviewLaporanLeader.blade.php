@extends('layouts.app')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@endpush

@section('content')

<div class="max-w-4xl mx-auto lg:mt-20 mt-5">
    {{-- Tombol Kembali --}}
    @if($logbook->senderSignature)
    {{-- Jika sudah ada tanda tangan pengirim, kembali ke daftar --}}
    <a href="{{ route('logbook.chief.index') }}"
        class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Daftar
    </a>
    @else
    {{-- Jika belum ada tanda tangan pengirim, kembali ke halaman sebelumnya --}}
    <a href="javascript:history.back()"
        class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali
    </a>
    @endif
</div>
<div class="max-w-4xl mx-auto bg-white p-6 mt-6 shadow-md border text-sm">
    {{-- Logo dan Header --}}
    <div class="flex flex-col sm:flex-row items-center justify-between">
        <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo" class="w-20 h-20 mb-2 sm:mb-0">
        <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
            LOGBOOK LAPORAN <br>
            CATATAN AKTIVITAS TEAM LEADER <br>
        </h1>
        <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo" class="w-20 h-20 mt-2 sm:mt-0">
    </div>
    <!-- Informasi detail -->
    <div class="border-t border-gray-300 pt-3 mt-4 mb-6">
        <div class="grid grid-cols-2 gap-y-2 text-sm text-gray-700">
            <p>HARI / TANGGAL
                <span class="font-semibold">
                    : {{ \Carbon\Carbon::parse($logbook->created_at)->translatedFormat('l, d F Y') }}
                </span>
            </p>
            <p>DINAS / SHIFT <span class="font-semibold">: {{ strtoupper($logbook->shift) }}</span></p>
            <p>GRUP <span class="font-semibold">: {{ strtoupper($logbook->grup) }}</span></p>
        </div>
    </div>

    {{-- Tabel Kemajuan Personil --}}
    <div class="flex justify-center mb-2">
        <p class="font-semibold self-center">KEMAJUAN PERSONIL</p>
    </div>
    <table class="w-full border border-black mb-6 text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th class="border border-black px-2 py-1 w-10">No</th>
                <th class="border border-black px-2 py-1 w-20">Jumlah Personil</th>
                <th class="border border-black px-2 py-1 w-20">Jumlah Hadir</th>
                <th class="border border-black px-2 py-1 w-20">Jumlah Kekuatan</th>
                <th class="border border-black px-2 py-1">Materi</th>
                <th class="border border-black px-2 py-1 w-20">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kemajuanPersonil as $index => $item)
            <tr>
                <td class="border border-black px-2 py-1 text-center">{{ $index + 1 }}</td>
                <td class="border border-black px-2 py-1 text-center">{{ $item->jml_personil }}</td>
                <td class="border border-black px-2 py-1 text-center">{{ $item->jml_hadir }}</td>
                <td class="border border-black px-2 py-1 text-center">{{ $item->jml_kekuatan }}</td>
                <td class="border border-black px-2 py-1">{{ $item->materi }}</td>
                <td class="border border-black px-2 py-1 text-center">{{ $item->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tabel Petugas Jaga --}}
    <div class="flex justify-center mb-2">
        <p class="font-semibold self-center">PETUGAS JAGA</p>
    </div>
    <table class="w-full border border-black mb-6 text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th class="border border-black px-2 py-1 w-10">No</th>
                <th class="border border-black px-2 py-1">Nama Petugas</th>
                <th class="border border-black px-2 py-1 w-20">Klasifikasi</th>
                <th class="border border-black px-2 py-1 w-20">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personil as $index => $item)
            <tr>
                <td class="border border-black px-2 py-1 text-center">{{ $index + 1 }}</td>
                <td class="border border-black px-2 py-1">{{ $item->user->name }}</td>
                <td class="border border-black px-2 py-1 text-center">{{ $item->classification }}</td>
                <td class="border border-black px-2 py-1 text-center">{{ $item->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tabel Fasilitas --}}
    <div class="flex justify-center mb-2">
        <p class="font-semibold self-center">FASILITAS</p>
    </div>
    <table class="w-full border border-black mb-6 text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th class="border border-black px-2 py-1 w-10">No</th>
                <th class="border border-black px-2 py-1">Fasilitas</th>
                <th class="border border-black px-2 py-1 w-20">Jumlah</th>
                <th class="border border-black px-2 py-1 w-20">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facility as $index => $item)
            <tr>
                <td class="border border-black px-2 py-1 text-center">{{ $index + 1 }}</td>
                <td class="border border-black px-2 py-1">{{ $item->facility }}</td>
                <td class="border border-black px-2 py-1 text-center">{{ $item->quantity }}</td>
                <td class="border border-black px-2 py-1 text-center">{{ $item->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tabel Uraian Tugas --}}
    <div class="flex justify-center mb-2">
        <p class="font-semibold self-center">URAIAN TUGAS</p>
    </div>
    <table class="w-full border border-black mb-6 text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th class="border border-black px-2 py-1">Jam</th>
                <th class="border border-black px-2 py-1">Uraian Tugas</th>
                <th class="border border-black px-2 py-1">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logbookDetails as $index => $detail)
            <tr>
                <td class="border border-black px-2 py-1 text-center">
                    {{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} - {{
                    \Carbon\Carbon::parse($detail->end_time)->format('H:i') }}
                </td>
                <td class="border border-black px-2 py-1">{{ $detail->summary }}</td>
                <td class="border border-black px-2 py-1">{{ $detail->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tanda Tangan --}}
    <div class="mt-10 text-center text-sm">
        <div class="grid grid-cols-2 gap-4">
            {{-- Kiri: Yang Menerima --}}
            <div>
                <p>Yang Menerima</p>

                @if ($logbook->approvedSignature)
                <div class="h-24 flex items-center justify-center">
                    <img src="data:image/png;base64,{!! $logbook->approvedSignature !!}" class="h-24" alt="Tanda Tangan Penerima">
                </div>
                @elseif ($logbook->senderSignature && !$logbook->approvedSignature && $logbook->approved_by == Auth::id())
                <form action="{{ route('logbook.chief.signature.receive', ['logbookID' => $logbook->logbookID]) }}" method="POST" onsubmit="return handleReceiveSignatureSubmit(event)">
                    @csrf
                    <div class="border border-gray-300 rounded-lg my-2 max-w-xs mx-auto">
                        <canvas id="receive-signature-canvas" class="w-full h-32"></canvas>
                    </div>
                    <input type="hidden" name="signature" id="receive-signature-data">

                    <div class="flex justify-center items-center space-x-4 mb-2">
                        <button type="button" class="text-xs text-blue-600 hover:text-blue-800" onclick="clearReceiveSignature()">
                            Hapus Tanda Tangan
                        </button>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-semibold shadow-md">
                        Simpan Tanda Tangan
                    </button>
                </form>
                @else
                <div class="h-24 flex items-center justify-center">
                    <span class="italic text-gray-400">Menunggu Tanda Tangan</span>
                </div>
                @endif
                <p class="font-semibold m-2">{{ $logbook->approvedBy->name ?? 'Belum Ditentukan' }}</p>

            </div>

            {{-- Kanan: Yang Menyerahkan --}}
            <div>
                <p>Yang Menyerahkan</p>
                <div class="h-24 flex items-center justify-center">
                    @if($logbook->senderSignature)
                    <img src="data:image/png;base64,{!! $logbook->senderSignature !!}" class="h-24"
                        alt="Tanda Tangan">
                    @else
                    <span class="italic text-gray-400">Belum tanda tangan</span>
                    @endif
                </div>
                <p class="font-semibold mt-2">{{ $logbook->createdBy->name?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let receiveSignaturePad;

    function initializeReceiveSignaturePad() {
        const canvas = document.getElementById('receive-signature-canvas');
        if (!canvas) return; // If canvas is not on the page, do nothing

        // Adjust for HiDPI screens
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        receiveSignaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });
    }

    function clearReceiveSignature() {
        if (receiveSignaturePad) {
            receiveSignaturePad.clear();
        }
    }

    function handleReceiveSignatureSubmit(event) {
        if (!receiveSignaturePad || receiveSignaturePad.isEmpty()) {
            alert('Mohon berikan tanda tangan Anda sebelum melanjutkan.');
            event.preventDefault();
            return false;
        }
        document.getElementById('receive-signature-data').value = receiveSignaturePad.toDataURL('image/png');
        console.log('Signature Data:', document.getElementById('receive-signature-data').value);
        return true; // Let the form submit
    }

    // Initialize the signature pad when the DOM is ready
    document.addEventListener('DOMContentLoaded', initializeReceiveSignaturePad);
</script>
@endpush

@endsection