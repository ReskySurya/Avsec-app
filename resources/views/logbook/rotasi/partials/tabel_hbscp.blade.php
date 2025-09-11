{{-- File: resources/views/logbook/rotasi/partials/tabel_hbscp.blade.php --}}

<!-- Desktop View -->
<div class="hidden lg:block bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
    <div class="overflow-x-auto scrollbar-thin">
        <table class="min-w-full text-sm text-center">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th rowspan="2" class="p-3 font-semibold text-gray-600 align-middle">No</th>
                    <th rowspan="2" class="p-3 font-semibold text-gray-600 align-middle text-left">Nama Officer</th>
                    <th colspan="2" class="p-3 font-semibold text-gray-600">Pengatur Flow</th>
                    <th colspan="2" class="p-3 font-semibold text-gray-600">Operator X-Ray</th>
                    <th colspan="2" class="p-3 font-semibold text-gray-600">Pemeriksaan Manual Bagasi</th>
                    <th colspan="2" class="p-3 font-semibold text-gray-600">Reunited</th>
                    <th rowspan="2" class="p-3 font-semibold text-gray-600 align-middle">Ket</th>
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
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($officerLog as $officerId => $data)
                <tr class="hover:bg-gray-50">
                    <td class="p-3">{{ $loop->iteration }}</td>
                    <td class="p-3 text-left font-medium">{{ $data['officer_name'] }}</td>

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

                    <td class="p-3 text-left text-xs">{!! implode('<br>',
                        array_unique(array_filter($data['keterangan']))) !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-gray-500 p-6">Tidak ada data entri HBSCP untuk ditampilkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if (isset($logbook) && $logbook->status === 'draft')
    <div class="p-4 bg-gray-50 border-t flex justify-end">
        {{-- Tombol DIUBAH di sini --}}
        <button type="button" @click="openFinishDialog = true"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 shadow-md flex items-center"
            title="Selesaikan semua shift">
            <i class="fas fa-check-circle mr-2"></i>Selesaikan Semua Shift
        </button>
    </div>
    @endif
</div>

<!-- Mobile/Tablet View -->
<div class="lg:hidden">
    @forelse($officerLog as $officerId => $data)
    <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-4 overflow-hidden">
        <!-- Officer Header -->
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 text-white p-4">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-lg">{{ $data['officer_name'] }}</h3>
                <span class="bg-white bg-opacity-20 px-2 py-1 rounded-full text-xs font-medium">
                    Officer #{{ $loop->iteration }}
                </span>
            </div>
        </div>

        <!-- Officer Activities -->
        <div class="p-4 space-y-4">

            <!-- Pengatur Flow -->
            @php $roleData = $data['roles']['pengatur_flow'] ?? []; @endphp
            @if (!empty($roleData))
            <div class="border-l-4 border-green-400 pl-4">
                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                    Pengatur Flow
                </h4>
                @foreach($roleData as $slot)
                <div class="bg-green-50 rounded-lg p-3 mb-2">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Mulai:</span>
                            <div class="font-medium text-gray-800">{!! $slot['start'] !!}</div>
                        </div>
                        <div>
                            <span class="text-gray-600">Selesai:</span>
                            <div class="font-medium text-gray-800">{!! $slot['end'] !!}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Operator X-Ray -->
            @php $roleData = $data['roles']['operator_xray'] ?? []; @endphp
            @if (!empty($roleData))
            <div class="border-l-4 border-purple-400 pl-4">
                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                    Operator X-Ray
                </h4>
                @foreach($roleData as $slot)
                <div class="bg-purple-50 rounded-lg p-3 mb-2">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Mulai:</span>
                            <div class="font-medium text-gray-800">{!! $slot['start'] !!}</div>
                        </div>
                        <div>
                            <span class="text-gray-600">Selesai:</span>
                            <div class="font-medium text-gray-800">{!! $slot['end'] !!}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Pemeriksaan Manual Bagasi -->
            @php $roleData = $data['roles']['manual_bagasi_petugas'] ?? []; @endphp
            @if (!empty($roleData))
            <div class="border-l-4 border-blue-400 pl-4">
                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                    Pemeriksaan Manual Bagasi
                </h4>
                @foreach($roleData as $slot)
                <div class="bg-blue-50 rounded-lg p-3 mb-2">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Mulai:</span>
                            <div class="font-medium text-gray-800">{!! $slot['start'] !!}</div>
                        </div>
                        <div>
                            <span class="text-gray-600">Selesai:</span>
                            <div class="font-medium text-gray-800">{!! $slot['end'] !!}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Reunited -->
            @php $roleData = $data['roles']['reunited'] ?? []; @endphp
            @if (!empty($roleData))
            <div class="border-l-4 border-orange-400 pl-4">
                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                    <span class="w-2 h-2 bg-orange-400 rounded-full mr-2"></span>
                    Reunited
                </h4>
                @foreach($roleData as $slot)
                <div class="bg-orange-50 rounded-lg p-3 mb-2">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Mulai:</span>
                            <div class="font-medium text-gray-800">{!! $slot['start'] !!}</div>
                        </div>
                        <div>
                            <span class="text-gray-600">Selesai:</span>
                            <div class="font-medium text-gray-800">{!! $slot['end'] !!}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Keterangan -->
            @php $keterangan = array_unique(array_filter($data['keterangan'])); @endphp
            @if (!empty($keterangan))
            <div class="border-l-4 border-gray-400 pl-4">
                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                    Keterangan
                </h4>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-700">
                        {!! implode('<br>', $keterangan) !!}
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
        <div class="text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-lg font-medium mb-2">Tidak ada data entri HBSCP</p>
            <p class="text-sm">Mulai tambahkan data rotasi untuk melihat logbook di sini.</p>
        </div>
    </div>
    @endforelse

    <!-- Mobile Action Button -->
    @if (isset($logbook) && $logbook->status === 'draft')
    <div class="sticky bottom-4 left-4 right-4 z-10">
        <button type="button" @click="openFinishDialog = true"
            class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-200 shadow-xl flex items-center justify-center transform hover:scale-[1.02]">
            <i class="fas fa-check-circle mr-3 text-lg"></i>
            <span>Selesaikan Semua Shift</span>
        </button>
    </div>
    @endif
</div>

{{-- Enhanced Mobile Styles --}}
<style>
    /* Responsive improvements */
    @media (max-width: 1023px) {
        .sticky {
            position: sticky;
        }

        /* Animation for mobile cards */
        .bg-white.rounded-lg.shadow-md {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .bg-white.rounded-lg.shadow-md:active {
            transform: scale(0.98);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Better spacing for mobile content */
        .space-y-4 > * + * {
            margin-top: 1rem;
        }

        /* Improved gradient headers */
        .bg-gradient-to-r {
            background-image: linear-gradient(to right, var(--tw-gradient-stops));
        }

        /* Enhanced color backgrounds for activities */
        .bg-blue-50 {
            background-color: rgba(59, 130, 246, 0.05);
        }

        .bg-green-50 {
            background-color: rgba(34, 197, 94, 0.05);
        }

        .bg-purple-50 {
            background-color: rgba(147, 51, 234, 0.05);
        }
        
        .bg-orange-50 {
            background-color: rgba(251, 146, 60, 0.05);
        }

        .bg-blue-100 {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .bg-green-100 {
            background-color: rgba(34, 197, 94, 0.1);
        }
    }

    /* Desktop table enhancements */
    @media (min-width: 1024px) {
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db #f9fafb;
        }

        .scrollbar-thin::-webkit-scrollbar {
            height: 8px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f9fafb;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    }
</style>
