<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>X-RAY CABIN Forms</title>
    <style>
        {!! file_get_contents(public_path('css/pdf.css')) !!}
    </style>
</head>
<body class="m-0 p-0">
@php
    $logoAirportBase64 = base64_encode(file_get_contents(public_path('images/airport-security-logo.png')));
    $logoInjourneyBase64 = base64_encode(file_get_contents(public_path('images/injourney-API.png')));
@endphp
    @foreach($forms as $form)
    <div class="page-break-after">
        <div class="bg-white p-4" style="width: 200mm;">
            <div id="format" class="mx-auto">
                <div class="border-t-2 border-x-2 border-black bg-white shadow-md py-2">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="width: 20%; text-align: center; vertical-align: middle;">
                                    <img src="data:image/png;base64,{{ $logoAirportBase64 }}" alt="Logo" style="width: 64px; height: 64px; display: inline-block;">
                                </td>
                                <td style="width: 60%; text-align: center; vertical-align: middle;">
                                    <h3 style="font-size: 12px; font-weight: bold; line-height: 1.3;">
                                        CHECK LIST PENGUJIAN HARIAN<br>
                                        MESIN X-RAY CABIN MULTI VIEW
                                    </h3>
                                </td>
                                <td style="width: 20%; text-align: center; vertical-align: middle;">
                                    <img src="data:image/png;base64,{{ $logoInjourneyBase64 }}" alt="Injourney Logo" style="width: 80px; height: 64px; display: inline-block;">
                                </td>
                            </tr>
                        </tbody>
                    </table>
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

                    <div class="p-2 text-xs">
                        <div class="flex flex-col">
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="custom-checkbox-alt" {{ $form->terpenuhi ? 'checked' : '' }} disabled>
                                <span class="ml-1 font-semibold">Terpenuhi</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="custom-checkbox-alt" {{ $form->tidakTerpenuhi ? 'checked' : '' }} disabled>
                                <span class="ml-1 font-semibold">Tidak Terpenuhi</span>
                            </label>
                        </div>

                        <!-- X-Ray Tests Here -->
                        <div class="border-t-2 border-black mt-2 pt-1">
                            <h3 class="text-center font-bold">GENERATOR ATAS/BAWAH</h3>
                            <div class="border-2 border-black mx-2 p-1">
                                <table class="w-full">
                                    <tr>
                                        <td class="text-center" style="width: 33%;">
                                            <p class="text-xs">TEST 2a</p>
                                            <div class="relative flex border-2 border-black h-20 w-24 mx-auto">
                                                <div class="bg-green-500 flex-1"></div>
                                                <div class="bg-orange-500 flex-1"></div>
                                                <div class="absolute inset-0 flex justify-center items-center">
                                                    <input type="checkbox" class="custom-checkbox-alt" {{ $form->test2aab ? 'checked' : '' }} disabled>
                                                </div>
                                            </div>
                                            <div class="mt-1">
                                                <p class="text-xs inline-block">TEST 2b</p>
                                                <input type="checkbox" class="custom-checkbox-alt" {{ $form->test2bab ? 'checked' : '' }} disabled>
                                            </div>
                                        </td>
                                        <td class="text-center" style="width: 33%;">
                                            <p class="text-xs">TEST 3</p>
                                            <table class="border-2 border-black mx-auto" style="width: 90%; height: 60px;">
                                                <tr class="h-full">
                                                    @foreach([14, 16, 18, 20, 22, 24, 26, 28, 30] as $val)
                                                        <td class="border-r border-black relative bg-blue-100" style="width: 11.1%;">
                                                            <input type="checkbox" class="custom-checkbox-alt absolute top-1.5" {{ $form->{'test3ab_'.$val} ? 'checked' : '' }} disabled>
                                                            <div class="absolute w-full h-0.5 border-t border-black bg-black top-1/2"></div>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            </table>
                                            <table class="mx-auto" style="width: 90%;">
                                                <tr>
                                                    @foreach([14, 16, 18, 20, 22, 24, 26, 28, 30] as $val)
                                                        <td class="text-center" style="width: 11.1%;">{{ $val }}</td>
                                                    @endforeach
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text-center" style="width: 33%;">
                                            <p class="text-xs">TEST 5</p>
                                            <div class="flex justify-center items-center">
                                                @foreach(['05mm', '10mm', '15mm'] as $val)
                                                <div class="w-12 h-12 border-2 border-black bg-green-200 flex justify-center items-center">
                                                    <input type="checkbox" class="custom-checkbox-alt" {{ $form->{'test5ab_'.$val} ? 'checked' : '' }} disabled>
                                                </div>
                                                <span class="text-xs transform -rotate-90">{{ str_replace('mm', '.', $val) }}mm</span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="border-t-2 border-black p-4">
                        <div class="flex items-start">
                            <label class="text-gray-700 font-bold mr-4">Hasil:</label>
                            <div class="flex flex-col">
                                <div class="flex items-center ">
                                    <input type="radio" class="" {{ $form->result == 'pass' ? 'checked' : '' }} disabled>
                                    <label class="text-sm">PASS</label>
                                </div>
                                <div class="flex items-center ">
                                    <input type="radio" class="" {{ $form->result == 'fail' ? 'checked' : '' }} disabled>
                                    <label class="text-sm">FAIL</label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">CATATAN:</label>
                            <p>{{ $form->notes }}</p>
                        </div>
                    </div>

                    <div class="border-t-2 border-black px-4 py-2">
                        <h3 class="text-sm font-bold mb-1">Personel Pengamanan Penerbangan</h3>
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
                                        <img src="{{ $form->officer_signature }}" alt="Tanda tangan Officer" style="width: 150px; height: auto;">
                                    @else
                                        <p>Tanda tangan Officer tidak tersedia</p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-center">
                                    @if($form->supervisor_signature)
                                        <img src="{{ $form->supervisor_signature }}" alt="Tanda tangan Supervisor" id="supervisorSignatureImage" style="width: 150px; height: auto;">
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