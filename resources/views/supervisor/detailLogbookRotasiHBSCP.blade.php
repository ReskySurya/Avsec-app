@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Detail Logbook Rotasi HBSCP</h1>
    </div>

    {{-- Informasi Logbook Utama (Sama seperti view lama) --}}
    <div class="bg-white p-6 rounded-lg shadow-md mb-6 border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-600"><span class="font-semibold text-gray-900">ID Logbook:</span> {{ $logbook->id }}
                </p>
                <p class="text-gray-600"><span class="font-semibold text-gray-900">Tanggal:</span> {{
                    \Carbon\Carbon::parse($logbook->date)->format('d F Y') }}</p>
                <p class="text-gray-600"><span class="font-semibold text-gray-900">Dibuat oleh:</span> {{
                    $logbook->creator?->display_name ?? '-' }}</p>
            </div>
            <div>
                <p class="flex items-center text-gray-600"><span class="font-semibold text-gray-900 mr-2">Status:</span>
                    <span class="px-3 py-1 text-xs font-medium rounded-full
                        @if($logbook->status === 'approved') bg-green-100 text-green-800
                        @elseif($logbook->status === 'submitted') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($logbook->status) ?? '-' }}
                    </span>
                </p>
                @if($logbook->approved_by)
                <p class="text-gray-600"><span class="font-semibold text-gray-900">Disetujui oleh:</span> {{
                    $logbook->approver?->display_name ?? '-' }}</p>
                <p class="text-gray-600"><span class="font-semibold text-gray-900">Tgl Persetujuan:</span> {{
                    $logbook->approved_at ? \Carbon\Carbon::parse($logbook->approved_at)->format('d/m/Y H:i') : '-' }}
                </p>
                @endif
            </div>
        </div>
        @if($logbook->notes)
        <p class="mt-4 text-sm text-gray-600"><span class="font-semibold text-gray-900">Catatan:</span> {{
            $logbook->notes }}</p>
        @endif
    </div>

    {{-- Tabel Detail Logbook Baru --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-center">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th rowspan="2" class="p-3 font-semibold text-gray-600 tracking-wider align-middle">No</th>
                        <th rowspan="2" class="p-3 font-semibold text-gray-600 tracking-wider align-middle text-left">
                            Nama Officer</th>
                        <th colspan="2" class="p-3 font-semibold text-gray-600 tracking-wider">Pengatur Flow</th>
                        <th colspan="2" class="p-3 font-semibold text-gray-600 tracking-wider">Operator X-Ray</th>
                        <th colspan="2" class="p-3 font-semibold text-gray-600 tracking-wider">Pemeriksaan Manual Bagasi
                        </th>
                        <th colspan="2" class="p-3 font-semibold text-gray-600 tracking-wider">Reunited</th>
                        <th rowspan="2" class="p-3 font-semibold text-gray-600 tracking-wider align-middle">Ket</th>
                    </tr>
                    <tr class="bg-gray-100">
                        <th class="p-2 font-medium text-gray-500">Start</th>
                        <th class="p-2 font-medium text-gray-500">End</th>
                        <th class="p-2 font-medium text-gray-500">Start</th>
                        <th class="p-2 font-medium text-gray-500">End</th>
                        <th class="p-2 font-medium text-gray-500">Start</th>
                        <th class="p-2 font-medium text-gray-500">End</th>
                        <th class="p-2 font-medium text-gray-500">Start</th>
                        <th class="p-2 font-medium text-gray-500">End</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($officerLog as $data)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-3 text-gray-500">{{ $loop->iteration }}</td>
                        <td class="p-3 text-gray-800 text-left font-medium">{{ $data['officer_name'] }}</td>

                        {{-- Kolom Pengatur Flow --}}
                        @php $roleData = $data['roles']['pengatur_flow'] ?? []; @endphp
                        <td class="p-3">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                        <td class="p-3">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>

                        {{-- Kolom Operator X-Ray --}}
                        @php $roleData = $data['roles']['operator_xray'] ?? []; @endphp
                        <td class="p-3">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                        <td class="p-3">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>

                        {{-- Kolom Pemeriksaan Manual Bagasi --}}
                        @php $roleData = $data['roles']['manual_bagasi_petugas'] ?? []; @endphp
                        <td class="p-3">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                        <td class="p-3">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>

                        {{-- Kolom Reunited --}}
                        @php $roleData = $data['roles']['reunited'] ?? []; @endphp
                        <td class="p-3">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                        <td class="p-3">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>

                        {{-- Kolom Keterangan --}}
                        <td class="p-3 text-gray-600 text-left">
                            {{-- Menggabungkan keterangan unik agar tidak duplikat --}}
                            {!! implode('<br>', array_unique(array_filter($data['keterangan']))) !!}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-gray-500 p-6">
                            <p>Tidak ada detail entri untuk logbook ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Area Tanda Tangan --}}
    <div class="p-6 bg-white">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Tanda Tangan Kiri: Dibuat Oleh (Tidak Berubah) --}}
            <div class="text-center">
                <div class="mb-2">
                    <p class="text-sm font-semibold text-gray-700">Dibuat Oleh</p>
                    <p class="text-xs text-gray-500">{{ $logbook->creator?->display_name ?? '-' }}</p>
                </div>
                <div class="border border-gray-300 rounded-lg h-24 flex items-center justify-center bg-gray-50 mb-2">
                    @if($logbook->submittedSignature)
                    <img src="{!! $logbook->submittedSignature !!}" alt="Submitted Signature"
                        class="max-h-20 max-w-full object-contain">
                    @else
                    <span class="text-gray-400 text-xs">Belum ada tanda tangan</span>
                    @endif
                </div>

            </div>

            {{-- Tanda Tangan Kanan: Disetujui Oleh (Supervisor) --}}
            <div class="text-center">
                <div class="mb-2">
                    <p class="text-sm font-semibold text-gray-700">Disetujui Oleh</p>
                    <p class="text-xs text-gray-500">{{ $logbook->approver?->display_name ?? '-' }}</p>
                </div>

                @if($logbook->approvedSignature)
                {{-- JIKA SUDAH ADA TANDA TANGAN: Tampilkan gambar --}}
                <div class="border border-gray-300 rounded-lg h-24 flex items-center justify-center bg-gray-50 mb-2">
                    <img src="{!! $logbook->approvedSignature !!}" alt="Approved Signature"
                        class="max-h-20 max-w-full object-contain">
                </div>
                @else
                {{-- JIKA BELUM ADA Tampilkan form canvas --}}
                <form action="{{ route('supervisor.logbook-rotasi.approved', $logbook->id) }}" method="POST"
                    onsubmit="return validateApprovalSignature(event)">
                    @csrf
                    {{-- Wrapper untuk canvas agar ukurannya konsisten --}}
                    <div class="border border-gray-300 rounded-lg h-24 mb-2 bg-white">
                        <canvas id="approvedSignatureCanvas" class="w-full h-full"></canvas>
                    </div>

                    {{-- Input tersembunyi untuk menyimpan data base64 tanda tangan --}}
                    <input type="hidden" name="signature" id="signature-data-approver">

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-center items-center space-x-2 mb-2">
                        <button type="button" onclick="clearApprovalSignature()"
                            class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600 transition-colors">
                            Clear
                        </button>
                        <button type="submit"
                            class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors">
                            Simpan & Setujui
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Tombol Kembali --}}
    <div class="mt-6">
        <a href="{{ route('supervisor.logbook-rotasi.list') }}"
            class="inline-flex items-center px-4 py-2 bg-white text-gray-700 rounded-lg border hover:bg-gray-100 transition-colors font-medium text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cari canvas di dalam dokumen
    const canvas = document.getElementById('approvedSignatureCanvas');

    // Hanya jalankan script jika canvas-nya ada di halaman
    if (canvas) {
        // Inisialisasi SignaturePad
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)', // Putih
            penColor: 'rgb(0, 0, 0)' // Hitam
        });

        // Fungsi untuk resize canvas agar responsif
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); // Hapus ttd saat resize
        }

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas(); // Panggil sekali saat load

        // Simpan data ttd ke hidden input setiap kali selesai menulis
        signaturePad.onEnd = () => {
            const signatureDataInput = document.getElementById('signature-data-approver');
            if (!signaturePad.isEmpty()) {
                // 'image/png' adalah format yang umum dan didukung luas
                signatureDataInput.value = signaturePad.toDataURL('image/png');
            } else {
                signatureDataInput.value = '';
            }
        };

        // Buat fungsi clear menjadi global agar bisa dipanggil dari tombol
        window.clearApprovalSignature = function() {
            signaturePad.clear();
            document.getElementById('signature-data-approver').value = '';
        }

        // Buat fungsi validasi menjadi global agar bisa dipanggil dari form
        window.validateApprovalSignature = function(event) {
            if (signaturePad.isEmpty()) {
                alert("Tanda tangan persetujuan tidak boleh kosong.");
                event.preventDefault(); // Mencegah form dikirim
                return false;
            }
            // Nonaktifkan tombol saat submit untuk mencegah double-click
            event.target.querySelector('button[type="submit"]').disabled = true;
            event.target.querySelector('button[type="submit"]').textContent = 'Menyimpan...';
            return true;
        }
    }
});
</script>
@endsection
