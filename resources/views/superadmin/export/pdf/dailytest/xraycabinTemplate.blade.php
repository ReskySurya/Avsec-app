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
                                        <td class="text-center" style="width: 30%; vertical-align: top;">
                                            <p class="text-xs font-bold">TEST 2a</p>
                                            <div class="relative flex border-2 border-black h-16 w-20 mx-auto">
                                                <div class="bg-green-500 flex-1"></div>
                                                <div class="bg-orange-500 flex-1"></div>
                                                <div class="absolute inset-0 flex justify-center items-center">
                                                    <input type="checkbox" class="custom-checkbox-alt" {{ $form->test2aab ? 'checked' : '' }} disabled>
                                                </div>
                                            </div>
                                            <div class="mt-1 text-right mr-4">
                                                <p class="text-xs inline-block font-bold">TEST 2b</p>
                                                <input type="checkbox" class="custom-checkbox-alt" {{ $form->test2bab ? 'checked' : '' }} disabled>
                                            </div>
                                        </td>
                                        <td class="text-center" style="width: 70%; vertical-align: top;">
                                            <p class="text-xs font-bold">TEST 3</p>
                                            <table class="border-2 border-black mx-auto" style="width: 95%; height: 40px;">
                                                <tr class="h-full">
                                                    @foreach([14, 16, 18, 20, 22, 24, 26, 28, 30] as $val)
                                                        <td class="border-r border-black relative bg-cyan-100" style="width: 11.1%;">
                                                            <input type="checkbox" class="custom-checkbox-alt absolute top-1.5" style="left: 5px;" {{ $form->{'test3ab_'.$val} ? 'checked' : '' }} disabled>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            </table>
                                            <table class="mx-auto" style="width: 95%;">
                                                <tr>
                                                    @foreach([14, 16, 18, 20, 22, 24, 26, 28, 30] as $val)
                                                        <td class="text-center text-xs" style="width: 11.1%;">{{ $val }}</td>
                                                    @endforeach
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <table class="w-full" style="margin-top: 5px; border-top: 1px solid black;">
                                    <tr>
                                        <td class="text-center" style="width: 35%; vertical-align: top;">
                                            <p class="text-xs font-bold">TEST 1a & 1b</p>
                                            <table class="mx-auto text-xs">
                                                <tr>
                                                    <td class="transform -rotate-90">AWG</td>
                                                    <td class="transform -rotate-90">36</td>
                                                    <td class="transform -rotate-90">32</td>
                                                    <td class="transform -rotate-90">30</td>
                                                    <td class="transform -rotate-90">24</td>
                                                </tr>
                                                <tr>
                                                    <td>1a</td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1ab_36 ? 'checked' : '' }} disabled></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1ab_32 ? 'checked' : '' }} disabled></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1ab_30 ? 'checked' : '' }} disabled></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1ab_24 ? 'checked' : '' }} disabled></td>
                                                </tr>
                                                <tr>
                                                    <td>1b</td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1bb_36_1 ? 'checked' : '' }} disabled></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1bb_32_1 ? 'checked' : '' }} disabled></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1bb_30_1 ? 'checked' : '' }} disabled></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1bb_24_1 ? 'checked' : '' }} disabled></td>
                                                </tr>
                                                 <tr>
                                                    <td></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1bb_36_2 ? 'checked' : '' }} disabled></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1bb_32_2 ? 'checked' : '' }} disabled></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1bb_30_2 ? 'checked' : '' }} disabled></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1bb_24_2 ? 'checked' : '' }} disabled></td>
                                                </tr>
                                                 <tr>
                                                    <td></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1bb_36_3 ? 'checked' : '' }} disabled></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1bb_32_3 ? 'checked' : '' }} disabled></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1bb_30_3 ? 'checked' : '' }} disabled></td>
                                                    <td><input type="checkbox" class="custom-checkbox-alt" {{ $form->test1bb_24_3 ? 'checked' : '' }} disabled></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text-center" style="width: 50%; vertical-align: top;">
                                            <p class="text-xs font-bold">TEST 4</p>
                                            <div class="bg-sky-400 p-1 border-2 border-black">
                                                <span class="text-xs font-semibold">1.0 mm gaps: </span>
                                                <span class="text-xs">H</span> <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4ab_h10mm ? 'checked' : '' }} disabled>
                                                <span class="text-xs">V</span> <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4ab_v10mm ? 'checked' : '' }} disabled>
                                            </div>
                                            <div class="bg-sky-400 p-1 border-2 border-black mt-1">
                                                <span class="text-xs font-semibold">1.5 mm gaps: </span>
                                                <span class="text-xs">H</span> <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4ab_h15mm ? 'checked' : '' }} disabled>
                                                <span class="text-xs">V</span> <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4ab_v15mm ? 'checked' : '' }} disabled>
                                            </div>
                                            <div class="bg-sky-400 p-1 border-2 border-black mt-1">
                                                <span class="text-xs font-semibold">2.0 mm gaps: </span>
                                                <span class="text-xs">H</span> <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4ab_h20mm ? 'checked' : '' }} disabled>
                                                <span class="text-xs">V</span> <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4ab_v20mm ? 'checked' : '' }} disabled>
                                            </div>
                                        </td>
                                        <td class="text-center" style="width: 15%; vertical-align: top;">
                                            <p class="text-xs font-bold">TEST 5</p>
                                            <div class="flex flex-col items-center">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 border-2 border-black bg-green-200 flex justify-center items-center">
                                                        <input type="checkbox" class="custom-checkbox-alt" {{ $form->test5ab_05mm ? 'checked' : '' }} disabled>
                                                    </div>
                                                    <span class="text-xs ml-1">0.05mm</span>
                                                </div>
                                                <div class="flex items-center mt-1">
                                                    <div class="w-8 h-8 border-2 border-black bg-green-300 flex justify-center items-center">
                                                        <input type="checkbox" class="custom-checkbox-alt" {{ $form->test5ab_10mm ? 'checked' : '' }} disabled>
                                                    </div>
                                                    <span class="text-xs ml-1">0.10mm</span>
                                                </div>
                                                <div class="flex items-center mt-1">
                                                    <div class="w-8 h-8 border-2 border-black bg-green-400 flex justify-center items-center">
                                                        <input type="checkbox" class="custom-checkbox-alt" {{ $form->test5ab_15mm ? 'checked' : '' }} disabled>
                                                    </div>
                                                    <span class="text-xs ml-1">0.15mm</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="border-t-2 border-black mt-2 pt-1">
                            <h3 class="text-center font-bold">GENERATOR SAMPING</h3>
                            <div class="border-2 border-black mx-2 p-1">
                                <table class="w-full">
                                    <tr>
                                        <td class="text-center" style="width: 30%; vertical-align: top;">
                                            <p class="text-xs font-bold">TEST 2a</p>
                                            <div class="relative flex border-2 border-black h-16 w-20 mx-auto">
                                                <div class="bg-green-500 flex-1"></div>
                                                <div class="bg-orange-500 flex-1"></div>
                                                <div class="absolute inset-0 flex justify-center items-center">
                                                    <input type="checkbox" class="custom-checkbox-alt" {{ $form->test2ab ? 'checked' : '' }} disabled>
                                                </div>
                                            </div>
                                            <div class="mt-1 text-right mr-4">
                                                <p class="text-xs inline-block font-bold">TEST 2b</p>
                                                <input type="checkbox" class="custom-checkbox-alt" {{ $form->test2bb ? 'checked' : '' }} disabled>
                                            </div>
                                        </td>
                                        <td class="text-center" style="width: 70%; vertical-align: top;">
                                            <p class="text-xs font-bold">TEST 3</p>
                                            <table class="border-2 border-black mx-auto" style="width: 95%; height: 40px;">
                                                <tr class="h-full">
                                                    @foreach([14, 16, 18, 20, 22, 24, 26, 28, 30] as $val)
                                                        <td class="border-r border-black relative bg-cyan-100" style="width: 11.1%;">
                                                            <input type="checkbox" class="custom-checkbox-alt absolute top-1.5" style="left: 5px;" {{ $form->{'test3b_'.$val} ? 'checked' : '' }} disabled>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            </table>
                                            <table class="mx-auto" style="width: 95%;">
                                                <tr>
                                                    @foreach([14, 16, 18, 20, 22, 24, 26, 28, 30] as $val)
                                                        <td class="text-center text-xs" style="width: 11.1%;">{{ $val }}</td>
                                                    @endforeach
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <table class="w-full" style="margin-top: 5px; border-top: 1px solid black;">
                                    <tr>
                                        <td class="text-center" style="width: 85%; vertical-align: top;">
                                            <p class="text-xs font-bold">TEST 4</p>
                                            <div class="bg-sky-400 p-1 border-2 border-black">
                                                <span class="text-xs font-semibold">1.0 mm gaps: </span>
                                                <span class="text-xs">H</span> <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4b_h10mm ? 'checked' : '' }} disabled>
                                                <span class="text-xs">V</span> <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4b_v10mm ? 'checked' : '' }} disabled>
                                            </div>
                                            <div class="bg-sky-400 p-1 border-2 border-black mt-1">
                                                <span class="text-xs font-semibold">1.5 mm gaps: </span>
                                                <span class="text-xs">H</span> <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4b_h15mm ? 'checked' : '' }} disabled>
                                                <span class="text-xs">V</span> <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4b_v15mm ? 'checked' : '' }} disabled>
                                            </div>
                                            <div class="bg-sky-400 p-1 border-2 border-black mt-1">
                                                <span class="text-xs font-semibold">2.0 mm gaps: </span>
                                                <span class="text-xs">H</span> <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4b_h20mm ? 'checked' : '' }} disabled>
                                                <span class="text-xs">V</span> <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4b_v20mm ? 'checked' : '' }} disabled>
                                            </div>
                                        </td>
                                        <td class="text-center" style="width: 15%; vertical-align: top;">
                                            <p class="text-xs font-bold">TEST 5</p>
                                            <div class="flex flex-col items-center">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 border-2 border-black bg-green-200 flex justify-center items-center">
                                                        <input type="checkbox" class="custom-checkbox-alt" {{ $form->test5b_05mm ? 'checked' : '' }} disabled>
                                                    </div>
                                                    <span class="text-xs ml-1">0.05mm</span>
                                                </div>
                                                <div class="flex items-center mt-1">
                                                    <div class="w-8 h-8 border-2 border-black bg-green-300 flex justify-center items-center">
                                                        <input type="checkbox" class="custom-checkbox-alt" {{ $form->test5b_10mm ? 'checked' : '' }} disabled>
                                                    </div>
                                                    <span class="text-xs ml-1">0.10mm</span>
                                                </div>
                                                <div class="flex items-center mt-1">
                                                    <div class="w-8 h-8 border-2 border-black bg-green-400 flex justify-center items-center">
                                                        <input type="checkbox" class="custom-checkbox-alt" {{ $form->test5b_15mm ? 'checked' : '' }} disabled>
                                                    </div>
                                                    <span class="text-xs ml-1">0.15mm</span>
                                                </div>
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