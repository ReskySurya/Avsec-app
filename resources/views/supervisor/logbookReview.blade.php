@extends('layouts.app')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@endpush

@section('content')
<div class="max-w-4xl mx-auto lg:mt-20 mt-5">
    <a href="{{ route('supervisor.logbook-form') }}"
        class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg shadow transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali
    </a>
</div>
<div class="max-w-4xl mx-auto bg-white p-6 mt-6 shadow-md border text-sm">
    {{-- Logo dan Header --}}
    <div class="flex flex-col sm:flex-row items-center justify-between">
        <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo" class="w-20 h-20 mb-2 sm:mb-0">
        <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
            LOGBOOK HARIAN <br>
            CATATAN AKTIVITAS HARIAN <br>
            POS JAGA
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
            <p>LOKASI <span class="font-semibold">: {{ $logbook->locationArea->name }}</span></p>
            <p>DINAS / SHIFT <span class="font-semibold">: {{ strtoupper($logbook->shift) }}</span></p>
            <p>GRUP <span class="font-semibold">: {{ strtoupper($logbook->grup) }}</span></p>
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
                <p class="font-semibold mt-1">{{ $logbook->receiverBy->name }}</p>
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
        <div class=" w-full md:w-1/3 mx-auto mt-8">
            <p>Mengetahui,</p>
            {{-- Cek 1: Apakah supervisor sudah ttd? --}}
            @if($logbook->approvedSignature)
                {{-- JIKA SUDAH: Tampilkan gambar tanda tangan dan nama --}}
                <div class="h-16 flex items-center justify-center">
                    <img src="data:image/png;base64,{!! $logbook->approvedSignature !!}" class="h-16 mt-5"
                        alt="Tanda Tangan Mengetahui">
                </div>
                <p class="font-semibold mt-1">{{ $logbook->approverBy->name ?? '-' }}</p>
            {{-- Cek 2: Apakah petugas penerima belum ttd? --}}
            @elseif(!$logbook->receivedSignature)
                {{-- JIKA BELUM: Tampilkan pesan tunggu --}}
                <div class="h-28 flex items-center justify-center">
                    <div class="mx-auto h-24 w-32 border rounded flex items-center justify-center text-xs text-gray-500 text-center p-2">
                        Menunggu Tanda Tangan Petugas Penerima
                    </div>
                </div>
                <p class="font-semibold mt-1">{{ $logbook->approverBy->name ?? '-' }}</p>
            @else
                {{-- JIKA SEMUA SIAP: Tampilkan form untuk ttd supervisor --}}
                <form
                    action="{{ route('supervisor.logbook.signature', [ 'logbookID' => $logbook->logbookID]) }}"
                    method="POST" onsubmit="return handleSignatureSubmit(event)">
                    @csrf
                    <div class="border-2 border-gray-200 rounded-xl p-4 my-2 ">
                        <div class="relative w-50 h-32 border border-gray-300 rounded-lg bg-white">
                            <canvas id="signature-canvas-receiver" class="w-full h-full"></canvas>
                        </div>
                        <input type="hidden" name="signature" id="signature-data-receiver">
                        <div class="flex justify-between items-center mt-2">
                            <span id="signature-status-receiver" class="text-xs text-gray-500">Belum ada tanda tangan</span>
                            <button type="button" class="text-sm text-blue-600 hover:text-blue-800"
                                onclick="clearSignatureReceiver()">Clear</button>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-semibold shadow transition">
                            Setujui & Tanda Tangan
                        </button>
                        @if($logbook->status === 'submitted')
                        <button type="button" onclick="openRejectModal()"
                            class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-semibold shadow transition">
                            Tolak Logbook
                        </button>
                        @endif
                    </div>
                </form>
            @endif
        </div>
    </div>
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tolak Logbook</h3>

            <form action="{{ route('supervisor.logbook.reject', $logbook->logbookID) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reject_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="rejected_reason"
                        id="rejected_reason"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="Masukkan alasan penolakan logbook..."
                        required></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        onclick="closeRejectModal()"
                        class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition">
                        Tolak Logbook
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('reject_reason').value = '';
    }


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
