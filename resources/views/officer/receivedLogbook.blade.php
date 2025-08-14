@extends('layouts.app')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@endpush

@section('content')
<div class="max-w-4xl mx-auto lg:mt-20 mt-5">
    <a href="{{ route('dashboard.officer') }}"
        class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali
    </a>
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
                @if($logbook->receivedSignature)
                <div class="h-16 flex items-center justify-center">
                    <img src="data:image/png;base64,{!! $logbook->receivedSignature !!}" class="h-16 mt-5"
                        alt="Tanda Tangan Penerima">
                </div>
                <p class="font-semibold mt-1">{{ $logbook->receiverBy->name ?? '-' }}</p>
                @else
                <form
                    action="{{ route('logbook.signature.receive', ['location' => $logbook->locationArea->name, 'logbookID' => $logbook->logbookID]) }}"
                    method="POST" onsubmit="return handleSignatureSubmit(event)">
                    @csrf
                    <div class="border-2 border-gray-200 rounded-xl p-4 my-2">
                        <div class="relative w-full h-32 border border-gray-300 rounded-lg bg-white">
                            <canvas id="signature-canvas-receiver" class="w-full h-full"></canvas>
                        </div>
                        <input type="hidden" name="signature" id="signature-data-receiver">
                        <div class="flex justify-between items-center mt-2">
                            <span id="signature-status-receiver" class="text-xs text-gray-500">Belum ada tanda
                                tangan</span>
                            <button type="button" class="text-sm text-blue-600 hover:text-blue-800"
                                onclick="clearSignatureReceiver()">Clear</button>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-semibold shadow transition">
                        Konfirmasi & Terima
                    </button>
                </form>
                @endif
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
                <p class="font-semibold mt-1">{{ $logbook->senderBy->name }}</p>
            </div>
        </div>

        {{-- Bawah Tengah: Chief Screening --}}
        <div class="mt-12">
            <p>Mengetahui,</p>
            <div class="h-16 flex items-center justify-center">
                @if($logbook->approvedSignature)
                <img src="data:image/png;base64,{!! $logbook->approvedSignature !!}" class="h-12" alt="Tanda Tangan">
                @else
                <span class="italic text-gray-400">Belum tanda tangan</span>
                @endif
            </div>
            <p class="font-semibold mt-1">{{ $logbook->approverBy->name }}</p>
        </div>
    </div>

</div>

<script>
    let signaturePadReceiver;

    // Inisialisasi hanya jika canvas ada (jika tanda tangan belum ada)
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('signature-canvas-receiver');
        if (canvas) {
            initializeSignaturePadReceiver();
        }
    });

    function initializeSignaturePadReceiver() {
        if (signaturePadReceiver) {
            signaturePadReceiver.clear();
            return;
        }

        const canvas = document.getElementById('signature-canvas-receiver');
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        signaturePadReceiver = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });

        const statusEl = document.getElementById('signature-status-receiver');
        signaturePadReceiver.addEventListener("endStroke", () => {
            if (statusEl) {
                statusEl.textContent = 'Tanda tangan tersimpan';
                statusEl.className = 'text-xs text-green-600 font-semibold';
            }
        });
    }

    function clearSignatureReceiver() {
        if (signaturePadReceiver) {
            signaturePadReceiver.clear();
            const statusEl = document.getElementById('signature-status-receiver');
            if (statusEl) {
                statusEl.textContent = 'Belum ada tanda tangan';
                statusEl.className = 'text-xs text-gray-500';
            }
        }
    }

    function handleSignatureSubmit(event) {
        event.preventDefault();

        if (!signaturePadReceiver || signaturePadReceiver.isEmpty()) {
            alert('Mohon berikan tanda tangan Anda sebelum melanjutkan.');
            return false;
        }

        const signatureData = signaturePadReceiver.toDataURL('image/png');
        document.getElementById('signature-data-receiver').value = signatureData;

        const form = event.target;
        const formData = new FormData(form);

        fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                } else if (data.error) {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Jika data berhasil disimpan tapi response bukan JSON, redirect ke halaman index
                if (error.message.includes('JSON')) {
                    alert('Berhasil menyimpan tanda tangan!!');
                } else {
                    alert('Terjadi kesalahan saat menyimpan tanda tangan. Silakan coba lagi.');
                }
            });

        return false;
    }
</script>
@endsection
