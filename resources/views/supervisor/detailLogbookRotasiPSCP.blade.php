@extends('layouts.app')

@section('content')
<div class="container-lg mx-auto px-4 py-6">
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
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-black">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-center">
                <thead class="bg-gray-50 border-b border-black">
                    <tr class="border-b border-black">
                        <th rowspan="2" class="p-3 font-semibold text-gray-600 tracking-wider border-r border-black align-middle">No</th>
                        <th rowspan="2" class="p-3 font-semibold text-gray-600 tracking-wider border-x border-black align-middle">
                            Nama Officer</th>
                        <th colspan="2" class="p-3 font-semibold text-gray-600 tracking-wider border-x border-black">Pemeriksa Dokumen</th>
                        <th colspan="2" class="p-3 font-semibold text-gray-600 tracking-wider border-x border-black">Pengatur Flow</th>
                        <th colspan="2" class="p-3 font-semibold text-gray-600 tracking-wider border-x border-black">Operator X-Ray</th>
                        <th colspan="4" class="p-3 font-semibold text-gray-600 tracking-wider border-x border-black">Pemeriksaan Orang
                            Manual/HHMD</th>
                        <th colspan="4" class="p-3 font-semibold text-gray-600 tracking-wider border-x border-black">Pemeriksa Manual Kabin
                        </th>
                        <th rowspan="2" class="p-3 font-semibold text-gray-600 tracking-wider border-l border-black align-middle">Keterangan
                        </th>
                    </tr>
                    <tr class="bg-gray-100">
                        {{-- Kolom untuk Peran dengan Start/End --}}
                        <th class="p-2 font-medium text-gray-500 border-x border-black">Start</th>
                        <th class="p-2 font-medium text-gray-500 border-x border-black">End</th>
                        <th class="p-2 font-medium text-gray-500 border-x border-black">Start</th>
                        <th class="p-2 font-medium text-gray-500 border-x border-black">End</th>
                        <th class="p-2 font-medium text-gray-500 border-x border-black">Start</th>
                        <th class="p-2 font-medium text-gray-500 border-x border-black">End</th>
                        {{-- Kolom untuk HHMD --}}
                        <th class="p-2 font-medium text-gray-500 border-x border-black">Start</th>
                        <th class="p-2 font-medium text-gray-500 border-x border-black">End</th>
                        <th class="p-2 font-medium text-gray-500 border-x border-black">R</th>
                        <th class="p-2 font-medium text-gray-500 border-x border-black">U</th>
                        {{-- Kolom untuk Manual Kabin --}}
                        <th class="p-2 font-medium text-gray-500 border-x border-black">Start</th>
                        <th class="p-2 font-medium text-gray-500 border-x border-black">End</th>
                        <th class="p-2 font-medium text-gray-500 border-x border-black">R</th>
                        <th class="p-2 font-medium text-gray-500 border-x border-black">U</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($officerLog as $officerId => $data)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-3 text-gray-500 border-r border-black">{{ $loop->iteration }}</td>
                        <td class="p-3 text-gray-800 text-left font-medium border-x border-black">{{ $data['officer_name'] }}</td>

                        {{-- Kolom Pemeriksaan Dokumen --}}
                        @php $roleData = $data['roles']['pemeriksaan_dokumen'] ?? []; @endphp
                        <td class="p-3 border-x border-black">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                        <td class="p-3 border-x border-black">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>

                        {{-- Kolom Pengatur Flow --}}
                        @php $roleData = $data['roles']['pengatur_flow'] ?? []; @endphp
                        <td class="p-3 border-x border-black">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                        <td class="p-3 border-x border-black">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>

                        {{-- Kolom Operator X-Ray --}}
                        @php $roleData = $data['roles']['operator_xray'] ?? []; @endphp
                        <td class="p-3 border-x border-black">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                        <td class="p-3 border-x border-black">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>

                        {{-- Kolom Pemeriksaan Orang Manual/HHMD (dengan Counter) --}}
                        @php $roleData = $data['roles']['hhmd_petugas'] ?? []; @endphp
                        <td class="p-3 border-x border-black">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                        <td class="p-3 border-x border-black">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>
                        <td class="p-3 border-x border-black font-semibold text-blue-600">
                            @foreach($roleData as $slot)
                            {{-- Ambil nilai counter dari slot --}}
                            {{ $slot['hhmd_random'] ?? '-' }}<br>
                            @endforeach
                        </td>
                        <td class="p-3 font-semibold text-blue-600">
                            @foreach($roleData as $slot)
                            {{ $slot['hhmd_unpredictable'] ?? '-' }}<br>
                            @endforeach
                        </td>

                        {{-- Kolom Pemeriksa Manual Kabin (dengan Counter) --}}
                        @php $roleData = $data['roles']['manual_kabin_petugas'] ?? []; @endphp
                        <td class="p-3 border-x border-black">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                        <td class="p-3 border-x border-black">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>
                        <td class="p-3 border-x border-black font-semibold text-green-600">
                            @foreach($roleData as $slot)
                            {{-- Ambil nilai counter dari slot --}}
                            {{ $slot['cek_random_barang'] ?? '-' }}<br>
                            @endforeach
                        </td>
                        <td class="p-3 font-semibold text-green-600">
                            @foreach($roleData as $slot)
                            {{ $slot['barang_unpredictable'] ?? '-' }}<br>
                            @endforeach
                        </td>

                        {{-- Kolom Keterangan --}}
                        <td class="p-3 border-l border-black text-gray-600 text-left">
                            {!! implode('<br>', array_unique(array_filter($data['keterangan']))) !!}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- Sesuaikan colspan dengan jumlah total kolom di header --}}
                        <td colspan="15" class="text-center text-gray-500 p-6">
                            <p>Tidak ada detail entri untuk logbook ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tombol Kembali --}}
    <div class="mt-6">
        <a href="{{ url()->previous() }}"
            class="inline-flex items-center px-4 py-2 bg-white text-gray-700 rounded-lg border hover:bg-gray-100 transition-colors font-medium text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>
</div>
@endsection
