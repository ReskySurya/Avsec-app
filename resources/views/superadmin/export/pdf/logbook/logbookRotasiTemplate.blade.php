<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forms - Rotasi</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>

<body class="m-0 p-0">
    @foreach($forms as $form)
    <div class="page-break-after">
        <div class="bg-white p-4" style="width: 210mm;">
            <div id="format" class="mx-auto">
                <div class="border-t-2 border-x-2 border-black bg-white shadow-md py-2">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="width: 20%; text-align: center; vertical-align: middle;">
                                    <img src="{{ public_path('images/airport-security-logo.png') }}" alt="Logo"
                                        style="width: 64px; height: 64px; display: inline-block;">
                                </td>
                                <td style="width: 60%; text-align: center; vertical-align: middle;">
                                    <h3 style="font-size: 12px; font-weight: bold; line-height: 1.3;">
                                        LOGBOOK HARIAN <br>
                                        CATATAN AKTIVITAS HARIAN <br>
                                        ROTASI PETUGAS
                                    </h3>
                                </td>
                                <td style="width: 20%; text-align: center; vertical-align: middle;">
                                    <img src="{{ public_path('images/injourney-API.png') }}" alt="Injourney Logo"
                                        style="width: 100px; height: 84px; display: inline-block;">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="border-2 border-black bg-white shadow p-4">
                    <!-- Informasi detail -->
                    <div class=" pt-3 mb-6">
                        <div class="grid grid-cols-2 gap-y-2 text-sm text-gray-700">
                            <p>HARI / TANGGAL
                                <span class="font-semibold">
                                    : {{ \Carbon\Carbon::parse($form->date)->locale('id')->translatedFormat('l, d F Y') }}
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
                                <p>Dibuat Oleh,</p>
                                <p>Airport Security Officer</p>
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
                                <p>Disetujui Oleh,</p>
                                <p>Airport Security Supervisor</p>
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
            </div>
        </div>
    @endforeach
</body>

</html>
