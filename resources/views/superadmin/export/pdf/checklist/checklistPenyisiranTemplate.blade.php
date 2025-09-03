<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Penyisiran Area</title>
    <style>{!! file_get_contents(public_path('css/pdf.css')) !!}</style>
</head>
<body class="m-0 p-0">
    @foreach($forms as $checklist)
    <div class="page-break-after border-t-2 border-x-2 border-black bg-white shadow-md p-6 m-6">
        @php
            $logoAirportBase64 = base64_encode(file_get_contents(public_path('images/airport-security-logo.png')));
            $logoInjourneyBase64 = base64_encode(file_get_contents(public_path('images/injourney-API.png')));
        @endphp

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
            <img src="data:image/png;base64,{{ $logoAirportBase64 }}" alt="Logo" class="w-20 h-20 mb-2 sm:mb-0">
            <div class="text-center flex-grow px-2">
                <h1 class="text-sm sm:text-xl font-bold">CHECK LIST PENYISIRAN AREA</h1>
                <h2 class="text-sm sm:text-lg font-bold">{{ strtoupper($checklist->type ?? 'N/A') }}</h2>
                <p class="text-xs sm:text-sm">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
            </div>
            <img src="data:image/png;base64,{{ $logoInjourneyBase64 }}" alt="Injourney Logo" class="w-20 h-20 mt-2 sm:mt-0">
        </div>

        {{-- Informasi Detail --}}
        <div class="border-t border-gray-300 pt-3 mt-4 mb-6">
            <div class="grid grid-cols-2 gap-y-2 text-sm text-gray-700">
                @php $checklistDate = $checklist->date ? \Carbon\Carbon::parse($checklist->date) : $checklist->created_at; @endphp
                <p>HARI / TANGGAL: <span class="font-semibold">{{ $checklistDate->translatedFormat('l, d F Y') }}</span></p>
                <p>WAKTU: <span class="font-semibold">{{ $checklist->time ? \Carbon\Carbon::parse($checklist->time)->format('H:i') : 'N/A' }}</span></p>
                <p>LOKASI: <span class="font-semibold">{{ strtoupper($checklist->type ?? 'N/A') }}</span></p>
                <p>GRUP: <span class="font-semibold">{{ strtoupper($checklist->grup ?? 'N/A') }}</span></p>
                <p>STATUS: <span class="font-semibold">{{ strtoupper($checklist->status ?? 'PENDING') }}</span></p>
            </div>
        </div>

        {{-- Main Table --}}
        <table class="w-full border border-black mb-6 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-black px-2 py-2 font-bold w-10" rowspan="2">NO</th>
                    <th class="border border-black px-2 py-2 font-bold" rowspan="2">LOKASI</th>
                    <th class="border border-black px-2 py-2 font-bold" rowspan="2">URAIAN</th>
                    <th class="border border-black px-2 py-2 font-bold" colspan="2">KONDISI</th>
                    <th class="border border-black px-2 py-2 font-bold" rowspan="2">KETERANGAN</th>
                </tr>
                <tr class="bg-gray-200">
                    <th class="border border-black px-2 py-1 font-bold w-16">AMAN</th>
                    <th class="border border-black px-2 py-1 font-bold w-16">TIDAK AMAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($checklist->details as $index => $detail)
                <tr>
                    <td class="border border-black px-2 py-2 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black px-2 py-2">{{ $detail->item->category ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-2">{{ $detail->item->name ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-2 text-center">@if($detail->iscondition) ✓ @endif</td>
                    <td class="border border-black px-2 py-2 text-center">@if(!$detail->iscondition) ✓ @endif</td>
                    <td class="border border-black px-2 py-2">{{ $detail->notes }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="border border-black px-2 py-4 text-center text-gray-500">Tidak ada data checklist</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tanda Tangan --}}
        <div class="mt-10 text-center text-sm">
            @php
                $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $date = \Carbon\Carbon::parse($checklist->date);
            @endphp
            <p class="mb-6">Yogyakarta, {{ $date->format('d') . ' ' . $bulan[$date->format('n') - 1] . ' ' . $date->format('Y') }}</p>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p>Yang Menyerahkan</p>
                    <div class="h-16 flex items-center justify-center">
                        @if($checklist->senderSignature)
                            <img src="data:image/png;base64,{{ $checklist->senderSignature }}" class="h-16 mt-5" alt="TTD">
                        @else
                            <span class="italic text-gray-400">Belum ttd</span>
                        @endif
                    </div>
                    <p class="font-semibold mt-1">({{ $checklist->sender->name ?? '...' }})</p>
                </div>
                <div>
                    <p>Yang Menerima</p>
                    <div class="h-16 flex items-center justify-center">
                        @if($checklist->receivedSignature)
                            <img src="data:image/png;base64,{{ $checklist->receivedSignature }}" class="h-16 mt-5" alt="TTD">
                        @else
                            <span class="italic text-gray-400">Belum ttd</span>
                        @endif
                    </div>
                    <p class="font-semibold mt-1">({{ $checklist->receiver->name ?? '...' }})</p>
                </div>
            </div>
            <div class="mt-6">
                <p>Mengetahui,</p>
                <div class="h-16 flex items-center justify-center">
                    @if($checklist->approvedSignature)
                        <img src="data:image/png;base64,{{ $checklist->approvedSignature }}" class="h-16 mt-5" alt="TTD">
                    @else
                        <span class="italic text-gray-400">Belum ttd</span>
                    @endif
                </div>
                <p class="font-semibold mt-1">({{ $checklist->approver->name ?? '...' }})</p>
                <p class="text-xs text-gray-600">Supervisor</p>
            </div>
        </div>
    </div>
    @endforeach
</body>
</html>