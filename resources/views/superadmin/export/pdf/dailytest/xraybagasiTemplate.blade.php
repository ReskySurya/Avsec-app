<!DOCTYPE html>
<html lang="id" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>X-RAY BAGASI Forms</title>
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
                <div class="border-t-2 border-x-2 border-black bg-white shadow-md p-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between">
                        <img src="data:image/png;base64,{{ $logoAirportBase64 }}" alt="Logo"
                            style="width: 80px; height: 80px; margin-bottom: 8px;">
                        <h1 style="font-size: 16px; font-weight: bold; text-align: center; flex-grow: 1; padding: 0 8px;">
                            CHECK LIST PENGUJIAN HARIAN<br>
                            MESIN X-RAY BAGASI MULTIVIEW<br>
                        </h1>
                        <img src="data:image/png;base64,{{ $logoInjourneyBase64 }}" alt="Injourney Logo" 
                            style="width: 80px; height: 80px; margin-top: 8px;">
                    </div>
                </div>

                <div class="border-2 border-black bg-white shadow">
                    <table class="w-full text-xs sm:text-sm">
                        <tbody>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-1 sm:p-2">Nama Operator Penerbangan:</th>
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

                    <div class="px-1">
                        <div class="p-2">
                            <div class="mb-0">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" {{ $form->terpenuhi ? 'checked' : '' }} disabled>
                                    <span class="ml-2 text-sm">Terpenuhi</span>
                                </label>
                            </div>
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" {{ $form->tidakTerpenuhi ? 'checked' : '' }} disabled>
                                    <span class="ml-2 text-sm">Tidak Terpenuhi</span>
                                </label>
                            </div>
                        </div>

                        <h2 class="font-bold text-sm mb-1 text-center">GENERATOR ATAS/BAWAH</h2>
                        <div class="border-2 border-black p-2 mb-2">
                            <div style="display: grid; grid-template-columns: 30% 70%; gap: 8px; margin-bottom: 16px;">
                                <div class="p-2">
                                    <h3 class="text-xs font-bold mb-2 text-center">TEST 2a</h3>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; position: relative;">
                                        <input type="checkbox" {{ $form->test2aab ? 'checked' : '' }}
                                        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10;" disabled>
                                        <div style="background-color: #10b981; height: 64px; display: flex; align-items: center; justify-content: center;"></div>
                                        <div style="background-color: #f97316; height: 64px; display: flex; align-items: center; justify-content: center;"></div>
                                    </div>
                                    <div style="display: grid; grid-template-columns: 70% 30%; position: relative;">
                                        <h3 class="text-xs font-bold" style="text-align: right;">TEST 2b</h3>
                                        <input type="checkbox" {{ $form->test2bab ? 'checked' : '' }}
                                        style="position: absolute; right: 0; bottom: 0;" disabled>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <h3 class="text-xs font-bold mb-2 text-center">TEST 3</h3>
                                    <div style="display: grid; grid-template-columns: repeat(9, 1fr); gap: 0; position: relative;">
                                        @foreach([14, 16, 18, 20, 22, 24, 26, 28, 30] as $index => $val)
                                            <div style="background-color: {{ ['#e0f2fe', '#b3e5fc', '#0ea5e9', '#3b82f6', '#3b82f6', '#1d4ed8', '#1d4ed8', '#1e3a8a', '#1e3a8a'][$index] }}; height: 64px; display: flex; align-items: center; justify-content: center;">
                                                <input type="checkbox" {{ $form->{'test3ab_'.$val} ? 'checked' : '' }}
                                                style="position: absolute; top: 16px;" disabled>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div style="display: grid; grid-template-columns: repeat(9, 1fr); gap: 0;">
                                        @foreach([14, 16, 18, 20, 22, 24, 26, 28, 30] as $val)
                                            <p class="text-xs" style="transform: rotate(-90deg);">{{ $val }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 35% 50% 15%; gap: 4px; margin-bottom: 8px; padding-right: 4px;">
                                <div>
                                    <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 0; height: 128px;">
                                        <div style="display: grid; grid-rows: 1fr; background-color: white; align-items: center; justify-content: center;">
                                            <p class="text-xs" style="transform: rotate(-90deg);">AWG</p>
                                        </div>
                                        <div style="display: grid; grid-template-rows: repeat(4, 1fr); background-color: white; align-items: center; justify-content: center;">
                                            @foreach([36, 32, 30, 24] as $val)
                                                <p class="text-xs" style="transform: rotate(-90deg);">{{ $val }}</p>
                                            @endforeach
                                        </div>
                                        <div style="display: grid; grid-template-rows: repeat(4, 1fr); background-color: white; align-items: center; justify-content: center;">
                                            @foreach([36, 32, 30, 24] as $val)
                                                <input type="checkbox" {{ $form->{'test1aab_'.$val} ? 'checked' : '' }} disabled>
                                            @endforeach
                                        </div>
                                        <div style="display: grid; grid-template-rows: repeat(4, 1fr); background-color: #C5E0B3; border: 1px solid black; align-items: center; justify-content: center;">
                                            @foreach([36, 32, 30, 24] as $val)
                                                <input type="checkbox" {{ $form->{'test1bb_'.$val.'_1'} ? 'checked' : '' }} disabled>
                                            @endforeach
                                        </div>
                                        <div style="display: grid; grid-template-rows: repeat(4, 1fr); background-color: #92D050; border-top: 1px solid black; border-bottom: 1px solid black; align-items: center; justify-content: center;">
                                            @foreach([36, 32, 30, 24] as $val)
                                                <input type="checkbox" {{ $form->{'test1bb_'.$val.'_2'} ? 'checked' : '' }} disabled>
                                            @endforeach
                                        </div>
                                        <div style="display: grid; grid-template-rows: repeat(4, 1fr); background-color: #10b981; border: 1px solid black; align-items: center; justify-content: center;">
                                            @foreach([36, 32, 30, 24] as $val)
                                                <input type="checkbox" {{ $form->{'test1bb_'.$val.'_3'} ? 'checked' : '' }} disabled>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div style="display: grid; grid-template-columns: 50% 50%; margin-top: 12px;">
                                        <h3 class="text-xs font-bold text-center">TEST 1a</h3>
                                        <h3 class="text-xs font-bold" style="text-align: right;">TEST 1b</h3>
                                    </div>
                                </div>

                                <div>
                                    <div style="display: grid; grid-template-rows: repeat(3, 1fr); gap: 0;">
                                        @php
                                            $gapTestsB = [
                                                ['size' => '1.5', 'h_field' => 'test4b_h15mm', 'v_field' => 'test4b_v15mm'],
                                                ['size' => '2.0', 'h_field' => 'test4b_h20mm', 'v_field' => 'test4b_v20mm'],
                                                ['size' => '1.0', 'h_field' => 'test4b_h10mm', 'v_field' => 'test4b_v10mm']
                                            ];
                                        @endphp
                                        @foreach($gapTestsB as $test)
                                            <div style="display: grid; grid-template-columns: {{ $test['size'] == '2.0' ? '65% 35%' : '40% 60%' }}; background-color: #0ea5e9; align-items: center; justify-content: {{ $test['size'] == '2.0' ? 'flex-end' : 'center' }}; position: relative;">
                                                <div style="display: grid; grid-template-rows: repeat(4, 1fr); font-size: 12px; height: 24px; padding-left: {{ $test['size'] == '2.0' ? '40px' : '4px' }};">
                                                    <p style="font-size: 8px; font-weight: 600; position: absolute; top: -4px;">{{ $test['size'] }} mm gaps</p>
                                                    <input type="checkbox" {{ $form->{$test['h_field']} ? 'checked' : '' }}
                                                    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); height: 24px; width: 24px;" disabled>
                                                    @for($i = 0; $i < 4; $i++)
                                                        <div style="border: 1px solid black; background-color: white; height: 4px;"></div>
                                                    @endfor
                                                </div>
                                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0; font-size: 12px; height: {{ $test['size'] == '1.0' ? '44px' : ($test['size'] == '2.0' ? '32px' : '28px') }}; padding: {{ $test['size'] == '1.0' ? '36px 4px' : ($test['size'] == '2.0' ? '0 8px' : '0 32px') }};">
                                                    <input type="checkbox" {{ $form->{$test['v_field']} ? 'checked' : '' }}
                                                    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); height: {{ $test['size'] == '2.0' ? '16px' : '20px' }}; width: {{ $test['size'] == '2.0' ? '16px' : '20px' }};" disabled>
                                                    @for($i = 0; $i < 4; $i++)
                                                        <div style="border: 1px solid black; background-color: white; width: 4px;"></div>
                                                    @endfor
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <h3 class="text-xs font-bold mt-2 text-center">TEST 4</h3>
                                </div>

                                <div>
                                    <div style="display: grid; grid-template-columns: 80% 20%; gap: 0;">
                                        <div style="display: grid; grid-template-rows: repeat(3, 1fr);">
                                            @php
                                                $thicknessTestsB = [
                                                    ['color' => '#C5E0B3', 'field' => 'test5b_05mm', 'label' => '0.05mm'],
                                                    ['color' => '#A8D08D', 'field' => 'test5b_10mm', 'label' => '0.10mm'],
                                                    ['color' => '#548135', 'field' => 'test5b_15mm', 'label' => '0.15mm']
                                                ];
                                            @endphp
                                            @foreach($thicknessTestsB as $test)
                                                <div style="background-color: {{ $test['color'] }}; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    <input type="checkbox" {{ $form->{$test['field']} ? 'checked' : '' }} disabled>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div style="display: grid; grid-template-rows: repeat(3, 1fr); padding-left: 4px;">
                                            @foreach($thicknessTestsB as $test)
                                                <p style="font-size: 8px; transform: rotate(-90deg);">{{ $test['label'] }}</p>
                                            @endforeach
                                        </div>
                                    </div>
                                    <h3 class="text-xs font-bold mt-5 text-center">TEST 5</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t-2 border-black p-4">
                        <div class="flex items-start mb-2">
                            <label class="text-gray-700 font-bold mr-4">Hasil:</label>
                            <div class="flex flex-col">
                                <div class="flex items-center mb-0">
                                    <input type="radio" {{ $form->result == 'pass' ? 'checked' : '' }} disabled>
                                    <label class="text-sm ml-2">PASS</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" {{ $form->result == 'fail' ? 'checked' : '' }} disabled>
                                    <label class="text-sm ml-2">FAIL</label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="notes" class="block text-gray-700 font-bold mb-2">CATATAN:</label>
                            <p>{{ $form->notes }}</p>
                        </div>
                    </div>

                    <div class="border-t-2 border-black p-2 sm:p-4">
                        <h3 class="text-xs sm:text-sm font-bold mb-2">Personel Pengamanan Penerbangan</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div style="display: grid; grid-template-rows: 1fr 1fr; gap: 8px; align-items: center; text-align: center;">
                                <div class="text-center self-end">
                                    <h4 class="font-bold">{{ $form->officerName }}</h4>
                                    <label class="text-gray-700 font-normal text-xs sm:text-sm">1. Airport Security Officer</label>
                                </div>
                                <div class="text-center self-end">
                                    <h4 class="font-bold">
                                        @if($form->supervisor)
                                            {{ $form->supervisor->name }}
                                        @else
                                            Nama Supervisor tidak tersedia
                                        @endif
                                    </h4>
                                    <label class="text-gray-700 font-normal text-xs sm:text-sm">2. Airport Security Supervisor</label>
                                </div>
                            </div>
                            <div>
                                <div class="flex flex-col items-center">
                                    @if($form->officer_signature)
                                    <img src="{{ $form->officer_signature }}" alt="Tanda tangan Officer"
                                        style="max-width: 100%; height: auto;">
                                    @else
                                    <p class="text-xs sm:text-sm">Tanda tangan Officer tidak tersedia</p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-center mt-2 sm:mt-4">
                                    @if($form->supervisor_signature)
                                    <img src="{{ $form->supervisor_signature }}" alt="Tanda tangan Supervisor"
                                        style="max-width: 100%; height: auto;">
                                    @else
                                    <p class="text-xs sm:text-sm">Tanda tangan Supervisor tidak tersedia</p>
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