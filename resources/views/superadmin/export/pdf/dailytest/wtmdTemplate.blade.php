<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WTMD Forms</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>
<body class="m-0 p-0">

    @foreach($forms as $form)
    <div class="page-break-after">
        <div class="bg-white p-4" style="width: 210mm;">
            <div id="format" class="mx-auto">
                <div class="border-t-2 border-x-2 border-black bg-white shadow-md px-4 py-2">
                    <div class="flex items-center justify-between">
                        <img src="{{ public_path('images/airport-security-logo.png') }}" alt="Airport Security Logo" class="w-16 h-16 object-contain">
                        <h1 class="text-xl font-bold text-center flex-grow px-2">
                            CHECK LIST PENGUJIAN HARIAN<br>
                            GAWANG PENDETEKSI LOGAM<br>
                            (WALK THROUGH METAL DETECTOR/WTMD)
                        </h1>
                        <img src="{{ public_path('images/injourney-API.png') }}" alt="Injourney Logo" class="w-16 h-16 object-contain">
                    </div>
                </div>

                <div class="border-2 border-black bg-white shadow">
                    <table class="w-full text-sm">
                        <tbody>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Nama Operator Penerbangan:</th>
                                <td class="w-2/3 p-2">{{ $form->operatorName }}</td>
                            </tr>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Tanggal & Waktu Pengujian:</th>
                                <td class="w-2/3 p-2">{{ date('d-m-Y H:i', strtotime($form->testDateTime)) }} WIB</td>
                            </tr>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Lokasi Penempatan:</th>
                                <td class="w-2/3 p-2">{{ $form->location }}</td>
                            </tr>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Merk/Tipe/Nomor Seri:</th>
                                <td class="w-2/3 p-2">{{ $form->deviceInfo }}</td>
                            </tr>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Nomor dan Tanggal Sertifikat:</th>
                                <td class="w-2/3 p-2">{{ $form->certificateInfo }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="px-4">
                        <div class="p-2">
                            <div class="mb-0">
                                <label class="inline-flex items-center">
                                    <input class="custom-checkbox-alt" type="checkbox" {{ $form->terpenuhi ? 'checked' : '' }} disabled>
                                    <span class="ml-2 text-sm">Terpenuhi</span>
                                </label>
                            </div>
                            <div>
                                <label class="inline-flex items-center">
                                    <input class="custom-checkbox-alt" type="checkbox" {{ $form->tidakTerpenuhi ? 'checked' : '' }} disabled>
                                    <span class="ml-2 text-sm">Tidak Terpenuhi</span>
                                </label>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 border-x-2 border-t-2 border-black text-center items-center">
                            <div class="relative">
                                <div>
                                    <img src="{{ public_path('images/tampakdepan.png') }}" alt="tampakdepan"
                                        class="w-[115px] h-auto mx-auto object-contain">
                                    <p class="text-sm font-semibold text-center mt-2">DEPAN</p>
                                </div>

                                <div class="absolute inset-0 flex flex-col items-start pl-10 pt-36 pointer-events-auto">
                                    <div class="mb-1">
                                        <div class="flex items-center gap-1">
                                            <div class="flex flex-col gap-2">
                                                <div class="flex items-center gap-1 pl-2.5">
                                                    <span class="text-[10px]">IN</span>
                                                    <input class="custom-checkbox-alt" type="checkbox" {{$form->test1_in_depan ? 'checked' : '' }} disabled>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-[10px]">OUT</span>
                                                    <input class="custom-checkbox-alt" type="checkbox" {{$form->test1_out_depan ? 'checked' : '' }} disabled>
                                                </div>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                            <span class="text-xs font-bold">TEST 1</span>
                                        </div>
                                    </div>

                                    <div class="mb-20">
                                        <div class="flex items-center gap-1">
                                            <div class="flex flex-col gap-2">
                                                <div class="flex items-center gap-1 pl-2.5">
                                                    <span class="text-[10px]">IN</span>
                                                    <input class="custom-checkbox-alt" type="checkbox" {{$form->test2_in_depan ? 'checked' : '' }} disabled>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-[10px]">OUT</span>
                                                    <input class="custom-checkbox-alt" type="checkbox" {{$form->test2_out_depan ? 'checked' : '' }} disabled>
                                                </div>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                            <span class="text-xs font-bold">TEST 2</span>
                                        </div>
                                    </div>

                                    <div class="mb-8">
                                        <div class="flex items-center gap-1">
                                            <div class="flex flex-col gap-2">
                                                <div class="flex items-center gap-1 pl-2.5">
                                                    <span class="text-[10px]">IN</span>
                                                    <input class="custom-checkbox-alt" type="checkbox" {{$form->test4_in_depan ? 'checked' : '' }} disabled>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-[10px]">OUT</span>
                                                    <input class="custom-checkbox-alt" type="checkbox" {{$form->test4_out_depan ? 'checked' : '' }} disabled>
                                                </div>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                            <span class="text-xs font-bold">TEST 4</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <div>
                                    <img src="{{ public_path('images/tampakbelakang.png') }}" alt="tampakbelakang"
                                        class="w-[115px] h-auto mx-auto object-contain">
                                    <p class="text-sm font-semibold text-center mt-2">BELAKANG</p>
                                </div>

                                <div class="absolute inset-0 flex flex-col items-end pr-10 pt-4 pointer-events-auto">
                                    <div class="mt-36">
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs font-bold">TEST 3</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 rotate-180"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                            <div class="flex flex-col gap-2">
                                                <div class="flex items-center gap-1 pr-2.5">
                                                    <input class="custom-checkbox-alt" type="checkbox" {{$form->test3_in_belakang ? 'checked' : '' }} disabled>
                                                    <span class="text-[10px]">IN</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <input class="custom-checkbox-alt" type="checkbox" {{$form->test3_out_belakang ? 'checked' : '' }} disabled>
                                                    <span class="text-[10px]">OUT</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t-2 border-black p-4">
                        <div class="flex items-start mb-4">
                            <label class="text-gray-700 font-bold mr-4">Hasil:</label>
                            <div class="flex flex-col">
                                <div class="flex items-center mb-0">
                                    <input class="custom-checkbox-alt" type="radio" class="custom-radio" {{ $form->result == 'pass' ? 'checked' : '' }} disabled>
                                    <label class="text-sm ml-2">PASS</label>
                                </div>
                                <div class="flex items-center">
                                    <input class="custom-checkbox-alt" type="radio" class="custom-radio" {{ $form->result == 'fail' ? 'checked' : '' }} disabled>
                                    <label class="text-sm ml-2">FAIL</label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">CATATAN:</label>
                            <p>{{ $form->notes }}</p>
                        </div>
                    </div>

                    <div class="border-t-2 border-black px-4 py-2">
                        <h3 class="text-sm font-bold">Personel Pengamanan Penerbangan</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid grid-rows-2 gap-2 items-center">
                                <div class="text-center self-end">
                                    <h4 class="font-bold">{{ $form->officerName }}</h4>
                                    <label class="text-gray-700 font-normal">1. Airport Security Officer</label>
                                </div>
                                <div class="text-center self-end">
                                    <h4 class="font-bold">
                                        @if($form->supervisor)
                                            {{ $form->supervisor->name }}
                                        @else
                                            Nama Supervisor tidak tersedia
                                        @endif
                                    </h4>
                                    <label class="text-gray-700 font-normal">2. Airport Security Supervisor</label>
                                </div>
                            </div>
                            <div>
                                <div class="flex flex-col items-center">
                                    @if($form->officer_signature)
                                        <img src="{{ $form->officer_signature }}" alt="Tanda tangan Officer"
                                            class="object-contain"
                                            style="width: 100px; height: 75px;">
                                    @else
                                        <p>Tanda tangan Officer tidak tersedia</p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-center">
                                    @if($form->supervisor_signature)
                                        <img src="{{ $form->supervisor_signature }}" alt="Tanda tangan Supervisor"
                                            class="object-contain"
                                            style="width: 150px; height: 75px;">
                                    @else
                                        <p>Tanda tangan Supervisor tidak tersedia</p>
                                    @endif
                                </div>
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
