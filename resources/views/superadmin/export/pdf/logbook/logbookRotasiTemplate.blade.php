<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HHMD Forms - Rotasi</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .page-container {
            border: 2px solid black;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin: 24px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .header img {
            width: 80px;
            height: 80px;
        }

        .header h1 {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            flex-grow: 1;
            margin: 0 20px;
        }

        .info-section {
            border-top: 1px solid #ccc;
            padding-top: 12px;
            margin: 16px 0 24px 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 14px;
            color: #374151;
        }

        .info-grid p {
            margin: 4px 0;
        }

        .section-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .signature-section {
            margin-top: 40px;
            text-align: center;
            font-size: 14px;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .signature-box {
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature-box img {
            height: 64px;
            margin-top: 20px;
        }

        .italic-gray {
            font-style: italic;
            color: #9ca3af;
        }

        .notes-section {
            border: 1px solid black;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 14px;
            min-height: 80px;
        }

        .bg-gray-100 {
            background-color: #f5f5f5;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-blue-600 {
            color: #2563eb;
        }

        .text-green-600 {
            color: #16a34a;
        }
    </style>
</head>

<body>
    <div class="page-container">
        <div class="header">
            <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo">
            <h1>
                LOGBOOK HARIAN <br>
                CATATAN AKTIVITAS HARIAN <br>
                ROTASI PETUGAS
            </h1>
            <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo">
        </div>

        @foreach($forms as $form)
        <!-- Informasi detail -->
        <div class="info-section">
            <div class="info-grid">
                <p>HARI / TANGGAL
                    <strong>: {{ \Carbon\Carbon::parse($form->date)->translatedFormat('l, d F Y') }}</strong>
                </p>
                <p>TIPE ROTASI <strong>: {{ strtoupper($form->type) }}</strong></p>
                <p>STATUS <strong>: {{ strtoupper($form->status ?? 'SUBMITTED') }}</strong></p>
            </div>
        </div>

        <!-- Tabel Detail Rotasi Lengkap -->
        <div class="section-title">DETAIL AKTIVITAS ROTASI</div>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Nama Officer</th>
                    <th colspan="2">Pemeriksa Dokumen</th>
                    <th colspan="2">Pengatur Flow</th>
                    <th colspan="2">Operator X-Ray</th>
                    <th colspan="4">Pemeriksaan Orang Manual/HHMD</th>
                    <th colspan="4">Pemeriksa Manual Kabin</th>
                    <th rowspan="2">Keterangan</th>
                </tr>
                <tr class="bg-gray-100">
                    <th>Start</th>
                    <th>End</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>R</th>
                    <th>U</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>R</th>
                    <th>U</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($officerLog) && count($officerLog) > 0)
                @foreach($officerLog as $officerId => $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left font-semibold">{{ $data['officer_name'] }}</td>

                    <!-- Pemeriksaan Dokumen -->
                    @php $roleData = $data['roles']['pemeriksaan_dokumen'] ?? []; @endphp
                    <td>@foreach($roleData as $slot) {{ strip_tags($slot['start']) }}<br> @endforeach</td>
                    <td>@foreach($roleData as $slot) {{ strip_tags($slot['end']) }}<br> @endforeach</td>

                    <!-- Pengatur Flow -->
                    @php $roleData = $data['roles']['pengatur_flow'] ?? []; @endphp
                    <td>@foreach($roleData as $slot) {{ strip_tags($slot['start']) }}<br> @endforeach</td>
                    <td>@foreach($roleData as $slot) {{ strip_tags($slot['end']) }}<br> @endforeach</td>

                    <!-- Operator X-Ray -->
                    @php $roleData = $data['roles']['operator_xray'] ?? []; @endphp
                    <td>@foreach($roleData as $slot) {{ strip_tags($slot['start']) }}<br> @endforeach</td>
                    <td>@foreach($roleData as $slot) {{ strip_tags($slot['end']) }}<br> @endforeach</td>

                    <!-- Pemeriksaan Orang Manual/HHMD -->
                    @php $roleData = $data['roles']['hhmd_petugas'] ?? []; @endphp
                    <td>@foreach($roleData as $slot) {{ strip_tags($slot['start']) }}<br> @endforeach</td>
                    <td>@foreach($roleData as $slot) {{ strip_tags($slot['end']) }}<br> @endforeach</td>
                    <td class="font-semibold text-blue-600">
                        @foreach($roleData as $slot)
                        {{ $slot['hhmd_random'] ?? '-' }}<br>
                        @endforeach
                    </td>
                    <td class="font-semibold text-blue-600">
                        @foreach($roleData as $slot)
                        {{ $slot['hhmd_unpredictable'] ?? '-' }}<br>
                        @endforeach
                    </td>

                    <!-- Pemeriksa Manual Kabin -->
                    @php $roleData = $data['roles']['manual_kabin_petugas'] ?? []; @endphp
                    <td>@foreach($roleData as $slot) {{ strip_tags($slot['start']) }}<br> @endforeach</td>
                    <td>@foreach($roleData as $slot) {{ strip_tags($slot['end']) }}<br> @endforeach</td>
                    <td class="font-semibold text-green-600">
                        @foreach($roleData as $slot)
                        {{ $slot['cek_random_barang'] ?? '-' }}<br>
                        @endforeach
                    </td>
                    <td class="font-semibold text-green-600">
                        @foreach($roleData as $slot)
                        {{ $slot['barang_unpredictable'] ?? '-' }}<br>
                        @endforeach
                    </td>

                    <!-- Keterangan -->
                    <td class="text-left">
                        {{ implode(', ', array_unique(array_filter($data['keterangan']))) }}
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td class="text-center italic-gray" colspan="16">
                        Tidak ada detail entri untuk logbook ini
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Catatan -->
        @if(isset($form->notes) && $form->notes)
        <div class="section-title">CATATAN</div>
        <div class="notes-section">
            <p>{{ $form->notes }}</p>
        </div>
        @endif

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-grid">
                <!-- Kiri: Yang Menyerahkan -->
                <div>
                    <p>Dibuat Oleh</p>
                    <div class="signature-box">
                        @if($form->submittedSignature)
                        <img src="{!! $form->submittedSignature !!}" alt="Tanda Tangan">
                        @else
                        <span class="italic-gray">Belum tanda tangan</span>
                        @endif
                    </div>
                    <p><strong>{{ $form->creatorName }}</strong></p>
                </div>

                <!-- Kanan: Yang Menyetujui -->
                <div>
                    <p>Disetujui Oleh</p>
                    <div class="signature-box">
                        @if($form->approvedSignature)
                        <img src="{!! $form->approvedSignature !!}" alt="Tanda Tangan">
                        @else
                        <span class="italic-gray">Belum tanda tangan</span>
                        @endif
                    </div>
                    <p><strong>{{ $form->approverName }}</strong></p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</body>

</html>