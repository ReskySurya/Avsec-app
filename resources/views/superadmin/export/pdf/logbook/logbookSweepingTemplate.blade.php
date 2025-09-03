<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forms - Sweeping PI</title>
    <style>
        {
            ! ! file_get_contents(public_path('css/pdf.css')) ! !
        }

        /* Custom CSS untuk landscape */
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-size: 10px;
        }

        .landscape-container {
            width: 100%;
            max-width: none;
        }

        /* Adjustments untuk landscape */
        .calendar-table {
            font-size: 8px;
        }

        .calendar-table th,
        .calendar-table td {
            padding: 1px 2px;
        }

        .day-cell {
            width: 20px;
            height: 20px;
        }

        .item-name-cell {
            min-width: 120px;
            max-width: 150px;
        }
    </style>
</head>

<body class="m-0 p-0">
    <div class="landscape-container border-t-2 border-x-2 border-black bg-white shadow-md p-4 m-4 page-break-after">
        @php
        $logoAirportBase64 = base64_encode(file_get_contents(public_path('images/airport-security-logo.png')));
        $logoInjourneyBase64 = base64_encode(file_get_contents(public_path('images/injourney-API.png')));
        @endphp

        <!-- Header Section -->
        <div class="flex items-center justify-between mb-4">
            <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo" class="w-16 h-16">
            <div class="text-center flex-grow px-4">
                <h1 class="text-sm font-bold">
                    LOGBOOK HARIAN <br>
                    CATATAN AKTIVITAS HARIAN <br>
                    SWEEPING PROHIBITED ITEMS
                </h1>
            </div>
            <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo" class="w-16 h-16">
        </div>

        @foreach($forms as $form)
        <!-- Informasi detail -->
        <div class="border-t border-gray-300 pt-2 mb-4">
            <div class="grid grid-cols-2 gap-x-8 text-xs text-gray-700">
                <p>PERIODE
                    <span class="font-semibold">
                        : {{ \Carbon\Carbon::createFromDate($form->tahun, $form->bulan, 1)->translatedFormat('F Y') }}
                    </span>
                </p>
                <p>TENANT <span class="font-semibold">: {{ $form->tenant->tenant_name }}</span></p>
            </div>
        </div>

        {{-- Tabel Completion Stats --}}
        @if(isset($form->completionStats))
        <div class="flex justify-center mb-2">
            <p class="font-semibold text-xs">STATISTIK PENYELESAIAN</p>
        </div>
        <table class="w-full border border-black mb-4 text-xs">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-black px-1 py-1 text-xs">Total Items</th>
                    <th class="border border-black px-1 py-1 text-xs">Hari dalam Bulan</th>
                    <th class="border border-black px-1 py-1 text-xs">Sudah Dicek</th>
                    <th class="border border-black px-1 py-1 text-xs">Belum Dicek</th>
                    <th class="border border-black px-1 py-1 text-xs">Tingkat Penyelesaian</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-black px-2 py-2 text-center">{{ $form->completionStats['total_items'] }}</td>
                    <td class="border border-black px-2 py-2 text-center">{{ $form->completionStats['days_in_month'] }}</td>
                    <td class="border border-black px-2 py-2 text-center">{{ $form->completionStats['total_checked'] }}</td>
                    <td class="border border-black px-2 py-2 text-center">{{ $form->completionStats['total_pending'] }}</td>
                    <td class="border border-black px-2 py-2 text-center">{{ $form->completionStats['completion_rate'] }}%</td>
                </tr>
            </tbody>
        </table>
        @endif

        {{-- Kalender Checklist Prohibited Items --}}
        @php
        $daysInMonth = \Carbon\Carbon::createFromDate($form->tahun, $form->bulan, 1)->daysInMonth;
        $monthName = \Carbon\Carbon::createFromDate($form->tahun, $form->bulan, 1)->translatedFormat('F Y');
        @endphp

        {{-- Tabel Prohibited Items dengan Kalender --}}
        <div class="flex justify-center mb-2">
            <p class="font-semibold text-xs">PROHIBITED ITEMS</p>
        </div>

        @if(isset($form->sweepingDetails) && $form->sweepingDetails->count() > 0)
        @foreach($form->sweepingDetails as $detailIndex => $detail)

        <!-- Single table untuk semua tanggal (landscape bisa muat) -->
        <table class="w-full border border-black mb-3 calendar-table">
            <thead>
                <!-- Header dengan nama item -->
                <tr class="bg-gray-100">
                    <td class="border border-black px-1 py-1 font-semibold item-name-cell" rowspan="2">
                        <div class="text-xs">
                            {{ $detail->item_name_pi ?? 'Item ' . ($detailIndex + 1) }}
                            @if($detail->quantity)
                            <br><span class="text-2xs font-normal">({{ $detail->quantity }} item)</span>
                            @endif
                        </div>
                    </td>
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        <th class="border border-black px-1 py-1 day-cell text-center text-2xs">{{ $day }}</th>
                        @endfor
                        @if($daysInMonth < 31)
                            @for($day=$daysInMonth + 1; $day <=31; $day++)
                            <th class="border border-black px-1 py-1 day-cell text-center bg-gray-200 text-2xs">-</th>
                            @endfor
                            @endif
                </tr>

                <!-- Baris untuk checkboxes -->
                <tr class="bg-gray-100">
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                        $fieldName='tanggal_' . $day;
                        $isChecked=isset($detail->$fieldName) && $detail->$fieldName;
                        $hasNote = isset($detail->{'note_' . $day}) && $detail->{'note_' . $day};
                        @endphp
                        <td class="border border-black px-1 py-1 text-center day-cell">
                            <div class="flex flex-col items-center justify-center h-full">
                                @if($isChecked)
                                <div class="w-3 h-3 bg-green-500 rounded flex items-center justify-center">
                                    <span class="text-white" style="font-size: 6px;">✓</span>
                                </div>
                                @else
                                <div class="w-3 h-3 border border-gray-400 rounded"></div>
                                @endif
                                @if($hasNote)
                                <div class="text-blue-600" style="font-size: 6px;">📝</div>
                                @endif
                            </div>
                        </td>
                        @endfor
                        @if($daysInMonth < 31)
                            @for($day=$daysInMonth + 1; $day <=31; $day++)
                            <td class="border border-black px-1 py-1 text-center day-cell bg-gray-200">-</td>
                            @endfor
                            @endif
                </tr>
            </thead>
        </table>

        @endforeach
        @else
        <div class="border border-black p-4 text-center text-gray-400 italic text-xs">
            Data detail sweeping tidak tersedia
        </div>
        @endif

        {{-- Keterangan --}}
        <div class="mb-4">
            <p class="font-semibold mb-2 text-xs">Keterangan:</p>
            <div class="grid grid-cols-4 gap-2 text-xs">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-500 rounded mr-1 flex items-center justify-center">
                        <span class="text-white" style="font-size: 6px;">✓</span>
                    </div>
                    <span>Sudah dicek hari ini</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 border border-gray-400 rounded mr-1"></div>
                    <span>Belum dicek</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-yellow-500 rounded mr-1"></div>
                    <span>Terlewat (hari sebelumnya)</span>
                </div>
            </div>
        </div>
        <!-- 
        {{-- Catatan --}}
        @if(isset($form->notes) && $form->notes)
        <div class="flex justify-center mb-2">
            <p class="font-semibold text-xs">CATATAN</p>
        </div>
        <div class="border border-black p-2 mb-4 text-xs min-h-12">
            <p>{{ $form->notes }}</p>
        </div>
        @endif -->

        {{-- Notes Sweeping PI --}}
        @if(isset($form->notesSweeping) && $form->notesSweeping->count() > 0)
        <div class="flex justify-center mb-2">
            <p class="font-semibold text-xs">CATATAN</p>
        </div>
        <table class="w-full border border-black mb-4 text-xs">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-black px-1 py-1 w-8">No</th>
                    <th class="border border-black px-1 py-1 w-20">Tanggal</th>
                    <th class="border border-black px-1 py-1">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($form->notesSweeping as $index => $note)
                <tr>
                    <td class="border border-black px-1 py-1 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black px-1 py-1 text-center">
                        {{ \Carbon\Carbon::parse($note->created_at)->format('d/m/Y') }}
                    </td>
                    <td class="border border-black px-1 py-1">{{ $note->notes ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Tanda Tangan --}}
        <div class="mt-6 text-center text-xs">
            <div class="flex justify-end">
                <div>
                    <p>Pemilik Tenant</p>
                    <div class="h-12 flex items-center justify-center">
                        @if(isset($sweeping) && $sweeping->tenant->supervisorSignature)
                        <img src="data:image/png;base64,{!! $sweeping->tenant->supervisorSignature !!}" class="h-12 mt-3" alt="Tanda Tangan">
                        @else
                        <span class="italic text-gray-400">Belum tanda tangan</span>
                        @endif
                    </div>
                    <p class="font-semibold mt-1">{{ $sweeping->tenant->supervisorName ?? '-' }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</body>

</html>