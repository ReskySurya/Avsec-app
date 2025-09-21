<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forms - Rotasi</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 8mm;
        }

        /* Custom styles untuk print dan landscape optimization */
        .landscape-container {
            max-width: 280mm;
            font-size: 24px;
        }

        .table-header-text {
            font-size: 8px;
            line-height: 1.2;
        }

        .table-cell-text {
            font-size: 9px;
            line-height: 1.3;
        }

        .info-text {
            font-size: 10px;
        }

        .title-text {
            font-size: 12px;
        }

        .signature-text {
            font-size: 9px;
        }

        .signature-container {
            min-height: 45px;
        }

        @media print {
            .landscape-container {
                font-size: 16px;
            }
            .table-header-text {
                font-size: 14px;
            }
            .table-cell-text {
                font-size: 14px;
            }
            .info-text {
                font-size: 9px;
            }
            .title-text {
                font-size: 16px;
            }
            .signature-text {
                font-size: 10px;
            }
        }
    </style>
</head>

<body class="m-0 p-0 font-sans">
    @foreach($forms as $form)
    <div class="page-break-after">
        <div class="landscape-container mx-auto bg-white">

            <!-- Header Section -->
            <div class="mb-2">
                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="w-1/6 text-center p-2">
                                <img src="{{ public_path('images/airport-security-logo.png') }}" alt="Logo"
                                     class="w-10 h-10 mx-auto">
                            </td>
                            <td class="w-4/6 text-center p-2">
                                <h3 class="title-text font-bold leading-tight">
                                    LOGBOOK HARIAN <br>
                                    CATATAN AKTIVITAS HARIAN <br>
                                    ROTASI PETUGAS
                                </h3>
                            </td>
                            <td class="w-1/6 text-center p-2">
                                <img src="{{ public_path('images/injourney-API.png') }}" alt="Injourney Logo"
                                     class="w-12 h-10 mx-auto">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Info Section -->
            <div class="flex justify-between items-start mb-3 info-text px-2">
                <div class="space-y-1">
                    <p><span class="font-semibold">HARI / TANGGAL:</span>
                        {{ \Carbon\Carbon::parse($form->date)->locale('id')->translatedFormat('l, d F Y') }}
                    </p>
                    <p><span class="font-semibold">STATUS:</span> {{ strtoupper($form->status ?? 'SUBMITTED') }}</p>
                </div>
                <div>
                    <p><span class="font-semibold">TIPE ROTASI:</span> {{ strtoupper($form->type) }}</p>
                </div>
            </div>

            <!-- Table Title -->
            <div class="text-center mb-2">
                <p class="font-bold title-text">DETAIL AKTIVITAS ROTASI {{ strtoupper($form->type) }}</p>
            </div>

            <!-- Main Table -->
            <div class="border border-black">
                <table class="w-full border-collapse">
                    <!-- Table Header -->
                    <thead class="bg-gray-200">
                        <tr>
                            <th rowspan="2" class="border border-black px-1 py-1 table-header-text w-8">No</th>
                            <th rowspan="2" class="border border-black px-1 py-1 table-header-text w-20">Nama Officer</th>
                            <th colspan="2" class="border border-black px-1 py-1 table-header-text">Pemeriksa Dokumen</th>
                            <th colspan="2" class="border border-black px-1 py-1 table-header-text">Pengatur Flow</th>
                            <th colspan="2" class="border border-black px-1 py-1 table-header-text">Operator X-Ray</th>
                            <th colspan="4" class="border border-black px-1 py-1 table-header-text">Pemeriksaan Orang Manual/HHMD</th>
                            <th colspan="4" class="border border-black px-1 py-1 table-header-text">Pemeriksa Manual Kabin</th>
                            <th rowspan="2" class="border border-black px-1 py-1 table-header-text w-16">Keterangan</th>
                        </tr>
                        <tr class="bg-gray-100">
                            <th class="border border-black px-1 py-1 table-header-text w-12">Start</th>
                            <th class="border border-black px-1 py-1 table-header-text w-12">End</th>
                            <th class="border border-black px-1 py-1 table-header-text w-12">Start</th>
                            <th class="border border-black px-1 py-1 table-header-text w-12">End</th>
                            <th class="border border-black px-1 py-1 table-header-text w-12">Start</th>
                            <th class="border border-black px-1 py-1 table-header-text w-12">End</th>
                            <th class="border border-black px-1 py-1 table-header-text w-12">Start</th>
                            <th class="border border-black px-1 py-1 table-header-text w-12">End</th>
                            <th class="border border-black px-1 py-1 table-header-text w-8">R</th>
                            <th class="border border-black px-1 py-1 table-header-text w-8">U</th>
                            <th class="border border-black px-1 py-1 table-header-text w-12">Start</th>
                            <th class="border border-black px-1 py-1 table-header-text w-12">End</th>
                            <th class="border border-black px-1 py-1 table-header-text w-8">R</th>
                            <th class="border border-black px-1 py-1 table-header-text w-8">U</th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        @if(isset($form->officerLog) && count($form->officerLog) > 0)
                            @foreach($form->officerLog as $officerId => $data)
                            <tr>
                                <td class="border border-black px-1 py-1 text-center table-cell-text">{{ $loop->iteration }}</td>
                                <td class="border border-black px-1 py-1 text-center font-semibold table-cell-text break-words">
                                    {{ $data['officer_name'] }}
                                </td>

                                <!-- Pemeriksaan Dokumen -->
                                @php $roleData = $data['roles']['pemeriksaan_dokumen'] ?? []; @endphp
                                <td class="border border-black px-1 py-1 text-center table-cell-text">
                                    @foreach($roleData as $slot)
                                        {{ \Carbon\Carbon::parse($slot['start'])->format('H:i') }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                                <td class="border border-black px-1 py-1 text-center table-cell-text">
                                    @foreach($roleData as $slot)
                                        {{ \Carbon\Carbon::parse($slot['end'])->format('H:i') }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>

                                <!-- Pengatur Flow -->
                                @php $roleData = $data['roles']['pengatur_flow'] ?? []; @endphp
                                <td class="border border-black px-1 py-1 text-center table-cell-text">
                                    @foreach($roleData as $slot)
                                        {{ \Carbon\Carbon::parse($slot['start'])->format('H:i') }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                                <td class="border border-black px-1 py-1 text-center table-cell-text">
                                    @foreach($roleData as $slot)
                                        {{ \Carbon\Carbon::parse($slot['end'])->format('H:i') }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>

                                <!-- Operator X-Ray -->
                                @php $roleData = $data['roles']['operator_xray'] ?? []; @endphp
                                <td class="border border-black px-1 py-1 text-center table-cell-text">
                                    @foreach($roleData as $slot)
                                        {{ \Carbon\Carbon::parse($slot['start'])->format('H:i') }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                                <td class="border border-black px-1 py-1 text-center table-cell-text">
                                    @foreach($roleData as $slot)
                                        {{ \Carbon\Carbon::parse($slot['end'])->format('H:i') }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>

                                <!-- Pemeriksaan Orang Manual/HHMD -->
                                @php $roleData = $data['roles']['hhmd_petugas'] ?? []; @endphp
                                <td class="border border-black px-1 py-1 text-center table-cell-text">
                                    @foreach($roleData as $slot)
                                        {{ \Carbon\Carbon::parse($slot['start'])->format('H:i') }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                                <td class="border border-black px-1 py-1 text-center table-cell-text">
                                    @foreach($roleData as $slot)
                                        {{ \Carbon\Carbon::parse($slot['end'])->format('H:i') }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                                <td class="border border-black px-1 py-1 text-center table-cell-text font-semibold text-blue-600">
                                    @foreach($roleData as $slot)
                                        {{ $slot['hhmd_random'] ?? '-' }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                                <td class="border border-black px-1 py-1 text-center table-cell-text font-semibold text-blue-600">
                                    @foreach($roleData as $slot)
                                        {{ $slot['hhmd_unpredictable'] ?? '-' }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>

                                <!-- Pemeriksa Manual Kabin -->
                                @php $roleData = $data['roles']['manual_kabin_petugas'] ?? []; @endphp
                                <td class="border border-black px-1 py-1 text-center table-cell-text">
                                    @foreach($roleData as $slot)
                                        {{ \Carbon\Carbon::parse($slot['start'])->format('H:i') }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                                <td class="border border-black px-1 py-1 text-center table-cell-text">
                                    @foreach($roleData as $slot)
                                        {{ \Carbon\Carbon::parse($slot['end'])->format('H:i') }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                                <td class="border border-black px-1 py-1 text-center table-cell-text font-semibold text-green-600">
                                    @foreach($roleData as $slot)
                                        {{ $slot['cek_random_barang'] ?? '-' }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                                <td class="border border-black px-1 py-1 text-center table-cell-text font-semibold text-green-600">
                                    @foreach($roleData as $slot)
                                        {{ $slot['barang_unpredictable'] ?? '-' }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>

                                <!-- Keterangan -->
                                <td class="border border-black px-1 py-1 text-left table-cell-text break-words">
                                    {{ implode(', ', array_unique(array_filter($data['keterangan']))) }}
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="border border-black px-2 py-4 text-center italic info-text" colspan="17">
                                    Tidak ada detail entri untuk logbook ini
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Notes Section -->
            @if(isset($form->notes) && $form->notes)
            <div class="mt-4">
                <div class="text-center mb-2">
                    <p class="font-bold text-xs">CATATAN</p>
                </div>
                <div class="border border-black p-3 min-h-12 text-xs">
                    <p>{{ $form->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Signature Section -->
            <div class="mt-6 text-sm">
                <div class="flex justify-between items-start space-x-8">
                    <!-- Left Signature -->
                    <div class="flex-1 text-center">
                        <p class="mb-1">Dibuat Oleh,</p>
                        <p class="mb-2">Airport Security Officer</p>
                        <div class="signature-container flex items-center justify-center mb-2">
                            @if($form->submittedSignature)
                                <img src="{{ $form->submittedSignature }}" class="h-10" alt="Tanda Tangan">
                            @else
                                <span class="italic text-gray-500">Belum tanda tangan</span>
                            @endif
                        </div>
                        <p class="font-semibold pt-1 mx-8">{{ $form->creatorName ?? 'N/A' }}</p>
                    </div>

                    <!-- Right Signature -->
                    <div class="flex-1 text-center">
                        <p class="mb-1">Disetujui Oleh,</p>
                        <p class="mb-2">Airport Security Supervisor</p>
                        <div class="signature-container flex items-center justify-center mb-2">
                            @if($form->approvedSignature)
                                <img src="{{ $form->approvedSignature }}" class="h-10" alt="Tanda Tangan">
                            @else
                                <span class="italic text-gray-500">Belum tanda tangan</span>
                            @endif
                        </div>
                        <p class="font-semibold pt-1 mx-8">{{ $form->approverName ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</body>

</html>
