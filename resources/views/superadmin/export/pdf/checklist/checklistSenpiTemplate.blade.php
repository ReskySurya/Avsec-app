<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Senjata Api</title>
    <style>{!! file_get_contents(public_path('css/pdf.css')) !!}</style>
</head>
<body class="m-0 p-0">
    @foreach($data as $checklist)
    <div class="page-break-after border-t-2 border-x-2 border-black bg-white shadow-md p-6 m-6">
        @php
            $logoAirportBase64 = base64_encode(file_get_contents(public_path('images/airport-security-logo.png')));
            $logoInjourneyBase64 = base64_encode(file_get_contents(public_path('images/injourney-API.png')));
        @endphp

        <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
            <img src="data:image/png;base64,{{ $logoAirportBase64 }}" alt="Logo" class="w-20 h-20">
            <div class="text-center flex-grow px-2">
                <h1 class="text-lg font-bold">CHECKLIST PENCATATAN SENJATA API</h1>
                <p class="text-sm">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
            </div>
            <img src="data:image/png;base64,{{ $logoInjourneyBase64 }}" alt="Injourney Logo" class="w-20 h-20">
        </div>

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
    @endforeach
</body>
</html>