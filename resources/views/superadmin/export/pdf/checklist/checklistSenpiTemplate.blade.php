<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Senjata Api</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>
<body class="m-0 p-0">
    @foreach($forms as $checklist)
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
                                        CHECKLIST PENCATATAN SENJATA API<br>
                                        AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO
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
                    <table class="w-full mb-6 text-sm">
                        @php $checklistDate = \Carbon\Carbon::parse($checklist->date); @endphp
                        <tr><td class="w-1/3">Tanggal</td><td>: {{ $checklistDate->format('d') }} {{ $checklistDate->translatedFormat('F') }} {{ $checklistDate->format('Y') }}</td></tr>
                        <tr><td class="w-1/3">Nama</td><td>: {{ $checklist->name ?? 'N/A' }}</td></tr>
                        <tr><td>Instansi</td><td>: {{ $checklist->agency ?? 'N/A' }}</td></tr>
                        <tr><td>No. Penerbangan</td><td>: {{ $checklist->flightNumber ?? 'N/A' }}</td></tr>
                        <tr><td>Tujuan</td><td>: {{ $checklist->destination ?? 'N/A' }}</td></tr>
                    </table>

                    <p class="text-sm mb-2">Dengan rincian sebagai berikut:</p>
                    <table class="w-full border border-black mb-6 text-sm">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border border-black px-2 py-2">JENIS SENPI</th>
                                <th class="border border-black px-2 py-2">JML SENPI</th>
                                <th class="border border-black px-2 py-2">JML MAGAZINE</th>
                                <th class="border border-black px-2 py-2">JML PELURU</th>
                                <th class="border border-black px-2 py-2">NO. IZIN SENPI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-black px-2 py-2 text-center">{{ $checklist->typeSenpi ?? '' }}</td>
                                <td class="border border-black px-2 py-2 text-center">{{ $checklist->quantitySenpi ?? 0 }}</td>
                                <td class="border border-black px-2 py-2 text-center">{{ $checklist->quantityMagazine ?? 0 }}</td>
                                <td class="border border-black px-2 py-2 text-center">{{ $checklist->quantityBullet ?? 0 }}</td>
                                <td class="border border-black px-2 py-2 text-center">{{ $checklist->licenseNumber ?? '' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="text-sm mb-6">Demikian berita acara ini dibuat dengan sebenarnya, untuk dapat dipergunakan sebagaimana mestinya.</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</body>
</html>