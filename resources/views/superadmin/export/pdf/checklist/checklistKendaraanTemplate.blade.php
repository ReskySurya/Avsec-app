<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checklist Pengecekan Harian Kendaraan Patroli</title>
    <style>
        {!! file_get_contents(public_path('css/pdf.css')) !!}
    </style>
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
                <h1 class="text-sm sm:text-xl font-bold">CHECK LIST PENGECEKAN HARIAN</h1>
                <h2 class="text-sm sm:text-lg font-bold">KENDARAAN {{ strtoupper($checklist->type ?? 'PATROLI') }} PATROLI</h2>
                <p class="text-xs sm:text-sm">AIRPORT SECURITY BANDAR UDARA ADISUTJIPTO</p>
            </div>
            <img src="data:image/png;base64,{{ $logoInjourneyBase64 }}" alt="Injourney Logo" class="w-20 h-20 mt-2 sm:mt-0">
        </div>

        {{-- Informasi Detail --}}
        <div class="border-t border-gray-300 pt-3 mt-4 mb-6">
            <div class="grid grid-cols-2 gap-y-2 text-sm text-gray-700">
                @php
                $checklistDate = $checklist->date ? \Carbon\Carbon::parse($checklist->date) : $checklist->created_at;
                @endphp
                <p>HARI / TANGGAL
                    <span class="font-semibold">
                        : {{ $checklistDate->translatedFormat('l, d F Y') }}
                    </span>
                </p>
                <p>SHIFT <span class="font-semibold">: {{ strtoupper($checklist->shift ?? 'PAGI') }}</span></p>
                <p>JENIS KENDARAAN <span class="font-semibold">: {{ strtoupper($checklist->type ?? 'MOTOR') }}</span></p>
                <p>STATUS <span class="font-semibold">: {{ strtoupper($checklist->status ?? 'PENDING') }}</span></p>
            </div>
        </div>

        {{-- Main Table --}}
        <table class="w-full border border-black mb-6 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th rowspan="2" class="border border-black px-2 py-2 font-bold w-10">NO</th>
                    <th rowspan="2" class="border border-black px-2 py-2 font-bold">KETERANGAN</th>
                    <th class="border border-black px-2 py-2 font-bold text-center" colspan="2">
                        KONDISI SHIFT {{ strtoupper($checklist->shift ?? 'PAGI') }}
                    </th>
                </tr>
                <tr class="bg-gray-200">
                    <th class="border border-black px-2 py-1 font-bold w-16">BAIK</th>
                    <th class="border border-black px-2 py-1 font-bold w-16">TIDAK</th>
                </tr>
            </thead>
            <tbody>
                @php
                $overallNumber = 1;

                // This logic is moved from the controller to be reusable for single previews and bulk exports.
                // It prepares the categories and groups the checklist items by category.

                $categoryList = $checklist->details
                    ->pluck('item.category')
                    ->filter() // Removes null/false/empty strings to avoid them being treated as categories
                    ->unique()
                    ->sort()
                    ->values();

                $categories = [];
                $categoryCounter = 'A';

                foreach ($categoryList as $category) {
                    // We already filtered for non-empty strings
                    $categories[$category] = [$categoryCounter, strtoupper($category)];
                    $categoryCounter++;
                }

                // Check if there are any items with a null or empty category string
                $hasEmptyCategory = $checklist->details->contains(function ($detail) {
                    return empty($detail->item->category);
                });

                // If so, add a special 'uncategorized' category
                if ($hasEmptyCategory) {
                    // Note: 'Motor' seems to be a default name for the uncategorized section
                    $categories['uncategorized'] = [$categoryCounter, 'Motor'];
                }

                // Group the items, ensuring that null or empty strings are keyed as 'uncategorized'
                $groupedItems = $checklist->details->groupBy(function ($detail) {
                    return !empty($detail->item->category) ? $detail->item->category : 'uncategorized';
                });
                @endphp
                
                @foreach ($categories as $categoryKey => $categoryData)
                    @if (isset($groupedItems[$categoryKey]) && count($groupedItems[$categoryKey]) > 0)
                        <tr>
                            <td class="border border-black px-2 py-2 text-center font-bold bg-gray-100">
                                {{ $categoryData[0] }}
                            </td>
                            <td class="border border-black px-2 py-2 font-bold bg-gray-100 uppercase">
                                {{ $categoryData[1] }}
                            </td>
                            <td class="border border-black bg-gray-100"></td>
                            <td class="border border-black bg-gray-100"></td>
                        </tr>

                        @foreach ($groupedItems[$categoryKey] as $detail)
                            <tr>
                                <td class="border border-black px-2 py-2 text-center">
                                    {{ $overallNumber }}
                                </td>
                                <td class="border border-black px-2 py-2 pl-4">
                                    {{ $detail->item->name ?? 'Item tidak ditemukan' }}
                                </td>
                                <td class="border border-black px-2 py-2 text-center">
                                    @if ($detail->is_ok)
                                        <span class="text-green-600 font-bold">✓</span>
                                    @endif
                                </td>
                                <td class="border border-black px-2 py-2 text-center">
                                    @if (!$detail->is_ok)
                                        <span class="text-red-600 font-bold">✓</span>
                                    @endif
                                </td>
                            </tr>
                            @php $overallNumber++; @endphp
                        @endforeach
                    @endif
                @endforeach

                @if($groupedItems->isEmpty())
                    <tr>
                        <td colspan="4" class="border border-black px-2 py-4 text-center text-gray-500">
                            Tidak ada data checklist
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Keterangan Section --}}
        <div class="mb-6">
            <h3 class="font-bold text-sm mb-2">KETERANGAN :</h3>
            <div class="space-y-1">
                <div class="flex">
                    <span class="w-4 text-sm">1</span>
                    <div class="border-b border-dotted border-black flex-1 min-h-[20px]">
                        <span class="text-sm">Petugas dinas pagi melakukan pengecekan pada nomor A, B dan C</span>
                    </div>
                </div>
                <div class="flex">
                    <span class="w-4 text-sm">2</span>
                    <div class="border-b border-dotted border-black flex-1 min-h-[20px]">
                        <span class="text-sm">Petugas dinas siang melakukan pengecekan pada nomor B dan C</span>
                    </div>
                </div>
                <div class="flex">
                    <span class="w-4 text-sm">3</span>
                    <div class="border-b border-dotted border-black flex-1 min-h-[20px]">
                        <span class="text-sm">Petugas dinas malam melakukan pengecekan pada nomor A dan B</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- CATATAN Section --}}
        <div class="mb-6">
            <h3 class="font-bold text-sm mb-2">CATATAN :</h3>
            <div class="space-y-1">
                @php
                $allNotes = $checklist->details->filter(fn($detail) => !empty($detail->notes));
                @endphp

                @forelse($allNotes as $index => $detail)
                    <div class="flex">
                        <span class="w-4 text-sm">{{ $index + 1 }}</span>
                        <div class="border-b border-dotted border-black flex-1 min-h-[20px] flex items-end pb-1">
                            <span class="text-sm">
                                <strong>{{ $detail->item->name ?? 'Item tidak ditemukan' }}:</strong> {{ $detail->notes }}
                            </span>
                        </div>
                    </div>
                @empty
                    @for($i = 1; $i <= 3; $i++)
                        <div class="flex">
                            <span class="w-4 text-sm">{{ $i }}</span>
                            <div class="border-b border-dotted border-black flex-1 min-h-[20px]">
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
        </div>

        {{-- Tanda Tangan --}}
        <div class="mt-10 text-center text-sm">
            @php
            $bulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $date = \Carbon\Carbon::parse($checklist->date);
            @endphp
            <p class="mb-6">Yogyakarta, {{ $date->format('d') }} {{ $bulan[(int)$date->format('n')] }} {{ $date->format('Y') }}</p>

            <div class="grid grid-cols-2 gap-4">
                {{-- Kiri: Yang Menyerahkan --}}
                <div>
                    <p>Yang Menyerahkan</p>
                    <div class="h-16 flex items-center justify-center">
                        @if(isset($checklist->senderSignature) && $checklist->senderSignature)
                            <img src="data:image/png;base64,{{ $checklist->senderSignature }}" class="h-16 mt-5" alt="TTD Yang Menyerahkan">
                        @else
                            <span class="italic text-gray-400">Belum tanda tangan</span>
                        @endif
                    </div>
                    <p class="font-semibold mt-1">({{ $checklist->sender->name ?? '...........................' }})</p>
                    <p class="text-xs text-gray-600">Petugas Dinas {{ ucfirst($checklist->shift ?? 'Pagi') }}</p>
                </div>

                {{-- Kanan: Yang Menerima --}}
                <div>
                    <p>Yang Menerima</p>
                    <div class="h-16 flex items-center justify-center">
                        @if(isset($checklist->receivedSignature) && $checklist->receivedSignature)
                            <img src="data:image/png;base64,{{ $checklist->receivedSignature }}" class="h-16 mt-5" alt="TTD Yang Menerima">
                        @else
                            <span class="italic text-gray-400">Belum tanda tangan</span>
                        @endif
                    </div>
                    <p class="font-semibold mt-1">({{ $checklist->receiver->name ?? '...........................' }})</p>
                    <p class="text-xs text-gray-600">
                        Petugas Dinas 
                        @if(isset($checklist->shift))
                            {{ $checklist->shift == 'pagi' ? 'Siang' : ($checklist->shift == 'siang' ? 'Malam' : 'Pagi') }}
                        @else
                            Siang
                        @endif
                    </p>
                </div>
            </div>

            {{-- Bawah Tengah: Mengetahui --}}
            <div class="mt-6">
                <p>Mengetahui,</p>
                <div class="h-16 flex items-center justify-center">
                    @if(isset($checklist->approvedSignature) && $checklist->approvedSignature)
                        <img src="data:image/png;base64,{{ $checklist->approvedSignature }}" class="h-16 mt-5" alt="TTD Mengetahui">
                    @else
                        <span class="italic text-gray-400">Belum tanda tangan</span>
                    @endif
                </div>
                <p class="font-semibold mt-1">({{ $checklist->approver->name ?? '...........................' }})</p>
                <p class="text-xs text-gray-600">Supervisor</p>
            </div>
        </div>
    </div>
    @endforeach
</body>

</html>
