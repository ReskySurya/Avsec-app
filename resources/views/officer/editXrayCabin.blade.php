@extends('layouts.app')

@section('content')
<div class="container mx-auto px-1 py-8">
    <div class="bg-white shadow-md w-fit rounded pt-6 pb-8 mb-4">
        <h1 class="text-2xl font-bold pl-6">Edit Formulir X-RAY CABIN</h1>

        @if (session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('status') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('officer.xraycabin.update', ['id' => $form->reportID]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white p-4 w-full max-w-full">
                <div id="format" class="mx-auto w-full">
                    <div class="border-t-2 border-x-2 border-black bg-white shadow-md p-4">
                        <div class="flex flex-col sm:flex-row items-center justify-between">
                            <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                                class="w-20 h-20 mb-2 sm:mb-0">
                            <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
                                CHECK LIST PENGUJIAN HARIAN<br>
                                MESIN X-RAY CABIN MULTIVIEW<br>
                            </h1>
                            <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo" class="w-20 h-20 mt-2 sm:mt-0">
                        </div>
                    </div>

                    <div class="border-2 border-black bg-white shadow">
                        <table class="w-full text-xs sm:text-sm">
                            <tbody>
                                <tr class="border-b border-black">
                                    <th class="w-1/3 text-left p-2">Nama Operator Penerbangan:</th>
                                    <td class="w-2/3 p-2">
                                        <input type="text" name="operatorName"
                                            value="Bandar Udara Adisutjipto Yogyakarta"
                                            class="w-full border rounded px-2 py-1">
                                    </td>
                                </tr>
                                <tr class="border-b border-black">
                                    <th class="w-1/3 text-left p-2">Tanggal & Waktu Pengujian:</th>
                                    <td class="w-2/3 p-2">
                                        <input type="datetime-local" name="testDateTime"
                                            value="{{ old('testDateTime', optional($form->testDate)->format('Y-m-d\TH:i')) }}"
                                            class="w-full border rounded px-2 py-1">
                                    </td>
                                </tr>
                                <tr class="border-b border-black">
                                    <th class="w-1/3 text-left p-2">Lokasi Penempatan:</th>
                                    <td class="w-2/3 p-2">
                                    <select id="location" name="location"
                                        class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base  bg-gray-100" disabled>
                                        <option value="">Pilih Lokasi</option>
                                        @if(isset($xrayLocations))
                                        @foreach($xrayLocations as $location)
                                        <option value="{{ $location->id }}"
                                            {{ old('location', $form->equipmentLocation->location_id) == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                    @error('location')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </td>
                                </tr>
                                <tr class="border-b border-black">
                                    <th class="w-1/3 text-left p-2">Merk/Tipe/Nomor Seri:</th>
                                    <td class="w-2/3 p-2">
                                        <input type="text" name="deviceInfo"
                                            value="{{ old('deviceInfo', $form->deviceInfo) }}"
                                            class="w-full border rounded px-2 py-1">
                                    </td>
                                </tr>
                                <tr class="border-b border-black">
                                    <th class="w-1/3 text-left p-2">Nomor dan Tanggal Sertifikat:</th>
                                    <td class="w-2/3 p-2">
                                        <input type="text" name="certificateInfo"
                                            value="{{ old('certificateInfo', $form->certificateInfo) }}"
                                            class="w-full border rounded px-2 py-1">
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="px-1">
                            <div class="p-2">
                                <div class="mb-0">
                                    <label class="inline-flex items-center">
                                        <input type="hidden" name="terpenuhi" value="0">
                                        <input type="checkbox" name="terpenuhi" value="1" {{ old('terpenuhi',
                                            $details->terpenuhi) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm">Terpenuhi</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="hidden" name="tidakterpenuhi" value="0">
                                        <input type="checkbox" name="tidakterpenuhi" value="1" {{ old('tidakterpenuhi',
                                            $details->tidakterpenuhi) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm">Tidak Terpenuhi</span>
                                    </label>
                                </div>
                            </div>

                            <h2 class="font-bold text-sm mb-1 text-center">GENERATOR ATAS/BAWAH</h2>
                            <div class="border-2 border-black p-2 mb-2">
                                <div class="grid grid-cols-[30%_70%] gap-2 mb-4">
                                    <div class="p-2">
                                        <h3 class="text-xs font-bold mb-2 text-center">TEST 2a</h3>
                                        <div class="grid grid-cols-2 gap-0">
                                            <input type="hidden" name="test2aab" value="0">
                                            <input type="checkbox" name="test2aab" id="test2aab" {{ old('test2aab', $details->test2aab)
                                                    ? 'checked' : '' }} value="1"
                                                class="form-checkbox absolute place-self-center">
                                            <div class="bg-green-500 h-16 flex items-center justify-center">
                                            </div>
                                            <div class="bg-orange-500 h-16 flex items-center justify-center">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-[70%_30%] relative">
                                            <h3 class="text-xs font-bold text-end">TEST 2b</h3>
                                            <input type="hidden" name="test2bab" value="0">
                                            <input type="checkbox" name="test2bab" id="test2bab" {{ old('test2bab', $details->test2bab)
                                                    ? 'checked' : '' }} value="1"
                                                class="absolute place-self-end form-checkbox">
                                        </div>
                                    </div>

                                    <div class="p-2">
                                        <h3 class="text-xs font-bold mb-2 text-center">TEST 3</h3>
                                        <div class="grid grid-cols-9 gap-0 relative">
                                            <div class="bg-cyan-100 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3ab_14" value="0">
                                                <input type="checkbox" name="test3ab_14" id="test3ab_14"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3ab_14', $details->test3ab_14)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-cyan-200 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3ab_16" value="0">
                                                <input type="checkbox" name="test3ab_16" id="test3ab_16"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3ab_16', $details->test3ab_16)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-sky-400 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3ab_18" value="0">
                                                <input type="checkbox" name="test3ab_18" id="test3ab_18"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3ab_18', $details->test3ab_18)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-blue-500 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3ab_20" value="0">
                                                <input type="checkbox" name="test3ab_20" id="test3ab_20"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3ab_20', $details->test3ab_20)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-blue-500 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3ab_22" value="0">
                                                <input type="checkbox" name="test3ab_22" id="test3ab_22"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3ab_22', $details->test3ab_22)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-blue-700 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3ab_24" value="0">
                                                <input type="checkbox" name="test3ab_24" id="test3ab_24"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3ab_24', $details->test3ab_24)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-blue-700 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3ab_26" value="0">
                                                <input type="checkbox" name="test3ab_26" id="test3ab_26"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3ab_26', $details->test3ab_26)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-blue-950 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3ab_28" value="0">
                                                <input type="checkbox" name="test3ab_28" id="test3ab_28"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3ab_28', $details->test3ab_28)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-blue-950 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3ab_30" value="0">
                                                <input type="checkbox" name="test3ab_30" id="test3ab_30"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3ab_30', $details->test3ab_30)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-9 gap-0">
                                            <p class="text-xs -rotate-90">14</p>
                                            <p class="text-xs -rotate-90">16</p>
                                            <p class="text-xs -rotate-90">18</p>
                                            <p class="text-xs -rotate-90">20</p>
                                            <p class="text-xs -rotate-90">22</p>
                                            <p class="text-xs -rotate-90">24</p>
                                            <p class="text-xs -rotate-90">26</p>
                                            <p class="text-xs -rotate-90">28</p>
                                            <p class="text-xs -rotate-90">30</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-[35%_50%_15%] gap-1 mb-2 pr-1">
                                    <div class="">
                                        <div class="grid grid-cols-6 gap-0 h-32">
                                            <div class="grid grid-rows bg-white items-center justify-center">
                                                <p class="text-xs -rotate-90">AWG</p>
                                            </div>
                                            <div class="grid grid-rows-4 bg-white items-center justify-center">
                                                <p class="text-xs -rotate-90">36</p>
                                                <p class="text-xs -rotate-90">32</p>
                                                <p class="text-xs -rotate-90">30</p>
                                                <p class="text-xs -rotate-90">24</p>
                                            </div>
                                            <div class="grid grid-rows-4 bg-white items-center justify-center">
                                                <input type="hidden" name="test1aab_36" value="0">
                                                <input type="checkbox" id="test1aab_36" name="test1aab_36"
                                                    class="form-checkbox" {{ old('test1aab_36', $details->test1aab_36)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1aab_32" value="0">
                                                <input type="checkbox" id="test1aab_32" name="test1aab_32"
                                                    class="form-checkbox" {{ old('test1aab_32', $details->test1aab_32)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1aab_30" value="0">
                                                <input type="checkbox" id="test1aab_30" name="test1aab_30"
                                                    class="form-checkbox" {{ old('test1aab_30', $details->test1aab_30)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1aab_24" value="0">
                                                <input type="checkbox" id="test1aab_24" name="test1aab_24"
                                                    class="form-checkbox" {{ old('test1aab_24', $details->test1aab_24)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div
                                                class="grid grid-rows-4 bg-[#C5E0B3] border border-black items-center justify-center">
                                                <input type="hidden" name="test1bab_36_1" value="0">
                                                <input type="checkbox" id="test1bab_36_1" name="test1bab_36_1"
                                                    class="form-checkbox" {{ old('test1bab_36_1', $details->test1bab_36_1)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bab_32_1" value="0">
                                                <input type="checkbox" id="test1bab_32_1" name="test1bab_32_1"
                                                    class="form-checkbox" {{ old('test1bab_32_1', $details->test1bab_32_1)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bab_30_1" value="0">
                                                <input type="checkbox" id="test1bab_30_1" name="test1bab_30_1"
                                                    class="form-checkbox" {{ old('test1bab_30_1', $details->test1bab_30_1)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bab_24_1" value="0">
                                                <input type="checkbox" id="test1bab_24_1" name="test1bab_24_1"
                                                    class="form-checkbox" {{ old('test1bab_24_1', $details->test1bab_24_1)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div
                                                class="grid grid-rows-4 bg-[#92D050] border-y border-black items-center justify-center">
                                                <input type="hidden" name="test1bab_36_2" value="0">
                                                <input type="checkbox" id="test1bab_36_2" name="test1bab_36_2"
                                                    class="form-checkbox" {{ old('test1bab_36_2', $details->test1bab_36_2)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bab_32_2" value="0">
                                                <input type="checkbox" id="test1bab_32_2" name="test1bab_32_2"
                                                    class="form-checkbox" {{ old('test1bab_32_2', $details->test1bab_32_2)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bab_30_2" value="0">
                                                <input type="checkbox" id="test1bab_30_2" name="test1bab_30_2"
                                                    class="form-checkbox" {{ old('test1bab_30_2', $details->test1bab_30_2)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bab_24_2" value="0">
                                                <input type="checkbox" id="test1bab_24_2" name="test1bab_24_2"
                                                    class="form-checkbox" {{ old('test1bab_24_2', $details->test1bab_24_2)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div
                                                class="grid grid-rows-4 bg-green-500 border border-black items-center justify-center">
                                                <input type="hidden" name="test1bab_36_3" value="0">
                                                <input type="checkbox" id="test1bab_36_3" name="test1bab_36_3"
                                                    class="form-checkbox" {{ old('test1bab_36_3', $details->test1bab_36_3)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bab_32_3" value="0">
                                                <input type="checkbox" id="test1bab_32_3" name="test1bab_32_3"
                                                    class="form-checkbox" {{ old('test1bab_32_3', $details->test1bab_32_3)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bab_30_3" value="0">
                                                <input type="checkbox" id="test1bab_30_3" name="test1bab_30_3"
                                                    class="form-checkbox" {{ old('test1bab_30_3', $details->test1bab_30_3)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bab_24_3" value="0">
                                                <input type="checkbox" id="test1bab_24_3" name="test1bab_24_3"
                                                    class="form-checkbox" {{ old('test1bab_24_3', $details->test1bab_24_3)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-[50%_50%] mt-3">
                                            <h3 class="text-xs font-bold text-center">TEST 1a</h3>
                                            <h3 class="text-xs font-bold text-end">TEST 1b</h3>
                                        </div>
                                    </div>

                                    <div class="">
                                        <div class="grid grid-rows-3 gap-0">
                                            <div
                                                class="grid grid-cols-[40%_60%] bg-sky-400 items-center justify-center relative">
                                                <div class="grid grid-rows-4 text-xs h-6 pl-1">
                                                    <p class="text-[8px] font-semibold absolute -top-1">1.5 mm gaps</p>
                                                    <input type="hidden" name="test4ab_h15mm" value="0">
                                                    <input type="checkbox" name="test4ab_h15mm" id="test4ab_h15mm"
                                                        class="form-checkbox absolute place-self-center h-6 w-6"
                                                        {{ old('test4ab_h15mm', $details->test4ab_h15mm)
                                                    ? 'checked' : '' }} value="1">
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                </div>
                                                <div class="grid grid-cols-4 gap-0 text-xs h-7 px-8">
                                                    <input type="hidden" name="test4ab_v15mm" value="0">
                                                    <input type="checkbox" name="test4ab_v15mm" id="test4ab_v15mm"
                                                        class="form-checkbox absolute place-self-center h-5 w-5"
                                                        {{ old('test4ab_v15mm', $details->test4ab_v15mm)
                                                    ? 'checked' : '' }} value="1">
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                </div>
                                            </div>
                                            <div
                                                class="grid grid-cols-[65%_35%] bg-sky-400 items-center justify-end relative">
                                                <div class="grid grid-rows-4 text-xs h-6 px-1 pl-10">
                                                    <p class="text-[8px] font-semibold absolute -top-1">2.0 mm gaps</p>
                                                    <input type="hidden" name="test4ab_h20mm" value="0">
                                                    <input type="checkbox" name="test4ab_h20mm" id="test4ab_h20mm"
                                                        class="form-checkbox absolute place-self-center h-6 w-6"
                                                        {{ old('test4ab_h20mm', $details->test4ab_h20mm)
                                                    ? 'checked' : '' }} value="1">
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                </div>
                                                <div class="grid grid-cols-4 gap-0 text-xs h-8 px-2">
                                                    <input type="hidden" name="test4ab_v20mm" value="0">
                                                    <input type="checkbox" name="test4ab_v20mm" id="test4ab_v20mm"
                                                        class="form-checkbox absolute place-self-center h-4 w-4"
                                                        {{ old('test4ab_v20mm', $details->test4ab_v20mm)
                                                    ? 'checked' : '' }} value="1">
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                </div>
                                            </div>
                                            <div
                                                class="grid grid-cols-[40%_60%] bg-sky-400 items-center justify-center relative">
                                                <div class="grid grid-rows-4 text-xs h-6 pl-1">
                                                    <p class="text-[8px] font-semibold absolute -top-1">1.0 mm gaps</p>
                                                    <input type="hidden" name="test4ab_h10mm" value="0">
                                                    <input type="checkbox" name="test4ab_h10mm" id="test4ab_h10mm"
                                                        class="form-checkbox absolute place-self-center h-6 w-6"
                                                        {{ old('test4ab_h10mm', $details->test4ab_h10mm)
                                                    ? 'checked' : '' }} value="1">
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                </div>
                                                <div class="grid grid-cols-4 gap-0 text-xs h-11 px-9 py-1">
                                                    <input type="hidden" name="test4ab_v10mm" value="0">
                                                    <input type="checkbox" name="test4ab_v10mm" id="test4ab_v10mm"
                                                        class="form-checkbox absolute place-self-center h-5 w-5"
                                                        {{ old('test4ab_v10mm', $details->test4ab_v10mm)
                                                    ? 'checked' : '' }} value="1">
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <h3 class="text-xs font-bold mt-2 text-center">TEST 4</h3>
                                    </div>

                                    <div class="">
                                        <div class="grid grid-cols-[80%_20%] gap-0">
                                            <div class="grid grid-rows-3">
                                                <div class="bg-[#C5E0B3] h-10 flex items-center justify-center">
                                                    <input type="hidden" name="test5ab_05mm" value="0">
                                                    <input type="checkbox" id="test5ab_05mm" name="test5ab_05mm"
                                                        class="form-checkbox" {{ old('test5ab_05mm', $details->test5ab_05mm)
                                                    ? 'checked' : '' }} value="1">
                                                </div>
                                                <div class="bg-[#A8D08D] h-10 flex items-center justify-center">
                                                    <input type="hidden" name="test5ab_10mm" value="0">
                                                    <input type="checkbox" id="test5ab_10mm" name="test5ab_10mm"
                                                        class="form-checkbox" {{ old('test5ab_10mm', $details->test5ab_10mm)
                                                    ? 'checked' : '' }} value="1">
                                                </div>
                                                <div class="bg-[#548135] h-10 flex items-center justify-center">
                                                    <input type="hidden" name="test5ab_15mm" value="0">
                                                    <input type="checkbox" id="test5ab_15mm" name="test5ab_15mm"
                                                        class="form-checkbox" {{ old('test5ab_15mm', $details->test5ab_15mm)
                                                    ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>
                                            <div class="grid grid-rows-3 pl-1">
                                                <p class="text-[8px] -rotate-90">0.05mm</p>
                                                <p class="text-[8px] -rotate-90">0.10mm</p>
                                                <p class="text-[8px] -rotate-90">0.15mm</p>
                                            </div>
                                        </div>
                                        <h3 class="text-xs font-bold mt-5 text-center">TEST 5</h3>
                                    </div>
                                </div>
                            </div>

                            <h2 class="font-bold text-sm mb-1 text-center">GENERATOR BAWAH</h2>
                            <div class="border-2 border-black p-2 mb-2">
                                <div class="grid grid-cols-[30%_70%] gap-2 mb-4">
                                    <div class="p-2">
                                        <h3 class="text-xs font-bold mb-2 text-center">TEST 2a</h3>
                                        <div class="grid grid-cols-2 gap-0">
                                            <input type="hidden" name="test2ab" value="0">
                                            <input type="checkbox" name="test2ab" id="test2ab" {{ old('test2ab', $details->test2ab)
                                                    ? 'checked' : '' }} value="1"
                                                class="form-checkbox absolute place-self-center">
                                            <div class="bg-green-500 h-16 flex items-center justify-center">
                                            </div>
                                            <div class="bg-orange-500 h-16 flex items-center justify-center">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-[70%_30%] relative">
                                            <h3 class="text-xs font-bold text-end">TEST 2b</h3>
                                            <input type="hidden" name="test2bb" value="0">
                                            <input type="checkbox" name="test2bb" id="test2bb" {{ old('test2bb', $details->test2bb)
                                                    ? 'checked' : '' }} value="1"
                                                class="absolute place-self-end form-checkbox">
                                        </div>
                                    </div>

                                    <div class="p-2">
                                        <h3 class="text-xs font-bold mb-2 text-center">TEST 3</h3>
                                        <div class="grid grid-cols-9 gap-0 relative">
                                            <div class="bg-cyan-100 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3b_14" value="0">
                                                <input type="checkbox" name="test3b_14" id="test3b_14"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3b_14', $details->test3b_14)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-cyan-200 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3b_16" value="0">
                                                <input type="checkbox" name="test3b_16" id="test3b_16"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3b_16', $details->test3b_16)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-sky-400 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3b_18" value="0">
                                                <input type="checkbox" name="test3b_18" id="test3b_18"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3b_18', $details->test3b_18)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-blue-500 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3b_20" value="0">
                                                <input type="checkbox" name="test3b_20" id="test3b_20"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3b_20', $details->test3b_20)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-blue-500 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3b_22" value="0">
                                                <input type="checkbox" name="test3b_22" id="test3b_22"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3b_22', $details->test3b_22)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-blue-700 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3b_24" value="0">
                                                <input type="checkbox" name="test3b_24" id="test3b_24"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3b_24', $details->test3b_24)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-blue-700 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3b_26" value="0">
                                                <input type="checkbox" name="test3b_26" id="test3b_26"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3b_26', $details->test3b_26)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-blue-950 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3b_28" value="0">
                                                <input type="checkbox" name="test3b_28" id="test3b_28"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3b_28', $details->test3b_28)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div class="bg-blue-950 h-16 flex items-center justify-center">
                                                <input type="hidden" name="test3b_30" value="0">
                                                <input type="checkbox" name="test3b_30" id="test3b_30"
                                                    class="form-checkbox justify-center absolute top-4" {{ old('test3b_30', $details->test3b_30)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-9 gap-0">
                                            <p class="text-xs -rotate-90">14</p>
                                            <p class="text-xs -rotate-90">16</p>
                                            <p class="text-xs -rotate-90">18</p>
                                            <p class="text-xs -rotate-90">20</p>
                                            <p class="text-xs -rotate-90">22</p>
                                            <p class="text-xs -rotate-90">24</p>
                                            <p class="text-xs -rotate-90">26</p>
                                            <p class="text-xs -rotate-90">28</p>
                                            <p class="text-xs -rotate-90">30</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-[35%_50%_15%] gap-1 mb-2 pr-1">
                                    <div class="">
                                        <div class="grid grid-cols-6 gap-0 h-32">
                                            <div class="grid grid-rows bg-white items-center justify-center">
                                                <p class="text-xs -rotate-90">AWG</p>
                                            </div>
                                            <div class="grid grid-rows-4 bg-white items-center justify-center">
                                                <p class="text-xs -rotate-90">36</p>
                                                <p class="text-xs -rotate-90">32</p>
                                                <p class="text-xs -rotate-90">30</p>
                                                <p class="text-xs -rotate-90">24</p>
                                            </div>
                                            <div class="grid grid-rows-4 bg-white items-center justify-center">
                                                <input type="hidden" name="test1ab_36" value="0">
                                                <input type="checkbox" id="test1ab_36" name="test1ab_36"
                                                    class="form-checkbox" {{ old('test1ab_36', $details->test1ab_36)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1ab_32" value="0">
                                                <input type="checkbox" id="test1ab_32" name="test1ab_32"
                                                    class="form-checkbox" {{ old('test1ab_32', $details->test1ab_32)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1ab_30" value="0">
                                                <input type="checkbox" id="test1ab_30" name="test1ab_30"
                                                    class="form-checkbox" {{ old('test1ab_30', $details->test1ab_30)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1ab_24" value="0">
                                                <input type="checkbox" id="test1ab_24" name="test1ab_24"
                                                    class="form-checkbox" {{ old('test1ab_24', $details->test1ab_24)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div
                                                class="grid grid-rows-4 bg-[#C5E0B3] border border-black items-center justify-center">
                                                <input type="hidden" name="test1bb_36_1" value="0">
                                                <input type="checkbox" id="test1bb_36_1" name="test1bb_36_1"
                                                    class="form-checkbox" {{ old('test1bb_36_1', $details->test1bb_36_1)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bb_32_1" value="0">
                                                <input type="checkbox" id="test1bb_32_1" name="test1bb_32_1"
                                                    class="form-checkbox" {{ old('test1bb_32_1', $details->test1bb_32_1)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bb_30_1" value="0">
                                                <input type="checkbox" id="test1bb_30_1" name="test1bb_30_1"
                                                    class="form-checkbox" {{ old('test1bb_30_1', $details->test1bb_30_1)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bb_24_1" value="0">
                                                <input type="checkbox" id="test1bb_24_1" name="test1bb_24_1"
                                                    class="form-checkbox" {{ old('test1bb_24_1', $details->test1bb_24_1)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div
                                                class="grid grid-rows-4 bg-[#92D050] border-y border-black items-center justify-center">
                                                <input type="hidden" name="test1bb_36_2" value="0">
                                                <input type="checkbox" id="test1bb_36_2" name="test1bb_36_2"
                                                    class="form-checkbox" {{ old('test1bb_36_2', $details->test1bb_36_2)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bb_32_2" value="0">
                                                <input type="checkbox" id="test1bb_32_2" name="test1bb_32_2"
                                                    class="form-checkbox" {{ old('test1bb_32_2', $details->test1bb_32_2)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bb_30_2" value="0">
                                                <input type="checkbox" id="test1bb_30_2" name="test1bb_30_2"
                                                    class="form-checkbox" {{ old('test1bb_30_2', $details->test1bb_30_2)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bb_24_2" value="0">
                                                <input type="checkbox" id="test1bb_24_2" name="test1bb_24_2"
                                                    class="form-checkbox" {{ old('test1bb_24_2', $details->test1bb_24_2)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                            <div
                                                class="grid grid-rows-4 bg-green-500 border border-black items-center justify-center">
                                                <input type="hidden" name="test1bb_36_3" value="0">
                                                <input type="checkbox" id="test1bb_36_3" name="test1bb_36_3"
                                                    class="form-checkbox" {{ old('test1bb_36_3', $details->test1bb_36_3)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bb_32_3" value="0">
                                                <input type="checkbox" id="test1bb_32_3" name="test1bb_32_3"
                                                    class="form-checkbox" {{ old('test1bb_32_3', $details->test1bb_32_3)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bb_30_3" value="0">
                                                <input type="checkbox" id="test1bb_30_3" name="test1bb_30_3"
                                                    class="form-checkbox" {{ old('test1bb_30_3', $details->test1bb_30_3)
                                                    ? 'checked' : '' }} value="1">

                                                <input type="hidden" name="test1bb_24_3" value="0">
                                                <input type="checkbox" id="test1bb_24_3" name="test1bb_24_3"
                                                    class="form-checkbox" {{ old('test1bb_24_3', $details->test1bb_24_3)
                                                    ? 'checked' : '' }} value="1">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-[50%_50%] mt-3">
                                            <h3 class="text-xs font-bold text-center">TEST 1a</h3>
                                            <h3 class="text-xs font-bold text-end">TEST 1b</h3>
                                        </div>
                                    </div>

                                    <div class="">
                                        <div class="grid grid-rows-3 gap-0">
                                            <div
                                                class="grid grid-cols-[40%_60%] bg-sky-400 items-center justify-center relative">
                                                <div class="grid grid-rows-4 text-xs h-6 pl-1">
                                                    <p class="text-[8px] font-semibold absolute -top-1">1.5 mm gaps</p>
                                                    <input type="hidden" name="test4b_h15mm" value="0">
                                                    <input type="checkbox" name="test4b_h15mm" id="test4b_h15mm"
                                                        class="form-checkbox absolute place-self-center h-6 w-6"
                                                        {{ old('test4b_h15mm', $details->test4b_h15mm)
                                                    ? 'checked' : '' }} value="1">
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                </div>
                                                <div class="grid grid-cols-4 gap-0 text-xs h-7 px-8">
                                                    <input type="hidden" name="test4b_v15mm" value="0">
                                                    <input type="checkbox" name="test4b_v15mm" id="test4b_v15mm"
                                                        class="form-checkbox absolute place-self-center h-5 w-5"
                                                        {{ old('test4b_v15mm', $details->test4b_v15mm)
                                                    ? 'checked' : '' }} value="1">
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                </div>
                                            </div>
                                            <div
                                                class="grid grid-cols-[65%_35%] bg-sky-400 items-center justify-end relative">
                                                <div class="grid grid-rows-4 text-xs h-6 px-1 pl-10">
                                                    <p class="text-[8px] font-semibold absolute -top-1">2.0 mm gaps</p>
                                                    <input type="hidden" name="test4b_h20mm" value="0">
                                                    <input type="checkbox" name="test4b_h20mm" id="test4b_h20mm"
                                                        class="form-checkbox absolute place-self-center h-6 w-6"
                                                        {{ old('test4b_h20mm', $details->test4b_h20mm)
                                                    ? 'checked' : '' }} value="1">
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                </div>
                                                <div class="grid grid-cols-4 gap-0 text-xs h-8 px-2">
                                                    <input type="hidden" name="test4b_v20mm" value="0">
                                                    <input type="checkbox" name="test4b_v20mm" id="test4b_v20mm"
                                                        class="form-checkbox absolute place-self-center h-4 w-4"
                                                        {{ old('test4b_v20mm', $details->test4b_v20mm)
                                                    ? 'checked' : '' }} value="1">
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                </div>
                                            </div>
                                            <div
                                                class="grid grid-cols-[40%_60%] bg-sky-400 items-center justify-center relative">
                                                <div class="grid grid-rows-4 text-xs h-6 pl-1">
                                                    <p class="text-[8px] font-semibold absolute -top-1">1.0 mm gaps</p>
                                                    <input type="hidden" name="test4b_h10mm" value="0">
                                                    <input type="checkbox" name="test4b_h10mm" id="test4b_h10mm"
                                                        class="form-checkbox absolute place-self-center h-6 w-6"
                                                        {{ old('test4b_h10mm', $details->test4b_h10mm)
                                                    ? 'checked' : '' }} value="1">
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                    <div class="border border-black bg-white h-1"></div>
                                                </div>
                                                <div class="grid grid-cols-4 gap-0 text-xs h-11 px-9 py-1">
                                                    <input type="hidden" name="test4b_v10mm" value="0">
                                                    <input type="checkbox" name="test4b_v10mm" id="test4b_v10mm"
                                                        class="form-checkbox absolute place-self-center h-5 w-5"
                                                        {{ old('test4b_v10mm', $details->test4b_v10mm)
                                                    ? 'checked' : '' }} value="1">
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                    <div class="border border-black bg-white w-1"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <h3 class="text-xs font-bold mt-2 text-center">TEST 4</h3>
                                    </div>

                                    <div class="">
                                        <div class="grid grid-cols-[80%_20%] gap-0">
                                            <div class="grid grid-rows-3">
                                                <div class="bg-[#C5E0B3] h-10 flex items-center justify-center">
                                                    <input type="hidden" name="test5b_05mm" value="0">
                                                    <input type="checkbox" id="test5b_05mm" name="test5b_05mm"
                                                        class="form-checkbox" {{ old('test5b_05mm', $details->test5b_05mm)
                                                    ? 'checked' : '' }} value="1">
                                                </div>
                                                <div class="bg-[#A8D08D] h-10 flex items-center justify-center">
                                                    <input type="hidden" name="test5b_10mm" value="0">
                                                    <input type="checkbox" id="test5b_10mm" name="test5b_10mm"
                                                        class="form-checkbox" {{ old('test5b_10mm', $details->test5b_10mm)
                                                    ? 'checked' : '' }} value="1">
                                                </div>
                                                <div class="bg-[#548135] h-10 flex items-center justify-center">
                                                    <input type="hidden" name="test5b_15mm" value="0">
                                                    <input type="checkbox" id="test5b_15mm" name="test5b_15mm"
                                                        class="form-checkbox" {{ old('test5b_15mm', $details->test5b_15mm)
                                                    ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>
                                            <div class="grid grid-rows-3 pl-1">
                                                <p class="text-[8px] -rotate-90">0.05mm</p>
                                                <p class="text-[8px] -rotate-90">0.10mm</p>
                                                <p class="text-[8px] -rotate-90">0.15mm</p>
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
                                    <input type="radio" id="resultPass" name="result" value="pass"
                                        {{ old('result', $form->result) == 'pass' ? 'checked' : '' }}>
                                    <label class="text-sm ml-2">PASS</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="resultFail" name="result" value="fail"
                                        {{ old('result', $form->result) == 'fail' ? 'checked' : '' }}>
                                    <label class="text-sm ml-2">FAIL</label>
                                </div>
                                <input type="hidden" id="result" name="result" value="{{ old('result', $form->result) }}">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">CATATAN:</label>
                            <textarea name="notes" class="w-full border rounded px-2 py-1"
                                rows="3">{{ old('notes', $form->note) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Simpan Perubahan
                </button>
                <a href="{{ route('dashboard.officer') }}" class="text-gray-600 hover:text-gray-800">
                    Kembali ke Dashboard
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Fungsi untuk mengecek status checkbox dan mengupdate radio button
    function updateRadioResult() {
            // Ambil semua checkbox berdasarkan ID yang ada
            const test2Checkboxes = [
                document.getElementById('test2aab'),
                document.getElementById('test2bab'),
                document.getElementById('test2ab'),
                document.getElementById('test2bb')
            ];
            const test3Checkboxes = [
                document.getElementById('test3ab_14'),
                document.getElementById('test3ab_16'),
                document.getElementById('test3ab_18'),
                document.getElementById('test3ab_20'),
                document.getElementById('test3ab_22'),
                document.getElementById('test3ab_24'),
                document.getElementById('test3b_14'),
                document.getElementById('test3b_16'),
                document.getElementById('test3b_18'),
                document.getElementById('test3b_20'),
                document.getElementById('test3b_22'),
                document.getElementById('test3b_24')
            ];
            const test1Checkboxes = [
                document.getElementById('test1aab_32'),
                document.getElementById('test1aab_30'),
                document.getElementById('test1aab_24'),
                document.getElementById('test1bab_30_1'),
                document.getElementById('test1bab_24_1'),
                document.getElementById('test1bab_30_2'),
                document.getElementById('test1bab_24_2'),
                document.getElementById('test1bab_24_3'),
                document.getElementById('test1ab_32'),
                document.getElementById('test1ab_30'),
                document.getElementById('test1ab_24'),
                document.getElementById('test1bb_30_1'),
                document.getElementById('test1bb_24_1'),
                document.getElementById('test1bb_30_2'),
                document.getElementById('test1bb_24_2'),
                document.getElementById('test1bb_24_3')
            ];
            const test4Checkboxes = [
                document.getElementById('test4ab_h15mm'),
                document.getElementById('test4ab_v15mm'),
                document.getElementById('test4ab_h20mm'),
                document.getElementById('test4ab_v20mm'),
                document.getElementById('test4b_h15mm'),
                document.getElementById('test4b_v15mm'),
                document.getElementById('test4b_h20mm'),
                document.getElementById('test4b_v20mm')
            ];

            const test5Checkboxes = [
                document.getElementById('test5ab_10mm'),
                document.getElementById('test5b_10mm')
            ];

            // Update radio button dan hidden input
            const resultPass = document.getElementById('resultPass');
            const resultFail = document.getElementById('resultFail');
            const resultHidden = document.getElementById('result');

            if (resultPass && resultFail && resultHidden) {
            // Cek apakah semua checkbox tercentang
            const allChecked = [...test1Checkboxes, ...test2Checkboxes, ...test3Checkboxes, ...test4Checkboxes, ...test5Checkboxes]
                .every(checkbox => checkbox && checkbox.checked);

            if (allChecked) {
                resultPass.checked = true;
                resultHidden.value = 'pass';
            } else {
                resultFail.checked = true;
                resultHidden.value = 'fail';
            }
        }
        }

        // Event listener setelah DOM sepenuhnya dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Tambahkan event listener untuk semua checkbox
            const allCheckboxIds = [
                'test2aab',
                'test2bab',
                'test2ab',
                'test2bb',
                'test3ab_14',
                'test3ab_16',
                'test3ab_18',
                'test3ab_20',
                'test3ab_22',
                'test3ab_24',
                'test3b_14',
                'test3b_16',
                'test3b_18',
                'test3b_20',
                'test3b_22',
                'test3b_24',
                'test1aab_32',
                'test1aab_30',
                'test1aab_24',
                'test1bab_30_1',
                'test1bab_24_1',
                'test1bab_30_2',
                'test1bab_24_2',
                'test1bab_24_3',
                'test1ab_32',
                'test1ab_30',
                'test1ab_24',
                'test1bb_30_1',
                'test1bb_24_1',
                'test1bb_30_2',
                'test1bb_24_2',
                'test1bb_24_3',
                'test4ab_h15mm',
                'test4ab_v15mm',
                'test4ab_h20mm',
                'test4ab_v20mm',
                'test4b_h15mm',
                'test4b_v15mm',
                'test4b_h20mm',
                'test4b_v20mm',
                'test5ab_10mm',
                'test5b_10mm'
            ];

            allCheckboxIds.forEach(id => {
                document.getElementById(id).addEventListener('change', updateRadioResult);
            });

            // Nonaktifkan radio button agar tidak bisa diklik manual
            document.getElementById('resultPass').addEventListener('click', (e) => e.preventDefault());
            document.getElementById('resultFail').addEventListener('click', (e) => e.preventDefault());

            // Inisialisasi status awal
            updateRadioResult();
        });
</script>
@endpush
@endsection
