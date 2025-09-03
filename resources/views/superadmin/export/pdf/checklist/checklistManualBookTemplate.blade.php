<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Manual Book</title>
    <style>{!! file_get_contents(public_path('css/pdf.css')) !!}</style>
</head>
<body class="m-0 p-0">
    @foreach($forms as $checklist)
    <div class="page-break-after border-t-2 border-x-2 border-black bg-white shadow-md p-6 m-6">
        @php
            $logoAirportBase64 = base64_encode(file_get_contents(public_path('images/airport-security-logo.png')));
            $logoInjourneyBase64 = base64_encode(file_get_contents(public_path('images/injourney-API.png')));
        @endphp

        <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
            <img src="data:image/png;base64,{{ $logoAirportBase64 }}" alt="Logo" class="w-20 h-20">
            <div class="text-center flex-grow px-2">
                <h1 class="text-lg font-bold">CATATAN MANUAL BOOK</h1>
                <p class="text-sm">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
            </div>
            <img src="data:image/png;base64,{{ $logoInjourneyBase64 }}" alt="Injourney Logo" class="w-20 h-20">
        </div>

        <div class="border-t border-gray-300 pt-3 mt-4 mb-6 text-sm">
            <div class="grid grid-cols-2 gap-y-2">
                @php $checklistDate = \Carbon\Carbon::parse($checklist->date); @endphp
                <p>HARI / TANGGAL: <span class="font-semibold">{{ $checklistDate->translatedFormat('l, d F Y') }}</span></p>
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
    @endforeach
</body>
</html>