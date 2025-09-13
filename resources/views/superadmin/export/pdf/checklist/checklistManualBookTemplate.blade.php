<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Manual Book</title>
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
                                        CATATAN MANUAL BOOK<br>
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
                    <div class=" pt-3 mb-6 text-sm">
                        <div class="grid grid-cols-2 gap-y-2">
                            @php $checklistDate = \Carbon\Carbon::parse($checklist->date); @endphp
                            <p>HARI / TANGGAL: <span class="font-semibold">{{ $checklistDate->locale('id')->translatedFormat('l, d F Y') }}</span></p>
                            <p>POS JAGA: <span class="font-semibold">{{ $checklist->type ?? 'N/A' }}</span></p>
                            <p>SHIFT: <span class="font-semibold">{{ $checklist->shift ?? 'N/A' }}</span></p>
                        </div>
                    </div>

                    <table class="w-full border border-black mb-6 text-sm">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border border-black px-2 py-2 w-10">NO</th>
                                <th class="border border-black px-2 py-2 w-24">JAM</th>
                                <th class="border border-black px-2 py-2">PAX</th>
                                <th class="border border-black px-2 py-2">FLIGHT</th>
                                <th class="border border-black px-2 py-2">ORANG</th>
                                <th class="border border-black px-2 py-2">BARANG</th>
                                <th class="border border-black px-2 py-2">TEMUAN</th>
                                <th class="border border-black px-2 py-2">KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($checklist->details as $index => $detail)
                            <tr>
                                <td class="border border-black px-2 py-2 text-center">{{ $index + 1 }}</td>
                                <td class="border border-black px-2 py-2 text-center">{{ $detail->time ? \Carbon\Carbon::parse($detail->time)->format('H:i') : '' }}</td>
                                <td class="border border-black px-2 py-2">{{ $detail->pax }}</td>
                                <td class="border border-black px-2 py-2">{{ $detail->flight }}</td>
                                <td class="border border-black px-2 py-2">{{ $detail->orang }}</td>
                                <td class="border border-black px-2 py-2">{{ $detail->barang }}</td>
                                <td class="border border-black px-2 py-2">{{ $detail->temuan }}</td>
                                <td class="border border-black px-2 py-2">{{ $detail->keterangan }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="border border-black px-2 py-4 text-center text-gray-500">Tidak ada catatan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-10 text-center text-sm">
                         @php
                            $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            $date = \Carbon\Carbon::parse($checklist->date);
                        @endphp
                        <p class="mb-6">Yogyakarta, {{ $date->format('d') . ' ' . $bulan[$date->format('n') - 1] . ' ' . $date->format('Y') }}</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p>Petugas</p>
                                <div class="h-16 flex items-center justify-center">
                                    @if($checklist->senderSignature)
                                        <img src="data:image/png;base64,{{ $checklist->senderSignature }}" class="h-16 mt-5" alt="TTD Petugas">
                                    @else
                                        <span class="italic text-gray-400">Belum ttd</span>
                                    @endif
                                </div>
                                <p class="font-semibold mt-1">({{ $checklist->creator->name ?? '...' }})</p>
                            </div>
                            <div>
                                <p>Mengetahui,</p>
                                <div class="h-16 flex items-center justify-center">
                                    @if($checklist->approvedSignature)
                                        <img src="data:image/png;base64,{{ $checklist->approvedSignature }}" class="h-16 mt-5" alt="TTD Supervisor">
                                    @else
                                        <span class="italic text-gray-400">Belum ttd</span>
                                    @endif
                                </div>
                                <p class="font-semibold mt-1">({{ $checklist->approver->name ?? '...' }})</p>
                                <p class="text-xs text-gray-600">Supervisor</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</body>
</html>