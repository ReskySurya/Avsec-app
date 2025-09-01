<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HHMD Forms</title>
    <style>
        {
            ! ! file_get_contents(public_path('css/pdf.css')) ! !
        }
    </style>
</head>

<body class="m-0 p-0">
    <div class="border-t-2 border-x-2 border-black bg-white shadow-md p-6 m-6">
        @php
        $logoAirportBase64 = base64_encode(file_get_contents(public_path('images/airport-security-logo.png')));
        $logoInjourneyBase64 = base64_encode(file_get_contents(public_path('images/injourney-API.png')));
        @endphp
        <div class="flex flex-col sm:flex-row items-center justify-between">
            <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo" class="w-20 h-20 mb-2 sm:mb-0">
            <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
                LOGBOOK HARIAN <br>
                CATATAN AKTIVITAS HARIAN <br>
                POS JAGA
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
                <p>LOKASI <span class="font-semibold">: {{ $form->location }}</span></p>
                <p>DINAS / SHIFT <span class="font-semibold">: {{ strtoupper($form->shift) }}</span></p>
                <p>GRUP <span class="font-semibold">: {{ strtoupper($form->grup) }}</span></p>
            </div>
        </div>

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
                @if(isset($form->personil) && $form->personil)
                @foreach($form->personil as $index => $personil)
                <tr>
                    <td class="border border-black px-2 py-1 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black px-2 py-1">{{ $personil->user->name ?? $personil->name ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1 text-center">{{ $personil->classification ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1 text-center">{{ $personil->description ?? 'N/A' }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td class="border border-black px-2 py-1 text-center" colspan="4">
                        <span class="italic text-gray-400">Data petugas tidak tersedia</span>
                    </td>
                </tr>
                @endif
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
                @if(isset($form->facility) && $form->facility)
                @foreach($form->facility as $index => $facility)
                <tr>
                    <td class="border border-black px-2 py-1 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black px-2 py-1">{{ $facility->facility ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1 text-center">{{ $facility->quantity ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1 text-center">{{ $facility->description ?? 'N/A' }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td class="border border-black px-2 py-1 text-center" colspan="4">
                        <span class="italic text-gray-400">Data fasilitas tidak tersedia</span>
                    </td>
                </tr>
                @endif
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
                @if(isset($form->logbookDetails) && $form->logbookDetails)
                @foreach($form->logbookDetails as $index => $detail)
                <tr>
                    <td class="border border-black px-2 py-1 text-center">
                        {{ \Carbon\Carbon::parse($detail->start_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($detail->end_time)->format('H:i') }}
                    </td>
                    <td class="border border-black px-2 py-1">{{ $detail->summary ?? 'N/A' }}</td>
                    <td class="border border-black px-2 py-1">{{ $detail->description ?? 'N/A' }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td class="border border-black px-2 py-1 text-center" colspan="3">
                        <span class="italic text-gray-400">Data uraian tugas tidak tersedia</span>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        {{-- Tanda Tangan --}}
        <div class="mt-10 text-center text-sm">
            <div class="grid grid-cols-2 gap-4">
                {{-- Kiri: Yang Menerima --}}
                <div>
                    <p>Yang Menerima</p>
                    <div class="h-16 flex items-center justify-center">
                        @if($form->receivedSignature)
                        <img src="data:image/png;base64,{!! $form->receivedSignature !!}" class="h-16 mt-5"
                            alt="Tanda Tangan">
                        @else
                        <span class="italic text-gray-400">Belum tanda tangan</span>
                        @endif
                    </div>
                    <p class="font-semibold mt-1">{{ $form->receiverName }}</p>
                </div>

                {{-- Kanan: Yang Menyerahkan --}}
                <div>
                    <p>Yang Menyerahkan</p>
                    <div class="h-16 flex items-center justify-center">
                        @if($form->senderSignature)
                        <img src="data:image/png;base64,{!! $form->senderSignature !!}" class="h-16 mt-5"
                            alt="Tanda Tangan">
                        @else
                        <span class="italic text-gray-400">Belum tanda tangan</span>
                        @endif
                    </div>
                    <p class="font-semibold mt-1">{{ $form->senderName }}</p>
                </div>
            </div>

            {{-- Bawah Tengah: Chief Screening --}}
            <div class="mt-6">
                <p>Mengetahui,</p>
                <div class="h-16 flex items-center justify-center">
                    @if($form->approvedSignature)
                    <img src="data:image/png;base64,{!! $form->approvedSignature !!}" class="h-16 mt-5"
                        alt="Tanda Tangan Mengetahui">
                    @else
                    <span class="italic text-gray-400">Belum tanda tangan</span>
                    @endif
                </div>
                <p class="font-semibold mt-1">{{ $form->approverName }}</p>
            </div>
        </div>
        @endforeach
    </div>
</body>

</html>