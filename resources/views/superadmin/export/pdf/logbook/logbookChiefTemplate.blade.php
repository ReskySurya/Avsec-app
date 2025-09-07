<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forms - Chief</title>
    <style>
        {
            ! ! file_get_contents(public_path('css/pdf.css')) ! !
        }
    </style>
</head>

<body class="m-0 p-0">
    @foreach($forms as $chief)
    <div class="border-t-2 border-x-2 border-black bg-white shadow-md p-6 m-6 page-break-after">
        @php
        $logoAirportBase64 = base64_encode(file_get_contents(public_path('images/airport-security-logo.png')));
        $logoInjourneyBase64 = base64_encode(file_get_contents(public_path('images/injourney-API.png')));
        @endphp

        <div class="flex flex-col sm:flex-row items-center justify-between">
            <img src="data:image/png;base64,{{ $logoAirportBase64 }}" alt="Logo" class="w-20 h-20 mb-2 sm:mb-0">
            <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
                LOGBOOK PELAPORAN <br>
                CATATAN AKTIVITAS TEAM LEADER <br>
            </h1>
            <img src="data:image/png;base64,{{ $logoInjourneyBase64 }}" alt="Injourney Logo" class="w-20 h-20 mt-2 sm:mt-0">
        </div>

        <!-- Informasi detail -->
        <div class="border-t border-gray-300 pt-3 mt-4 mb-6">
            <div class="grid grid-cols-2 gap-y-2 text-sm text-gray-700">
                <p>HARI / TANGGAL
                    <span class="font-semibold">
                        : {{ \Carbon\Carbon::parse($chief->date)->translatedFormat('l, d F Y') }}
                    </span>
                </p>
                <p>GRUP <span class="font-semibold">: {{ $chief->grup }}</span></p>
                <p>CHIEF <span class="font-semibold">: {{ $chief->chief }}</span></p>
                <p>STATUS <span class="font-semibold">: {{ strtoupper($chief->status) }}</span></p>
            </div>
        </div>

        {{-- Tabel Aktivitas Chief --}}
        <div class="flex justify-center mb-2">
            <p class="font-semibold self-center">CHIEF KEMAJUAN</p>
        </div>
        <table class="w-full border border-black mb-6 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-black px-2 py-1 w-10">No</th>
                    <th class="border border-black px-2 py-1">Jumlah Personil</th>
                    <th class="border border-black px-2 py-1">Jumlah Hadir</th>
                    <th class="border border-black px-2 py-1">Jumlah Kekuatan</th>
                    <th class="border border-black px-2 py-1">Materi Apel</th>
                    <th class="border border-black px-2 py-1">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chief->kemajuan as $index => $kemajuan)
                <tr>
                    <td class="border border-black px-2 py-1 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black px-2 py-1">{{ $kemajuan->jml_personil ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1">{{ $kemajuan->jml_hadir ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1 text-center">{{ $kemajuan->jml_kekuatan ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1 text-center">{{ $kemajuan->materi ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1 text-center">{{ $kemajuan->keterangan ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td class="border border-black px-2 py-1 text-center" colspan="4">
                        <span class="italic text-gray-400">Data petugas tidak tersedia</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tabel Petugas Jaga --}}
        <div class="flex justify-center mb-2">
            <p class="font-semibold self-center">PETUGAS JAGA</p>
        </div>
        <table class="w-full border border-black mb-6 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-black px-2 py-1 w-10">No</th>
                    <th class="border border-black px-2 py-1">Nama Petugas</th>
                    <th class="border border-black px-2 py-1 w-20">Klasifikasi</th>
                    <th class="border border-black px-2 py-1 w-20">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chief->personil as $index => $personil)
                <tr>
                    <td class="border border-black px-2 py-1 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black px-2 py-1">{{ $personil->user->name ?? $personil->name ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1 text-center">{{ $personil->classification ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1 text-center">{{ $personil->description ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td class="border border-black px-2 py-1 text-center" colspan="4">
                        <span class="italic text-gray-400">Data petugas tidak tersedia</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tabel Fasilitas --}}
        <div class="flex justify-center mb-2">
            <p class="font-semibold self-center">FASILITAS</p>
        </div>
        <table class="w-full border border-black mb-6 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-black px-2 py-1 w-10">No</th>
                    <th class="border border-black px-2 py-1">Fasilitas</th>
                    <th class="border border-black px-2 py-1 w-20">Jumlah</th>
                    <th class="border border-black px-2 py-1 w-20">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chief->facility as $index => $facility)
                <tr>
                    <td class="border border-black px-2 py-1 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black px-2 py-1">{{ $facility->facility ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1 text-center">{{ $facility->quantity ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1 text-center">{{ $facility->description ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td class="border border-black px-2 py-1 text-center" colspan="4">
                        <span class="italic text-gray-400">Data fasilitas tidak tersedia</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tabel Uraian Tugas --}}
        <div class="flex justify-center mb-2">
            <p class="font-semibold self-center">URAIAN TUGAS</p>
        </div>
        <table class="w-full border border-black mb-6 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-black px-2 py-1">Jam</th>
                    <th class="border border-black px-2 py-1">Uraian Tugas</th>
                    <th class="border border-black px-2 py-1">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chief->logbookDetails as $index => $detail)
                <tr>
                    <td class="border border-black px-2 py-1 text-center">
                        {{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($detail->end_time)->format('H:i') }}
                    </td>
                    <td class="border border-black px-2 py-1">{{ $detail->summary ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1">{{ $detail->description ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td class="border border-black px-2 py-1 text-center" colspan="3">
                        <span class="italic text-gray-400">Data uraian tugas tidak tersedia</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tanda Tangan --}}
        <div class="mt-10 text-center text-sm">
            <div class="grid grid-cols-2 gap-4">
                {{-- Kiri: Chief Security --}}
                <div>
                    <p>Chief Security</p>
                    <div class="h-16 flex items-center justify-center">
                        @if(isset($chief) && $chief->chiefSignature)
                        <img src="data:image/png;base64,{{ $chief->chiefSignature }}" class="h-16 mt-5" alt="Tanda Tangan">
                        @else
                        <span class="italic text-gray-400">Belum tanda tangan</span>
                        @endif
                    </div>
                    <p class="font-semibold mt-1">{{ $chief->chief }}</p>
                </div>

                {{-- Kanan: Supervisor --}}
                <div>
                    <p>Supervisor</p>
                    <div class="h-16 flex items-center justify-center">
                        <span class="italic text-gray-400">Belum tanda tangan</span>
                    </div>
                    <p class="font-semibold mt-1">( Nama Supervisor )</p>
                </div>
            </div>

            <!-- {{-- Bawah Tengah: Mengetahui --}}
            <div class="mt-6">
                <p>Mengetahui,</p>
                <div class="h-16 flex items-center justify-center">
                    <span class="italic text-gray-400">Belum tanda tangan</span>
                </div>
                <p class="font-semibold mt-1">( Nama Manager )</p>
            </div> -->
        </div>
    </div>
    @endforeach
</body>

</html>