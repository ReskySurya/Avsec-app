<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WTMD Forms</title>
    <style>
        {!! file_get_contents(public_path('css/pdf.css')) !!}
    </style>
</head>
<body class="m-0 p-0">
@php
    $logoAirportBase64 = base64_encode(file_get_contents(public_path('images/airport-security-logo.png')));
    $logoInjourneyBase64 = base64_encode(file_get_contents(public_path('images/injourney-API.png')));
    $tampakDepanBase64 = base64_encode(file_get_contents(public_path('images/tampakdepan.png')));
    $tampakBelakangBase64 = base64_encode(file_get_contents(public_path('images/tampakbelakang.png')));
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
                                        GAWANG PENDETEKSI LOGAM<br>
                                        (WALK THROUGH METAL DETECTOR/WTMD)
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

                    <div class="px-4 py-2">
                        <div class="mb-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="custom-checkbox-alt" {{ $form->terpenuhi ? 'checked' : '' }} disabled>
                                <span class="ml-2 text-sm">Terpenuhi</span>
                            </label>
                            <label class="inline-flex items-center ml-4">
                                <input type="checkbox" class="custom-checkbox-alt" {{ $form->tidakTerpenuhi ? 'checked' : '' }} disabled>
                                <span class="ml-2 text-sm">Tidak Terpenuhi</span>
                            </label>
                        </div>

                        <div style="display: flex; justify-content: space-around; align-items: flex-start;">
                            <!-- Tampak Depan -->
                            <div style="text-align: center; position: relative;">
                                <img src="data:image/png;base64,{{ $tampakDepanBase64 }}" alt="Tampak Depan" style="width: 150px; margin-bottom: 5px;">
                                <div style="position: absolute; top: 20px; left: -60px; font-size: 10px;">
                                    TEST 1
                                    <div style="display: flex; align-items: center;">
                                        <div style="margin-right: 5px;">
                                            IN <input type="checkbox" class="custom-checkbox-alt" {{ $form->test1_in_depan ? 'checked' : '' }} disabled><br>
                                            OUT <input type="checkbox" class="custom-checkbox-alt" {{ $form->test1_out_depan ? 'checked' : '' }} disabled>
                                        </div>
                                        <span>&rarr;</span>
                                    </div>
                                </div>
                                <div style="position: absolute; top: 120px; left: -60px; font-size: 10px;">
                                    TEST 2
                                     <div style="display: flex; align-items: center;">
                                        <div style="margin-right: 5px;">
                                            IN <input type="checkbox" class="custom-checkbox-alt" {{ $form->test2_in_depan ? 'checked' : '' }} disabled><br>
                                            OUT <input type="checkbox" class="custom-checkbox-alt" {{ $form->test2_out_depan ? 'checked' : '' }} disabled>
                                        </div>
                                        <span>&rarr;</span>
                                    </div>
                                </div>
                                <div style="position: absolute; top: 70px; right: -60px; font-size: 10px;">
                                    TEST 4
                                     <div style="display: flex; align-items: center;">
                                        <span>&larr;</span>
                                        <div style="margin-left: 5px;">
                                            IN <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4_in_depan ? 'checked' : '' }} disabled><br>
                                            OUT <input type="checkbox" class="custom-checkbox-alt" {{ $form->test4_out_depan ? 'checked' : '' }} disabled>
                                        </div>
                                    </div>
                                </div>
                                <strong>DEPAN</strong>
                            </div>

                            <!-- Tampak Belakang -->
                            <div style="text-align: center; position: relative;">
                                <img src="data:image/png;base64,{{ $tampakBelakangBase64 }}" alt="Tampak Belakang" style="width: 150px; margin-bottom: 5px;">
                                <div style="position: absolute; top: 70px; left: -60px; font-size: 10px;">
                                    TEST 3
                                     <div style="display: flex; align-items: center;">
                                        <div style="margin-right: 5px;">
                                            IN <input type="checkbox" class="custom-checkbox-alt" {{ $form->test3_in_belakang ? 'checked' : '' }} disabled><br>
                                            OUT <input type="checkbox" class="custom-checkbox-alt" {{ $form->test3_out_belakang ? 'checked' : '' }} disabled>
                                        </div>
                                        <span>&rarr;</span>
                                    </div>
                                </div>
                                <strong>BELAKANG</strong>
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