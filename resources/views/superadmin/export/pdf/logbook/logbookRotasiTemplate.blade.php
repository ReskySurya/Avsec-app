<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forms - Rotasi</title>
    <style>
        {!! file_get_contents(public_path('css/pdf.css')) !!}
    </style>
</head>

<body class="m-0 p-0">
    @foreach($forms as $form)
    <div class="border-t-2 border-x-2 border-black bg-white shadow-md p-6 m-6 page-break-after">
        @php
        $logoAirportBase64 = base64_encode(file_get_contents(public_path('images/airport-security-logo.png')));
        $logoInjourneyBase64 = base64_encode(file_get_contents(public_path('images/injourney-API.png')));
        @endphp
        <div class="flex flex-col sm:flex-row items-center justify-between">
            <img src="data:image/png;base64,{{ $logoAirportBase64 }}" alt="Logo" class="w-20 h-20 mb-2 sm:mb-0">
            <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
                LOGBOOK HARIAN <br>
                CATATAN AKTIVITAS HARIAN <br>
                ROTASI PETUGAS
            </h1>
            <img src="data:image/png;base64,{{ $logoInjourneyBase64 }}" alt="Injourney Logo" class="w-20 h-20 mt-2 sm:mt-0">
        </div>

        <!-- Informasi detail -->
        <div class="border-t border-gray-300 pt-3 mt-4 mb-6">
            <div class="grid grid-cols-2 gap-y-2 text-sm text-gray-700">
                <p>HARI / TANGGAL
                    <span class="font-semibold">
                        : {{ \Carbon\Carbon::parse($form->date)->translatedFormat('l, d F Y') }}
                    </span>
                </p>
                <p>TIPE ROTASI <span class="font-semibold">: {{ strtoupper($form->type) }}</span></p>
                <p>STATUS <span class="font-semibold">: {{ strtoupper($form->status ?? 'SUBMITTED') }}</span></p>
            </div>
        </div>

        <!-- Tabel Detail Rotasi Lengkap -->
        <div class="flex justify-center mb-2">
            <p class="font-semibold self-center">DETAIL AKTIVITAS ROTASI</p>
        </div>
        <table class="w-full border border-black mb-6 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-black px-1 py-1" rowspan="2">No</th>
                    <th class="border border-black px-1 py-1" rowspan="2">Nama Officer</th>
                    <th class="border border-black px-1 py-1" colspan="2">Pemeriksa Dokumen</th>
                    <th class="border border-black px-1 py-1" colspan="2">Pengatur Flow</th>
                    <th class="border border-black px-1 py-1" colspan="2">Operator X-Ray</th>
                    <th class="border border-black px-1 py-1" colspan="4">Pemeriksaan Orang Manual/HHMD</th>
                    <th class="border border-black px-1 py-1" colspan="4">Pemeriksa Manual Kabin</th>
                    <th class="border border-black px-1 py-1" rowspan="2">Keterangan</th>
                </tr>
                <tr class="bg-gray-100">
                    <th class="border border-black px-1 py-1">Start</th>
                    <th class="border border-black px-1 py-1">End</th>
                    <th class="border border-black px-1 py-1">Start</th>
                    <th class="border border-black px-1 py-1">End</th>
                    <th class="border border-black px-1 py-1">Start</th>
                    <th class="border border-black px-1 py-1">End</th>
                    <th class="border border-black px-1 py-1">Start</th>
                    <th class="border border-black px-1 py-1">End</th>
                    <th class="border border-black px-1 py-1">R</th>
                    <th class="border border-black px-1 py-1">U</th>
                    <th class="border border-black px-1 py-1">Start</th>
                    <th class="border border-black px-1 py-1">End</th>
                    <th class="border border-black px-1 py-1">R</th>
                    <th class="border border-black px-1 py-1">U</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($form->officerLog) && count($form->officerLog) > 0)
                @foreach($form->officerLog as $officerId => $data)
                <tr>
                    <td class="border border-black px-2 py-1 text-center">{{ $loop->iteration }}</td>
                    <td class="border border-black px-2 py-1 text-left font-semibold">{{ $data['officer_name'] }}</td>

                    <!-- Pemeriksaan Dokumen -->
                    @php $roleData = $data['roles']['pemeriksaan_dokumen'] ?? []; @endphp
                    <td class="border border-black px-2 py-1 text-center">@foreach($roleData as $slot) {{ strip_tags($slot['start']) }}<br> @endforeach</td>
                    <td class="border border-black px-2 py-1 text-center">@foreach($roleData as $slot) {{ strip_tags($slot['end']) }}<br> @endforeach</td>

                    <!-- Pengatur Flow -->
                    @php $roleData = $data['roles']['pengatur_flow'] ?? []; @endphp
                    <td class="border border-black px-2 py-1 text-center">@foreach($roleData as $slot) {{ strip_tags($slot['start']) }}<br> @endforeach</td>
                    <td class="border border-black px-2 py-1 text-center">@foreach($roleData as $slot) {{ strip_tags($slot['end']) }}<br> @endforeach</td>

                    <!-- Operator X-Ray -->
                    @php $roleData = $data['roles']['operator_xray'] ?? []; @endphp
                    <td class="border border-black px-2 py-1 text-center">@foreach($roleData as $slot) {{ strip_tags($slot['start']) }}<br> @endforeach</td>
                    <td class="border border-black px-2 py-1 text-center">@foreach($roleData as $slot) {{ strip_tags($slot['end']) }}<br> @endforeach</td>

                    <!-- Pemeriksaan Orang Manual/HHMD -->
                    @php $roleData = $data['roles']['hhmd_petugas'] ?? []; @endphp
                    <td class="border border-black px-2 py-1 text-center">@foreach($roleData as $slot) {{ strip_tags($slot['start']) }}<br> @endforeach</td>
                    <td class="border border-black px-2 py-1 text-center">@foreach($roleData as $slot) {{ strip_tags($slot['end']) }}<br> @endforeach</td>
                    <td class="border border-black px-2 py-1 text-center font-semibold text-blue-600">
                        @foreach($roleData as $slot)
                        {{ $slot['hhmd_random'] ?? '-' }}<br>
                        @endforeach
                    </td>
                    <td class="border border-black px-2 py-1 text-center font-semibold text-blue-600">
                        @foreach($roleData as $slot)
                        {{ $slot['hhmd_unpredictable'] ?? '-' }}<br>
                        @endforeach
                    </td>

                    <!-- Pemeriksa Manual Kabin -->
                    @php $roleData = $data['roles']['manual_kabin_petugas'] ?? []; @endphp
                    <td class="border border-black px-2 py-1 text-center">@foreach($roleData as $slot) {{ strip_tags($slot['start']) }}<br> @endforeach</td>
                    <td class="border border-black px-2 py-1 text-center">@foreach($roleData as $slot) {{ strip_tags($slot['end']) }}<br> @endforeach</td>
                    <td class="border border-black px-2 py-1 text-center font-semibold text-green-600">
                        @foreach($roleData as $slot)
                        {{ $slot['cek_random_barang'] ?? '-' }}<br>
                        @endforeach
                    </td>
                    <td class="border border-black px-2 py-1 text-center font-semibold text-green-600">
                        @foreach($roleData as $slot)
                        {{ $slot['barang_unpredictable'] ?? '-' }}<br>
                        @endforeach
                    </td>

                    <!-- Keterangan -->
                    <td class="border border-black px-2 py-1 text-left">
                        {{ implode(', ', array_unique(array_filter($data['keterangan']))) }}
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td class="border border-black px-2 py-1 text-center italic" colspan="17">
                        Tidak ada detail entri untuk logbook ini
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Catatan -->
        @if(isset($form->notes) && $form->notes)
        <div class="flex justify-center mb-2">
            <p class="font-semibold self-center">CATATAN</p>
        </div>
        <div class="border border-black p-4 mb-6 text-sm min-h-20">
            <p>{{ $form->notes }}</p>
        </div>
        @endif

        <!-- Tanda Tangan -->
        <div class="mt-10 text-center text-sm">
            <div class="grid grid-cols-2 gap-4">
                <!-- Kiri: Yang Menyerahkan -->
                <div>
                    <p>Dibuat Oleh</p>
                    <div class="h-16 flex items-center justify-center">
                        @if($form->submittedSignature)
                        <img src="{{ $form->submittedSignature }}" class="h-16 mt-5" alt="Tanda Tangan">
                        @else
                        <span class="italic text-gray-400">Belum tanda tangan</span>
                        @endif
                    </div>
                    <p class="font-semibold mt-1">{{ $form->creatorName ?? 'N/A' }}</p>
                </div>

                <!-- Kanan: Yang Menyetujui -->
                <div>
                    <p>Disetujui Oleh</p>
                    <div class="h-16 flex items-center justify-center">
                        @if($form->approvedSignature)
                        <img src="{{ $form->approvedSignature }}" class="h-16 mt-5" alt="Tanda Tangan">
                        @else
                        <span class="italic text-gray-400">Belum tanda tangan</span>
                        @endif
                    </div>
                    <p class="font-semibold mt-1">{{ $form->approverName ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</body>

</html>