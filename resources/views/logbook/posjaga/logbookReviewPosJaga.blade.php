@extends('layouts.app')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@endpush

@section('content')

<div class="max-w-4xl mx-auto lg:mt-20 mt-5">
    @if($logbook->senderSignature)
    {{-- Jika sudah ada tanda tangan pengirim, kembali ke daftar --}}
    <a href="{{ route('logbook.index') }}"
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
    <div class="flex justify-between items-start mb-4">
        <div>
            <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo" class="h-10 mb-2">
            <p class="text-xs text-gray-500">LOKASI: <span class="font-semibold">{{ $logbook->locationArea->name
                    }}</span></p>
            <p class="text-xs text-gray-500">HARI / TANGGAL: <span class="font-semibold">{{
                    \Carbon\Carbon::parse($logbook->created_at)->translatedFormat('l, d F Y') }}</span></p>
            <p class="text-xs text-gray-500">DINAS / SHIFT: <span class="font-semibold">{{ $logbook->shift }}</span></p>
        </div>
        <div class="text-right">
            <img src="{{ asset('images/Injourney-API.png') }}" alt="Logo Yogyakarta Airport" class="h-12">
        </div>
    </div>

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
            @foreach($personil as $index => $personil)
            <tr>
                <td class="border border-black px-2 py-1 text-center">{{ $index + 1 }}</td>
                <td class="border border-black px-2 py-1">{{ $personil->user->name }}</td>
                <td class="border border-black px-2 py-1 text-center">{{ $personil->classification }}</td>
                <td class="border border-black px-2 py-1 text-center">{{ $personil->description }}</td>
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
            @foreach($facility as $index => $facility)
            <tr>
                <td class="border border-black px-2 py-1 text-center">{{ $index + 1 }}</td>
                <td class="border border-black px-2 py-1">{{ $facility->facility }}</td>
                <td class="border border-black px-2 py-1 text-center">{{ $facility->quantity }}</td>
                <td class="border border-black px-2 py-1 text-center">{{ $facility->description }}</td>
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
                <div class="h-16 flex items-center justify-center">
                    @if($logbook->receivedSignature)
                    <img src="data:image/png;base64,{!! $logbook->receivedSignature !!}" class="h-16 mt-5"
                        alt="Tanda Tangan">
                    @else
                    <span class="italic text-gray-400">Belum tanda tangan</span>
                    @endif
                </div>
                <p class="font-semibold mt-1">{{ $logbook->receiverBy->name?? '-' }}</p>
            </div>

            {{-- Kanan: Yang Menyerahkan --}}
            <div>
                <p>Yang Menyerahkan</p>
                <div class="h-16 flex items-center justify-center">
                    @if($logbook->senderSignature)
                    <img src="data:image/png;base64,{!! $logbook->senderSignature !!}" class="h-16 mt-5"
                        alt="Tanda Tangan">
                    @else
                    <span class="italic text-gray-400">Belum tanda tangan</span>
                    @endif
                </div>
                <p class="font-semibold mt-1">{{ $logbook->senderBy->name?? '-' }}</p>
            </div>
        </div>

        {{-- Bawah Tengah: Chief Screening --}}
        <div class="mb-20">
            <p class="pb-5">Mengetahui,</p>
            @if($logbook->approvedSignature)
            <div class="h-16 flex items-center justify-center">
                <img src="data:image/png;base64,{!! $logbook->approvedSignature !!}" class="h-16 mt-5"
                    alt="Tanda Tangan Mengetahui">
            </div>
            @else
            <span class="italic text-gray-400">Belum tanda tangan</span>
            <p class="font-semibold mt-1">{{ $logbook->approverBy->name ?? '-' }}</p>
            @endif
        </div>
    </div>
</div>
@endsection