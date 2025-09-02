<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HHMD Forms - Chief</title>
    <style>
        {!! file_get_contents(public_path('css/pdf.css')) !!}
    </style>
</head>

<body class="m-0 p-0">
    <div class="border-t-2 border-x-2 border-black bg-white shadow-md p-6 m-6 page-break-after">
        @php
        $logoAirportBase64 = base64_encode(file_get_contents(public_path('images/airport-security-logo.png')));
        $logoInjourneyBase64 = base64_encode(file_get_contents(public_path('images/injourney-API.png')));
        @endphp
        
        <div class="flex flex-col sm:flex-row items-center justify-between">
            <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo" class="w-20 h-20 mb-2 sm:mb-0">
            <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
                LOGBOOK HARIAN <br>
                CATATAN AKTIVITAS HARIAN <br>
                CHIEF SECURITY
            </h1>
            <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo" class="w-20 h-20 mt-2 sm:mt-0">
        </div>

        @foreach($forms as $form)
        <!-- Informasi detail -->
        <div class="border-t border-gray-300 pt-3 mt-4 mb-6">
            <div class="grid grid-cols-2 gap-y-2 text-sm text-gray-700">
                <p>HARI / TANGGAL
                    <span class="font-semibold">
                        : {{ \Carbon\Carbon::parse($form->date)->translatedFormat('l, d F Y') }}
                    </span>
                </p>
                <p>LOKASI <span class="font-semibold">: {{ $form->lokasi }}</span></p>
                <p>CHIEF <span class="font-semibold">: {{ $form->chief }}</span></p>
                <p>STATUS <span class="font-semibold">: {{ strtoupper($form->status) }}</span></p>
            </div>
        </div>

        {{-- Tabel Aktivitas Chief --}}
        <div class="flex justify-center mb-2">
            <p class="font-semibold self-center">AKTIVITAS CHIEF SECURITY</p>
        </div>
        <table class="w-full border border-black mb-6 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-black px-2 py-1 w-10">No</th>
                    <th class="border border-black px-2 py-1">Jam</th>
                    <th class="border border-black px-2 py-1">Aktivitas</th>
                    <th class="border border-black px-2 py-1">Area/Lokasi</th>
                    <th class="border border-black px-2 py-1">Temuan</th>
                    <th class="border border-black px-2 py-1">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                {{-- Dummy data untuk chief --}}
                <tr>
                    <td class="border border-black px-2 py-1 text-center">1</td>
                    <td class="border border-black px-2 py-1 text-center">08:00 - 09:00</td>
                    <td class="border border-black px-2 py-1">{{ $form->aktivitas }}</td>
                    <td class="border border-black px-2 py-1 text-center">{{ $form->lokasi }}</td>
                    <td class="border border-black px-2 py-1 text-center">-</td>
                    <td class="border border-black px-2 py-1 text-center">-</td>
                </tr>
                <tr>
                    <td class="border border-black px-2 py-1 text-center">2</td>
                    <td class="border border-black px-2 py-1 text-center">10:00 - 11:00</td>
                    <td class="border border-black px-2 py-1">Monitoring CCTV</td>
                    <td class="border border-black px-2 py-1 text-center">Control Room</td>
                    <td class="border border-black px-2 py-1 text-center">Normal</td>
                    <td class="border border-black px-2 py-1 text-center">Lanjut monitoring</td>
                </tr>
                <tr>
                    <td class="border border-black px-2 py-1 text-center">3</td>
                    <td class="border border-black px-2 py-1 text-center">13:00 - 14:00</td>
                    <td class="border border-black px-2 py-1">Briefing Team</td>
                    <td class="border border-black px-2 py-1 text-center">Meeting Room</td>
                    <td class="border border-black px-2 py-1 text-center">-</td>
                    <td class="border border-black px-2 py-1 text-center">-</td>
                </tr>
                {{-- Add more dummy rows or loop through actual data when available --}}
            </tbody>
        </table>

        {{-- Tabel Evaluasi Harian --}}
        <div class="flex justify-center mb-2">
            <p class="font-semibold self-center">EVALUASI HARIAN</p>
        </div>
        <table class="w-full border border-black mb-6 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-black px-2 py-1">Aspek</th>
                    <th class="border border-black px-2 py-1 w-20">Rating</th>
                    <th class="border border-black px-2 py-1">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-black px-2 py-1">Kedisiplinan Petugas</td>
                    <td class="border border-black px-2 py-1 text-center">Baik</td>
                    <td class="border border-black px-2 py-1">Semua petugas hadir tepat waktu</td>
                </tr>
                <tr>
                    <td class="border border-black px-2 py-1">Kelengkapan Fasilitas</td>
                    <td class="border border-black px-2 py-1 text-center">Baik</td>
                    <td class="border border-black px-2 py-1">Semua fasilitas berfungsi normal</td>
                </tr>
                <tr>
                    <td class="border border-black px-2 py-1">Keamanan Area</td>
                    <td class="border border-black px-2 py-1 text-center">Baik</td>
                    <td class="border border-black px-2 py-1">Tidak ada insiden keamanan</td>
                </tr>
            </tbody>
        </table>

        {{-- Catatan --}}
        <div class="flex justify-center mb-2">
            <p class="font-semibold self-center">CATATAN CHIEF</p>
        </div>
        <div class="border border-black p-4 mb-6 text-sm min-h-20">
            <p>{{ $form->notes ?? 'Tidak ada catatan khusus. Semua aktivitas berjalan normal sesuai prosedur.' }}</p>
        </div>

        {{-- Tanda Tangan --}}
        <div class="mt-10 text-center text-sm">
            <div class="grid grid-cols-1 gap-4">
                {{-- Chief Security --}}
                <div>
                    <p>Chief Security</p>
                    <div class="h-16 flex items-center justify-center">
                        @if(isset($chief) && $chief->chiefSignature)
                        <img src="data:image/png;base64,{!! $chief->chiefSignature !!}" class="h-16 mt-5" alt="Tanda Tangan">
                        @else
                        <span class="italic text-gray-400">Belum tanda tangan</span>
                        @endif
                    </div>
                    <p class="font-semibold mt-1">{{ $form->chief }}</p>
                    <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($form->date)->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</body>

</html>