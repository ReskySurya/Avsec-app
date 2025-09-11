@extends('layouts.app')

@section('title', 'Detail Logbook Rotasi PSCP')

@section('content')
<div class="mx-auto p-0 sm:p-6 min-h-screen pt-5 lg:pt-20">
    <div class="bg-white shadow-xl rounded-none sm:rounded-2xl overflow-hidden border-0 sm:border border-gray-100">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-4 sm:px-6 py-4 sm:py-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold">Detail Logbook Rotasi PSCP</h3>
                    <p class="text-blue-100 text-sm sm:text-base">ID Logbook: {{ $logbook->id }}</p>
                </div>
                <a href="{{ route('supervisor.logbook-rotasi.list') }}" class="hidden sm:inline-flex items-center px-4 py-2 bg-white bg-opacity-20 text-white rounded-lg hover:bg-opacity-30 transition-colors font-medium text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali ke Daftar
                </a>
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-6">
            {{-- Logbook Info Card --}}
            <div class="bg-gray-50 p-4 sm:p-6 rounded-xl border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Tanggal</p>
                        <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($logbook->date)->isoFormat('dddd, D MMMM Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Dibuat Oleh</p>
                        <p class="font-semibold text-gray-800">{{ $logbook->creator?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p class="font-semibold text-gray-800 flex items-center">
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                @if($logbook->status === 'approved') bg-green-100 text-green-800
                                @elseif($logbook->status === 'submitted') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($logbook->status) }}
                            </span>
                        </p>
                    </div>
                    @if($logbook->approved_by)
                    <div>
                        <p class="text-gray-500">Disetujui Oleh</p>
                        <p class="font-semibold text-gray-800">{{ $logbook->approver?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tgl Persetujuan</p>
                        <p class="font-semibold text-gray-800">{{ $logbook->approved_at ? \Carbon\Carbon::parse($logbook->approved_at)->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Officer Rotation Table (using the partial's structure) --}}
            @include('logbook.rotasi.partials.tabel_pscp', ['officerLog' => $officerLog, 'logbook' => $logbook])

            {{-- Signature Area --}}
            <div class="bg-white p-4 sm:p-6 rounded-xl border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tanda Tangan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Submitted By --}}
                    <div class="text-center">
                        <p class="font-semibold text-gray-700">Dibuat Oleh</p>
                        <div class="mt-2 border border-gray-200 rounded-lg h-32 flex items-center justify-center bg-gray-50">
                            @if($logbook->submittedSignature)
                            <img src="{!! $logbook->submittedSignature !!}" alt="Submitted Signature" class="max-h-28">
                            @else
                            <span class="text-gray-400">Tidak ada Tanda Tangan</span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-600">{{ $logbook->creator?->name ?? '-' }}</p>
                    </div>

                    {{-- Approved By --}}
                    <div class="text-center">
                        <p class="font-semibold text-gray-700">Disetujui Oleh</p>
                        <div class="mt-2 border border-gray-200 rounded-lg h-32 flex items-center justify-center bg-gray-50">
                            @if($logbook->approvedSignature)
                            <img src="{!! $logbook->approvedSignature !!}" alt="Approved Signature" class="max-h-28">
                            @elseif($logbook->status === 'submitted' && auth()->user()->isSupervisor())
                            {{-- Show signature pad only if submitted and user is supervisor --}}
                            <form action="{{ route('supervisor.logbook-rotasi.approved', $logbook->id) }}" method="POST" onsubmit="return validateApprovalSignature(event)" class="w-full">
                                @csrf
                                <div class="bg-white h-32 rounded-lg">
                                    <canvas id="approvedSignatureCanvas" class="w-full h-full"></canvas>
                                </div>
                                <input type="hidden" name="signature" id="signature-data-approver">
                                <div class="flex justify-center items-center space-x-2 mt-2">
                                    <button type="button" onclick="clearApprovalSignature()" class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">Clear</button>
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">Setujui</button>
                                </div>
                            </form>
                            @else
                            <span class="text-gray-400">Belum Disetujui</span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-600">{{ $logbook->approver?->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
         <div class="mt-6 px-4 sm:px-0">
            <a href="{{ route('supervisor.logbook-rotasi.list') }}" class="inline-flex sm:hidden items-center px-4 py-2 bg-white text-gray-700 rounded-lg border hover:bg-gray-100 font-medium text-sm w-full justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('approvedSignatureCanvas');
    if (canvas) {
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        signaturePad.onEnd = () => {
            const signatureDataInput = document.getElementById('signature-data-approver');
            if (!signaturePad.isEmpty()) {
                signatureDataInput.value = signaturePad.toDataURL('image/png');
            } else {
                signatureDataInput.value = '';
            }
        };

        window.clearApprovalSignature = function() {
            signaturePad.clear();
            document.getElementById('signature-data-approver').value = '';
        }

        window.validateApprovalSignature = function(event) {
            if (signaturePad.isEmpty()) {
                alert("Tanda tangan persetujuan tidak boleh kosong.");
                event.preventDefault();
                return false;
            }
            event.target.querySelector('button[type="submit"]').disabled = true;
            event.target.querySelector('button[type="submit"]').textContent = 'Menyimpan...';
            return true;
        }
    }
});
</script>
@endsection