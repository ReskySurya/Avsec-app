<div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-center">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th rowspan="2" class="p-3 font-semibold text-gray-600 align-middle">No</th>
                    <th rowspan="2" class="p-3 font-semibold text-gray-600 align-middle text-left">Nama Officer</th>
                    <th colspan="2" class="p-3 font-semibold text-gray-600">Pemeriksa Dokumen</th>
                    <th colspan="2" class="p-3 font-semibold text-gray-600">Pengatur Flow</th>
                    <th colspan="2" class="p-3 font-semibold text-gray-600">Operator X-Ray</th>
                    <th colspan="4" class="p-3 font-semibold text-gray-600">Pemeriksaan Manual/HHMD</th>
                    <th colspan="4" class="p-3 font-semibold text-gray-600">Pemeriksa Manual Kabin</th>
                    <th rowspan="2" class="p-3 font-semibold text-gray-600 align-middle">Keterangan</th>
                </tr>
                <tr class="bg-gray-100">
                    <th class="p-2 font-medium text-gray-500">Mulai</th>
                    <th class="p-2 font-medium text-gray-500">Selesai</th>
                    <th class="p-2 font-medium text-gray-500">Mulai</th>
                    <th class="p-2 font-medium text-gray-500">Selesai</th>
                    <th class="p-2 font-medium text-gray-500">Mulai</th>
                    <th class="p-2 font-medium text-gray-500">Selesai</th>
                    <th class="p-2 font-medium text-gray-500">Mulai</th>
                    <th class="p-2 font-medium text-gray-500">Selesai</th>
                    <th class="p-2 font-medium text-gray-500" title="Random">R</th>
                    <th class="p-2 font-medium text-gray-500" title="Unpredictable">U</th>
                    <th class="p-2 font-medium text-gray-500">Mulai</th>
                    <th class="p-2 font-medium text-gray-500">Selesai</th>
                    <th class="p-2 font-medium text-gray-500" title="Random">R</th>
                    <th class="p-2 font-medium text-gray-500" title="Unpredictable">U</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($officerLog as $officerId => $data)
                <tr class="hover:bg-gray-50">
                    <td class="p-3">{{ $loop->iteration }}</td>
                    <td class="p-3 text-left font-medium">{{ $data['officer_name'] }}</td>

                    {{-- Pemeriksaan Dokumen --}}
                    @php $roleData = $data['roles']['pemeriksaan_dokumen'] ?? []; @endphp
                    <td class="p-3">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                    <td class="p-3">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>

                    {{-- Pengatur Flow --}}
                    @php $roleData = $data['roles']['pengatur_flow'] ?? []; @endphp
                    <td class="p-3">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                    <td class="p-3">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>

                    {{-- Operator X-Ray --}}
                    @php $roleData = $data['roles']['operator_xray'] ?? []; @endphp
                    <td class="p-3">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                    <td class="p-3">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>

                    {{-- HHMD (dengan Counter) --}}
                    @php $roleData = $data['roles']['hhmd_petugas'] ?? []; @endphp
                    <td class="p-3">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                    <td class="p-3">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>
                    <td class="p-3 font-semibold text-blue-600">@foreach($roleData as $slot) {{ $slot['hhmd_random'] ??
                        '-' }}<br> @endforeach</td>
                    <td class="p-3 font-semibold text-blue-600">@foreach($roleData as $slot) {{
                        $slot['hhmd_unpredictable'] ?? '-' }}<br> @endforeach</td>

                    {{-- Manual Kabin (dengan Counter) --}}
                    @php $roleData = $data['roles']['manual_kabin_petugas'] ?? []; @endphp
                    <td class="p-3">@foreach($roleData as $slot) {!! $slot['start'] !!}<br> @endforeach</td>
                    <td class="p-3">@foreach($roleData as $slot) {!! $slot['end'] !!}<br> @endforeach</td>
                    <td class="p-3 font-semibold text-green-600">@foreach($roleData as $slot) {{
                        $slot['cek_random_barang'] ?? '-' }}<br> @endforeach</td>
                    <td class="p-3 font-semibold text-green-600">@foreach($roleData as $slot) {{
                        $slot['barang_unpredictable'] ?? '-' }}<br> @endforeach</td>

                    <td class="p-3 text-left text-xs">{!! implode('<br>',
                        array_unique(array_filter($data['keterangan']))) !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="18" class="text-center text-gray-500 p-6">Tidak ada data entri PSCP untuk ditampilkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (isset($logbook) && $logbook->status === 'draft')
        <div class="p-4 bg-gray-50 border-t flex justify-end">
            {{-- Tombol DIUBAH di sini --}}
            <button type="button"
                    @click="openFinishDialog = true"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 shadow-md flex items-center"
                    title="Selesaikan semua shift">
                <i class="fas fa-check-circle mr-2"></i>Selesaikan Semua Shift
            </button>
        </div>
    @endif
</div>

{{-- CSS untuk responsif pada mobile --}}
<style>
    @media (max-width: 768px) {
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }

        .min-w-full th,
        .min-w-full td {
            white-space: nowrap;
            min-width: 80px;
        }

        .min-w-full th:first-child,
        .min-w-full td:first-child {
            position: sticky;
            left: 0;
            background: white;
            z-index: 10;
        }

        .min-w-full th:nth-child(2),
        .min-w-full td:nth-child(2) {
            position: sticky;
            left: 60px;
            /* Sesuaikan dengan lebar kolom No */
            background: white;
            z-index: 10;
            min-width: 120px;
        }
    }
</style>
