@extends('layouts.app')

@section('title', 'Detail Form Pencatatan PI')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 lg:pt-20">

    <div class="flex justify-start pb-4">
        <a href="{{ route('supervisor.form-pencatatan-pi.list') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-medium transition-colors">Kembali ke daftar</a>
    </div>
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-white mb-1">Detail Form Pencatatan PI</h2>
                    <p class="text-sm text-blue-200">ID: {{ $pencatatanPI->id }}</p>
                </div>
                <div class="text-right">
                    @if($pencatatanPI->status === 'approved')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Disetujui
                    </span>
                    @elseif($pencatatanPI->status === 'submitted')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 001.414-1.414L11 9.586V5z" clip-rule="evenodd"></path>
                        </svg>
                        Menunggu Persetujuan
                    </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kolom Kiri --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tanggal</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($pencatatanPI->date)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Grup</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $pencatatanPI->grup }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nama Pemilik</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $pencatatanPI->name_person }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Instansi</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $pencatatanPI->agency }}</p>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Jenis PI</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $pencatatanPI->jenis_PI }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Jam Masuk / Keluar</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $pencatatanPI->in_time }} / {{ $pencatatanPI->out_time ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Jumlah Masuk / Keluar</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $pencatatanPI->in_quantity }} / {{ $pencatatanPI->out_quantity ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Keterangan</label>
                        <p class="mt-1 text-sm text-gray-800 rounded-lg">{{ $pencatatanPI->summary ?? 'Tidak ada keterangan.' }}</p>
                    </div>
                </div>
            </div>
            <div class="mt-12">
                {{-- Date Section --}}
                <form id="signatureForm" action="{{ route('supervisor.form-pencatatan-pi.signature', $pencatatanPI->id) }}" method="POST">
                    @csrf
                    <div class="text-center mb-6">
                        @php
                        $bulan = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        @endphp
                        <p class="text-sm">Yogyakarta,
                            @if(isset($pencatatanPI))
                            {{ $pencatatanPI->created_at->format('d') }} {{ $bulan[(int)$pencatatanPI->created_at->format('n')] }} {{
                                $pencatatanPI->created_at->format('Y') }}
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
                            <div class="w-48 mx-auto h-28 flex flex-col items-center justify-center">
                                @if($pencatatanPI->senderSignature)
                                <img src="data:image/png;base64,{{ $pencatatanPI->senderSignature }}" alt="TTD Yang Menyerahkan"
                                    class="max-h-24 max-w-full object-contain">
                                @else
                                <div class="border-b border-dotted border-black w-full h-1"></div>
                                @endif
                            </div>
                            <p class="text-sm font-semibold">
                                ({{ $pencatatanPI->sender->name ?? '...' }})
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Yang Mengetahui</p>
                            @if($pencatatanPI->approvedSignature)
                            <img src="data:image/png;base64,{{ $pencatatanPI->approvedSignature }}" alt="TTD Penerima" class="mx-auto mt-2 h-24 border rounded">
                            @else
                            {{-- Tampilkan canvas hanya jika user yang login adalah penerima --}}
                            @if(Auth::id() == $pencatatanPI->approved_id)
                            <div class="w-48 mx-auto mb-2 h-28 flex flex-col items-center justify-center">
                                <canvas id="signature-canvas" class="w-full h-full"></canvas>
                            </div>
                            <button type="button" id="clear-signature" class="text-sm text-blue-600 hover:underline mt-1 no-print">Hapus</button>
                            @else
                            <div class="mx-auto mt-2 h-24 w-48 border rounded flex items-center justify-center text-sm text-gray-500">Menunggu TTD</div>
                            @endif
                            @endif
                            <p class="mt-2 text-sm font-semibold">({{ $pencatatanPI->approver->name ?? 'N/A' }})</p>
                        </div>
                    </div>
                    @if(!$pencatatanPI->approvedSignature && Auth::id() == $pencatatanPI->approved_id)
                    <div class="mt-6 text-center no-print">
                        <input type="hidden" name="approvedSignature" id="approvedSignature">
                        <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-lg">
                            Simpan Tanda Tangan
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

</div>

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
            const hiddenInput = document.getElementById('approvedSignature');
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
@endsection