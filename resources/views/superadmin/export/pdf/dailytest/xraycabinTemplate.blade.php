<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>X-RAY CABIN Forms</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>

<body>
    @foreach($forms as $form)
    <div class="page-break-after">
        <div class="bg-white p-4 content" style="width: auto;">
            <div id="format" class="mx-auto">
                <div class="flex justify-between items-center border-t-2 border-x-2 border-black p-2">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/airport-security-logo.png'))) }}" alt="Airport Security Logo" class="w-16 h-16 object-contain">
                    <h3 class="text-xl font-bold text-center flex-grow mx-4">
                        CHECK LIST PENGUJIAN HARIAN<br>
                        MESIN X-RAY CABIN MULTI VIEW
                    </h3>
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/injourney-API.png'))) }}" alt="Injourney Logo" class="w-16 h-16 object-contain">
                </div>

                <div class="flex justify-center">
                    <table class="w-full border-collapse border-2 border-black">
                        <tr class="border-b border-black">
                            <th class="w-1/3 text-left text-xs p-1">
                                <label class="text-gray-700 font-bold">Nama Operator Penerbangan:</label>
                            </th>
                            <td class="w-2/3 p-1">{{ $form->operatorName }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="w-1/3 text-left p-1">
                                <label class="text-gray-700 font-bold text-xs">Tanggal & Waktu Pengujian:</label>
                            </th>
                            <td class="w-2/3 p-1">{{ date('d-m-Y H:i', strtotime($form->testDateTime)) }} WIB</td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="w-1/3 text-left p-1">
                                <label class="text-gray-700 font-bold text-xs">Lokasi Penempatan:</label>
                            </th>
                            <td class="w-2/3 p-1">{{ $form->location }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="w-1/3 text-left p-1">
                                <label class="text-gray-700 font-bold text-xs">Merk/Tipe/Nomor Seri:</label>
                            </th>
                            <td class="w-2/3 p-1">{{ $form->deviceInfo }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="w-1/3 text-left p-1">
                                <label class="text-gray-700 font-bold text-xs">Nomor dan Tanggal Sertifikat:</label>
                            </th>
                            <td class="w-2/3 p-1">{{ $form->certificateInfo }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Checkbox Terpenuhi & Tidak Terpenuhi -->
                <div class="mb-2 border-b-2 border-x-2 border-black">
                    <div class="flex flex-col">
                        <label class="inline-flex items-center mt-1 mb-1 ml-4">
                            <input type="checkbox" class="custom-checkbox-alt" {{ $form->terpenuhi ? 'checked' : '' }}
                            disabled>
                            <span class="ml-1 text-xs font-semibold">Terpenuhi</span>
                        </label>
                        <label class="inline-flex items-center ml-4">
                            <input type="checkbox" class="custom-checkbox-alt" {{ $form->tidakTerpenuhi ? 'checked' : ''
                            }}
                            disabled>
                            <span class="ml-1 text-xs font-semibold">Tidak Terpenuhi</span>
                        </label>
                    </div>

                    <!-- Generator Atas/Bawah Section -->
                    <div>
                        <h3 class="text-center font-bold mt-0.5 text-xs">GENERATOR ATAS/BAWAH</h3>
                        <div class="border-2 border-black mx-2 p-0">

                            <!-- Test 2a, Test 2b, Test 3, dll -->
                            <div class="grid grid-cols-3 gap-10 ml-2">
                                <!-- Test 2a -->
                                <div class="p-2 text-center">
                                    <p class="text-xs ml-24">TEST 2a</p>
                                    <div class="relative flex border-2 border-black h-20 w-25 ml-24 mt-1 mr-5">
                                        <!-- Menggeser ke kanan dan bawah -->
                                        <div class="bg-green-500 flex-1 flex items-center justify-center relative">
                                            <div class="absolute right-0 top-0 bottom-0 border-r-2 border-black">
                                            </div>
                                        </div>
                                        <div class="bg-orange-500 flex-1 flex items-center justify-center relative">
                                            <div class="absolute left-0 top-0 bottom-0 border-l-2 border-black"></div>
                                        </div>
                                        <div class="absolute inset-0 flex justify-center items-center">
                                            <input type="checkbox" class="custom-checkbox-alt w-4 h-4" {{
                                                $form->test2aab ? 'checked' : '' }} disabled>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex justify-center items-center ml-36">
                                        <!-- Menggeser ke kanan -->
                                        <p class="mr-1 text-xs">TEST 2b</p>
                                        <input type="checkbox" class="custom-checkbox-alt w-4 h-4" {{ $form->test2bab ?
                                        'checked' : '' }} disabled>
                                    </div>
                                </div>

                                <!-- Test 3 -->
                                <div class="p-2 text-center">
                                    <!-- Mengurangi padding -->
                                    <p class="text-xs mb-2 ml-28">TEST 3</p> <!-- Mengurangi ukuran teks -->
                                    <div class="relative">
                                        <div class="table border-2 border-black mb-2"
                                            style="width: 160%; height: 60px;">
                                            <!-- Mengurangi tinggi -->
                                            <div class="table-row">
                                                <div class="table-cell bg-blue-100 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-1.5" {{
                                                        $form->test3ab_14 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div> <!-- Mengurangi tinggi garis -->
                                                </div>
                                                <div class="table-cell bg-blue-200 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-1.5" {{
                                                        $form->test3ab_16 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-300 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-1.5" {{
                                                        $form->test3ab_18 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-400 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-1.5" {{
                                                        $form->test3ab_20 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-500 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-1.5" {{
                                                        $form->test3ab_22 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-600 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-1.5" {{
                                                        $form->test3ab_24 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-700 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-[13px] left-[12px]"
                                                        {{ $form->test3ab_26 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-800 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-[13px] left-[12px]"
                                                        {{ $form->test3ab_28 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-900 relative" style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-[13px] left-[12px]"
                                                        {{ $form->test3ab_30? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between" style="width: 160%;">
                                            <span class="text-center text-xs" style="width: 10%;">14</span>
                                            <span class="text-center text-xs" style="width: 10%;">16</span>
                                            <span class="text-center text-xs" style="width: 10%;">18</span>
                                            <span class="text-center text-xs" style="width: 10%;">20</span>
                                            <span class="text-center text-xs" style="width: 10%;">22</span>
                                            <span class="text-center text-xs" style="width: 10%;">24</span>
                                            <span class="text-center text-xs" style="width: 10%;">26</span>
                                            <span class="text-center text-xs" style="width: 10%;">28</span>
                                            <span class="text-center text-xs" style="width: 10%;">30</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Test 1a, Test 1b, Test 4, Test 5 -->
                            <div class="flex items-start space-x-2 mt-0 ml-20">
                                <!-- Test 1a -->
                                <div class="flex flex-col items-center">
                                    <div class="flex items-start">
                                        <span class="transform -rotate-90 mt-14 mr-1 text-xs">AWG</span>
                                        <div class="grid grid-rows-4 gap-1.5 mt-6">
                                            <div class="flex items-center space-x-1">
                                                <span class="mr-1 text-xs">36</span>
                                                <input type="checkbox"
                                                    class="custom-checkbox-alt w-[18px] h-[18px] border-black" {{
                                                    $form->test1aab_36 ? 'checked' : '' }} disabled>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <span class="mr-1 text-xs">32</span>
                                                <input type="checkbox"
                                                    class="custom-checkbox-alt w-[18px] h-[18px] border-black" {{
                                                    $form->test1aab_32 ? 'checked' : '' }} disabled>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <span class="mr-1 text-xs">30</span>
                                                <input type="checkbox"
                                                    class="custom-checkbox-alt w-[18px] h-[18px] border-black" {{
                                                    $form->test1aab_30 ? 'checked' : '' }} disabled>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <span class="mr-1 text-xs">24</span>
                                                <input type="checkbox"
                                                    class="custom-checkbox-alt w-[18px] h-[18px] border-black" {{
                                                    $form->test1aab_24 ? 'checked' : '' }} disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-9 ml-6 text-xs">TEST 1a</p>
                                </div>

                                <!-- Test 1b -->
                                <div class="flex flex-col items-center">
                                    <div class="grid grid-cols-3 gap-0 border border-black mr-5">
                                        <!-- Kolom 1 (4.7 mm) -->
                                        <div
                                            class="flex flex-col items-center bg-green-200 p-0.5 border-r border-black">
                                            <span style="font-size: 0.7rem;">4.7 mm</span>
                                            <!-- Mengurangi ukuran teks -->
                                            <div class="grid grid-rows-4 gap-1.5 mt-1">
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bab_36_1 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bab_32_1 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bab_30_1 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bab_24_1 ?
                                                'checked' : '' }} disabled>
                                            </div>
                                            <span class="mt-1" style="font-size: 0.7rem;">3/16"</span>
                                            <!-- Mengurangi ukuran teks -->
                                        </div>

                                        <!-- Kolom 2 (7.9 mm) -->
                                        <div
                                            class="flex flex-col items-center bg-green-300 p-0.5 border-r border-black">
                                            <span style="font-size: 0.7rem;">7.9 mm</span>
                                            <!-- Mengurangi ukuran teks -->
                                            <div class="grid grid-rows-4 gap-1.5 mt-1">
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bab_36_2 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bab_32_2 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bab_30_2 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bab_24_2 ?
                                                'checked' : '' }} disabled>
                                            </div>
                                            <span class="mt-1" style="font-size: 0.7rem;">5/16"</span>
                                            <!-- Mengurangi ukuran teks -->
                                        </div>

                                        <!-- Kolom 3 (11.1 mm) -->
                                        <div class="flex flex-col items-center bg-green-400 p-0.5">
                                            <span style="font-size: 0.7rem;">11.1 mm</span>
                                            <!-- Mengurangi ukuran teks -->
                                            <div class="grid grid-rows-4 gap-1.5 mt-1">
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bab_36_3 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bab_32_3 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bab_30_3 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bab_24_3 ?
                                                'checked' : '' }} disabled>
                                            </div>
                                            <span class="mt-1" style="font-size: 0.7rem;">7/16"</span>
                                            <!-- Mengurangi ukuran teks -->
                                        </div>
                                    </div>
                                    <p class="mt-2.5" style="font-size: 0.7rem;">TEST 1b</p>
                                    <!-- Mengurangi ukuran teks -->
                                </div>

                                <!-- Test 4 -->
                                <div class="flex flex-col items-center w-57">
                                    <div class="bg-sky-300 w-56 h-36 relative px-8 mr-5">
                                        <!-- 1.5 mm gaps horizontal -->
                                        <div class="absolute top-1 left-6 flex">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs mb-1 text-black">1.5 mm gaps</span>
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt border border-black absolute w-4 h-4 z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-[56%]"
                                                        {{ $form->test4ab_h15mm ? 'checked' : '' }} disabled>
                                                    <div class="flex flex-col gap-0.5 -mt-1">
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 1.5 mm gaps vertical -->
                                        <div class="absolute top-4 right-16 flex">
                                            <div class="flex flex-col items-center">
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt border border-black absolute w-4 h-4 z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-[56%]"
                                                        {{ $form->test4ab_v15mm ? 'checked' : '' }} disabled>
                                                    <div class="flex flex-row gap-0.5 -mt-1">
                                                        <div class="h-7 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-7 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-7 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-7 w-0.5 border-1 border-black bg-white"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 2.0 mm gaps horizontal -->
                                        <div class="absolute top-14 left-12 flex">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs mb-1 text-black">2.0 mm gaps</span>
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt border border-black absolute w-4 h-4 z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-[56%]"
                                                        {{ $form->test4ab_h20mm ? 'checked' : '' }} disabled>
                                                    <div class="flex flex-col gap-0.5 -mt-5">
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 2.0 mm gaps vertical -->
                                        <div class="absolute top-14 right-8 flex">
                                            <div class="flex flex-col items-center">
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt border border-black absolute w-4 h-4 z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-[56%]"
                                                        {{ $form->test4ab_v20mm ? 'checked' : '' }} disabled>
                                                    <div class="flex flex-row gap-1 -mt-5">
                                                        <div class="h-8 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-8 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-8 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-8 w-0.5 border-1 border-black bg-white"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 1.0 mm gaps horizontal -->
                                        <div class="absolute bottom-2 left-6 flex">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs mb-1 text-black relative top-0.5">1.0 mm
                                                    gaps</span>
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt border border-black absolute w-4 h-4 z-10 ml-6"
                                                        {{ $form->test4ab_h10mm ? 'checked' : '' }} disabled>
                                                    <div class="flex flex-col gap-0.5">
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 1.0 mm gaps vertical -->
                                        <div class="absolute bottom-2 right-16 flex">
                                            <div class="flex flex-col items-center">
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt border border-black absolute w-4 h-4 z-10 top-3"
                                                        {{ $form->test4ab_v10mm ? 'checked' : '' }} disabled>
                                                    <div class="flex flex-row gap-0.5">
                                                        <div class="h-10 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-10 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-10 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-10 w-0.5 border-1 border-black bg-white"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs">TEST 4</p>
                                </div>

                                <!-- Test 5 -->
                                <div class="w-24 font-sans">
                                    <!-- Kotak 1 -->
                                    <div class="relative inline-block mr-0 flex items-center">
                                        <div
                                            class="w-12 h-12 border-2 border-black bg-[#c8e6c9] flex justify-center items-center">
                                            <input type="checkbox" class="custom-checkbox-alt w-4 h-4" {{
                                                $form->test5ab_05mm ? 'checked' : '' }} disabled>
                                        </div>
                                        <span class="text-[0.7rem] transform -rotate-90">0.05mm</span>
                                    </div>

                                    <!-- Kotak 2 -->
                                    <div class="relative inline-block mr-0 flex items-center">
                                        <div
                                            class="w-12 h-12 border-2 border-black bg-[#a5d6a7] flex justify-center items-center">
                                            <input type="checkbox" class="custom-checkbox-alt w-4 h-4" {{
                                                $form->test5ab_10mm ? 'checked' : '' }} disabled>
                                        </div>
                                        <span class="text-[0.7rem] transform -rotate-90">0.10mm</span>
                                    </div>

                                    <!-- Kotak 3 -->
                                    <div class="relative inline-block mr-0 flex items-center">
                                        <div
                                            class="w-12 h-12 border-2 border-black bg-[#81c784] flex justify-center items-center">
                                            <input type="checkbox" class="custom-checkbox-alt w-4 h-4" {{
                                                $form->test5ab_15mm ? 'checked' : '' }} disabled>
                                        </div>
                                        <span class="text-[0.7rem] transform -rotate-90">0.15mm</span>
                                    </div>

                                    <!-- Teks "Test 5" -->
                                    <div class="text-center mt-1 text-[0.7rem]">TEST 5</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Generator Samping Section (Gunakan pendekatan yang sama) -->
                    <div>
                        <h3 class="text-center font-bold mt-0.5 text-xs">GENERATOR SAMPING</h3>
                        <div class="border-2 border-black mx-2 p-0">

                            <!-- Test 2a, Test 2b, Test 3, dll -->
                            <div class="grid grid-cols-3 gap-10 ml-2">
                                <!-- Test 2a -->
                                <div class="p-2 text-center">
                                    <p class="text-xs ml-24">TEST 2a</p>
                                    <div class="relative flex border-2 border-black h-20 w-25 ml-24 mt-2 mr-5">
                                        <!-- Menggeser ke kanan dan bawah -->
                                        <div class="bg-green-500 flex-1 flex items-center justify-center relative">
                                            <div class="absolute right-0 top-0 bottom-0 border-r-2 border-black">
                                            </div>
                                        </div>
                                        <div class="bg-orange-500 flex-1 flex items-center justify-center relative">
                                            <div class="absolute left-0 top-0 bottom-0 border-l-2 border-black"></div>
                                        </div>
                                        <div class="absolute inset-0 flex justify-center items-center">
                                            <input type="checkbox" class="custom-checkbox-alt w-4 h-4" {{ $form->test2ab
                                            ? 'checked' : '' }} disabled>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex justify-center items-center ml-36">
                                        <!-- Menggeser ke kanan -->
                                        <p class="mr-1 text-xs">TEST 2b</p>
                                        <input type="checkbox" class="custom-checkbox-alt w-4 h-4" {{ $form->test2bb ?
                                        'checked' : '' }} disabled>
                                    </div>
                                </div>

                                <!-- Test 3 -->
                                <div class="p-2 text-center">
                                    <!-- Mengurangi padding -->
                                    <p class="text-xs mb-2 ml-28">TEST 3</p> <!-- Mengurangi ukuran teks -->
                                    <div class="relative">
                                        <div class="table border-2 border-black mb-2"
                                            style="width: 160%; height: 60px;">
                                            <!-- Mengurangi tinggi -->
                                            <div class="table-row">
                                                <div class="table-cell bg-blue-100 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-1.5" {{
                                                        $form->test3b_14 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div> <!-- Mengurangi tinggi garis -->
                                                </div>
                                                <div class="table-cell bg-blue-200 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-1.5" {{
                                                        $form->test3b_16 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-300 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-1.5" {{
                                                        $form->test3b_18 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-400 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-1.5" {{
                                                        $form->test3b_20 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-500 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-1.5" {{
                                                        $form->test3b_22 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-600 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-1.5" {{
                                                        $form->test3b_24 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-700 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-[13px] left-[12px]"
                                                        {{ $form->test3b_26 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-800 border-black border-r relative"
                                                    style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-[13px] left-[12px]"
                                                        {{ $form->test3b_28 ? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                                <div class="table-cell bg-blue-900 relative" style="width: 10%;">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt w-3 h-3 absolute top-[13px] left-[12px]"
                                                        {{ $form->test3b_30? 'checked' : '' }} disabled>
                                                    <div
                                                        class="absolute w-full h-1 border border-black bg-black top-1/2 left-0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between" style="width: 160%;">
                                            <span class="text-center text-xs" style="width: 10%;">14</span>
                                            <span class="text-center text-xs" style="width: 10%;">16</span>
                                            <span class="text-center text-xs" style="width: 10%;">18</span>
                                            <span class="text-center text-xs" style="width: 10%;">20</span>
                                            <span class="text-center text-xs" style="width: 10%;">22</span>
                                            <span class="text-center text-xs" style="width: 10%;">24</span>
                                            <span class="text-center text-xs" style="width: 10%;">26</span>
                                            <span class="text-center text-xs" style="width: 10%;">28</span>
                                            <span class="text-center text-xs" style="width: 10%;">30</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Test 1a, Test 1b, Test 4, Test 5 -->
                            <div class="flex items-start space-x-2 mt-1 ml-20">
                                <!-- Test 1a -->
                                <div class="flex flex-col items-center">
                                    <div class="flex items-start">
                                        <span class="transform -rotate-90 mt-14 mr-1 text-xs">AWG</span>
                                        <div class="grid grid-rows-4 gap-1.5 mt-6">
                                            <div class="flex items-center space-x-1">
                                                <span class="mr-1 text-xs">36</span>
                                                <input type="checkbox"
                                                    class="custom-checkbox-alt w-[18px] h-[18px] border-black" {{
                                                    $form->test1ab_36 ? 'checked' : '' }} disabled>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <span class="mr-1 text-xs">32</span>
                                                <input type="checkbox"
                                                    class="custom-checkbox-alt w-[18px] h-[18px] border-black" {{
                                                    $form->test1ab_32 ? 'checked' : '' }} disabled>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <span class="mr-1 text-xs">30</span>
                                                <input type="checkbox"
                                                    class="custom-checkbox-alt w-[18px] h-[18px] border-black" {{
                                                    $form->test1ab_30 ? 'checked' : '' }} disabled>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <span class="mr-1 text-xs">24</span>
                                                <input type="checkbox"
                                                    class="custom-checkbox-alt w-[18px] h-[18px] border-black" {{
                                                    $form->test1ab_24 ? 'checked' : '' }} disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-9 ml-6 text-xs">TEST 1a</p>
                                </div>

                                <!-- Test 1b -->
                                <div class="flex flex-col items-center">
                                    <div class="grid grid-cols-3 gap-0 border border-black mr-5">
                                        <!-- Kolom 1 (4.7 mm) -->
                                        <div
                                            class="flex flex-col items-center bg-green-200 p-0.5 border-r border-black">
                                            <span style="font-size: 0.7rem;">4.7 mm</span>
                                            <!-- Mengurangi ukuran teks -->
                                            <div class="grid grid-rows-4 gap-1.5 mt-1">
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bb_36_1 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bb_32_1 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bb_30_1 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bb_24_1 ?
                                                'checked' : '' }} disabled>
                                            </div>
                                            <span class="mt-1" style="font-size: 0.7rem;">3/16"</span>
                                            <!-- Mengurangi ukuran teks -->
                                        </div>

                                        <!-- Kolom 2 (7.9 mm) -->
                                        <div
                                            class="flex flex-col items-center bg-green-300 p-0.5 border-r border-black">
                                            <span style="font-size: 0.7rem;">7.9 mm</span>
                                            <!-- Mengurangi ukuran teks -->
                                            <div class="grid grid-rows-4 gap-1.5 mt-1">
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bb_36_2 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bb_32_2 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bb_30_2 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bb_24_2 ?
                                                'checked' : '' }} disabled>
                                            </div>
                                            <span class="mt-1" style="font-size: 0.7rem;">5/16"</span>
                                            <!-- Mengurangi ukuran teks -->
                                        </div>

                                        <!-- Kolom 3 (11.1 mm) -->
                                        <div class="flex flex-col items-center bg-green-400 p-0.5">
                                            <span style="font-size: 0.7rem;">11.1 mm</span>
                                            <!-- Mengurangi ukuran teks -->
                                            <div class="grid grid-rows-4 gap-1.5 mt-1">
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bb_36_3 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bb_32_3 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bb_30_3 ?
                                                'checked' : '' }} disabled>
                                                <input type="checkbox" class="custom-checkbox-alt border border-black"
                                                    style="width: 18px; height: 18px;" {{ $form->test1bb_24_3 ?
                                                'checked' : '' }} disabled>
                                            </div>
                                            <span class="mt-1" style="font-size: 0.7rem;">7/16"</span>
                                            <!-- Mengurangi ukuran teks -->
                                        </div>
                                    </div>
                                    <p class="mt-2.5" style="font-size: 0.7rem;">TEST 1b</p>
                                    <!-- Mengurangi ukuran teks -->
                                </div>

                                <!-- Test 4 -->
                                <div class="flex flex-col items-center w-57">
                                    <div class="bg-sky-300 w-56 h-36 relative px-8 mr-5">
                                        <!-- 1.5 mm gaps horizontal -->
                                        <div class="absolute top-1 left-6 flex">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs mb-1 text-black">1.5 mm gaps</span>
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt border border-black absolute w-4 h-4 z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-[56%]"
                                                        {{ $form->test4b_h15mm ? 'checked' : '' }} disabled>
                                                    <div class="flex flex-col gap-0.5 -mt-1">
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 1.5 mm gaps vertical -->
                                        <div class="absolute top-4 right-16 flex">
                                            <div class="flex flex-col items-center">
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt border border-black absolute w-4 h-4 z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-[56%]"
                                                        {{ $form->test4b_v15mm ? 'checked' : '' }} disabled>
                                                    <div class="flex flex-row gap-0.5 -mt-1">
                                                        <div class="h-7 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-7 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-7 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-7 w-0.5 border-1 border-black bg-white"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 2.0 mm gaps horizontal -->
                                        <div class="absolute top-14 left-12 flex">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs mb-1 text-black">2.0 mm gaps</span>
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt border border-black absolute w-4 h-4 z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-[56%]"
                                                        {{ $form->test4b_h20mm ? 'checked' : '' }} disabled>
                                                    <div class="flex flex-col gap-0.5 -mt-5">
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 2.0 mm gaps vertical -->
                                        <div class="absolute top-14 right-8 flex">
                                            <div class="flex flex-col items-center">
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt border border-black absolute w-4 h-4 z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-[56%]"
                                                        {{ $form->test4b_v20mm ? 'checked' : '' }} disabled>
                                                    <div class="flex flex-row gap-1 -mt-5">
                                                        <div class="h-8 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-8 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-8 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-8 w-0.5 border-1 border-black bg-white"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 1.0 mm gaps horizontal -->
                                        <div class="absolute bottom-2 left-6 flex">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs mb-1 text-black relative top-0.5">1.0 mm
                                                    gaps</span>
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt border border-black absolute w-4 h-4 z-10 ml-6"
                                                        {{ $form->test4b_h10mm ? 'checked' : '' }} disabled>
                                                    <div class="flex flex-col gap-0.5">
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                        <div class="w-16 h-0.5 border-1 border-black bg-white"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 1.0 mm gaps vertical -->
                                        <div class="absolute bottom-2 right-16 flex">
                                            <div class="flex flex-col items-center">
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="custom-checkbox-alt border border-black absolute w-4 h-4 z-10 top-3"
                                                        {{ $form->test4b_v10mm ? 'checked' : '' }} disabled>
                                                    <div class="flex flex-row gap-0.5">
                                                        <div class="h-10 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-10 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-10 w-0.5 border-1 border-black bg-white"></div>
                                                        <div class="h-10 w-0.5 border-1 border-black bg-white"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs">TEST 4</p>
                                </div>

                                <!-- Test 5 -->
                                <div class="w-24 font-sans">
                                    <!-- Kotak 1 -->
                                    <div class="relative inline-block mr-0 flex items-center">
                                        <div
                                            class="w-12 h-12 border-2 border-black bg-[#c8e6c9] flex justify-center items-center">
                                            <input type="checkbox" class="custom-checkbox-alt w-4 h-4" {{
                                                $form->test5b_05mm ? 'checked' : '' }} disabled>
                                        </div>
                                        <span class="text-[0.7rem] transform -rotate-90">0.05mm</span>
                                    </div>

                                    <!-- Kotak 2 -->
                                    <div class="relative inline-block mr-0 flex items-center">
                                        <div
                                            class="w-12 h-12 border-2 border-black bg-[#a5d6a7] flex justify-center items-center">
                                            <input type="checkbox" class="custom-checkbox-alt w-4 h-4" {{
                                                $form->test5b_10mm ? 'checked' : '' }} disabled>
                                        </div>
                                        <span class="text-[0.7rem] transform -rotate-90">0.10mm</span>
                                    </div>

                                    <!-- Kotak 3 -->
                                    <div class="relative inline-block mr-0 flex items-center">
                                        <div
                                            class="w-12 h-12 border-2 border-black bg-[#81c784] flex justify-center items-center">
                                            <input type="checkbox" class="custom-checkbox-alt w-4 h-4" {{
                                                $form->test5b_15mm ? 'checked' : '' }} disabled>
                                        </div>
                                        <span class="text-[0.7rem] transform -rotate-90">0.15mm</span>
                                    </div>

                                    <!-- Teks "Test 5" -->
                                    <div class="text-center mt-1 text-[0.7rem]">TEST 5</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div>
                            <div class="border-t border-black mt-1">
                                <div class="flex items-start m-1">
                                    <label class="text-gray-700 font-bold mr-1 text-xs">Hasil:</label>
                                    <div class="flex flex-col text-xs">
                                        <div class="flex items-center">
                                            <input type="radio" class="custom-radio" {{ $form->result == 'pass' ?
                                            'checked' : '' }} disabled>
                                            <label class="ml-1">PASS</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="radio" class="custom-radio" {{ $form->result == 'fail' ?
                                            'checked' : '' }} disabled>
                                            <label class="ml-1">FAIL</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-1">
                                    <label class="text-gray-700 font-bold text-xs">CATATAN:</label>
                                    <p class="text-xs">{{ $form->notes }}</p>
                                </div>
                            </div>

                            <div class="border-t-2 border-black px-4 py-1">
                                <h3 class="text-sm font-bold mb-0.5">Personel Pengamanan Penerbangan</h3>
                                <div class="grid grid-cols-2 gap-0.5">
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
                                            <label class="text-gray-700 font-normal">2. Airport Security
                                                Supervisor</label>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex flex-col items-center">
                                            @if($form->officer_signature)
                                            <img src="{{ $form->officer_signature }}" alt="Tanda tangan Officer"
                                                class="object-contain"
                                                style="width: 100px; height: 70px;">
                                            @else
                                            <p>Tanda tangan Officer tidak tersedia</p>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-center">
                                            @if($form->supervisor_signature)
                                            <img src="{{ $form->supervisor_signature }}" alt="Tanda tangan Supervisor"
                                                class="object-contain"
                                                style="width: 100px; height: 65px;">
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
        </div>
    </div>
    @endforeach
</body>

</html>
