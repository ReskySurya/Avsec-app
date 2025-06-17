@push('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

<div class="bg-white p-4 w-full max-w-full">
    <div id="format" class="mx-auto w-full">
        <div class="border-t-2 border-x-2 border-black bg-white shadow-md p-4">
            <div class="flex flex-col sm:flex-row items-center justify-between">
                <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo" class="w-20 h-20 mb-2 sm:mb-0">
                <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
                    CHECK LIST PENGUJIAN HARIAN<br>
                    MESIN X-RAY CABIN MULTIVIEW<br>
                </h1>
                <img src="{{ asset('images/injourney-logo.png') }}" alt="Injourney Logo" class="w-20 h-20 mt-2 sm:mt-0">
            </div>
        </div>

        <form id="xrayForm" method="POST" enctype="multipart/form-data" onsubmit="onFormSubmit(event)" class="mt-0">
            @csrf
            <div class="border-2 border-black bg-white shadow">
                <table class="w-full text-xs sm:text-sm">
                    <tbody>
                        <tr class="border-b border-black">
                            <th class="w-1/3 text-left p-1 sm:p-2">
                                <label for="operatorName" class="text-gray-700 font-bold">Nama Operator
                                    Penerbangan:</label>
                            </th>
                            <td class="w-2/3 p-2">
                                <input type="text" id="operatorName" name="operatorName"
                                    class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base"
                                    value="Bandar Udara Adisutjipto Yogyakarta" readonly>
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="w-1/3 text-left p-1 sm:p-2">
                                <label for="testDateTime" class="text-gray-700 font-bold">Tanggal & Waktu
                                    Pengujian:</label>
                            </th>
                            <td class="w-2/3 p-2">
                                <div class="flex items-center">
                                    <input type="datetime-local" id="testDateTime" name="testDateTime"
                                        class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base"
                                        readonly>
                                    <span class="ml-2 text-xs sm:text-base">WIB</span>
                                </div>
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="w-1/3 text-left p-1 sm:p-2">
                                <label for="location" class="text-gray-700 font-bold">Lokasi Penempatan:</label>
                            </th>
                            <td class="w-2/3 p-2">
                                <select id="location" name="location"
                                    class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base">
                                    <option value="">Pilih Lokasi</option>
                                    @if(isset($xrayCabinLocations))
                                        @foreach($xrayCabinLocations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="w-1/3 text-left p-1 sm:p-2">
                                <label for="deviceInfo" class="text-gray-700 font-bold">Merk/Tipe/Nomor Seri:</label>
                            </th>
                            <td class="w-2/3 p-2">
                                <input type="text" id="deviceInfo" name="deviceInfo"
                                    class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base">
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="w-1/3 text-left p-1 sm:p-2">
                                <label for="certificateInfo" class="text-gray-700 font-bold">Nomor dan Tanggal
                                    Sertifikat:</label>
                            </th>
                            <td class="w-2/3 p-2">
                                <input type="text" id="certificateInfo" name="certificateInfo"
                                    class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="px-1">
                    <div class="p-2">
                        <div class="mb-0">
                            <label class="inline-flex items-center">
                                <input type="checkbox" id="terpenuhi" name="terpenuhi" class="form-checkbox" value="1"
                                    checked>
                                <span class="ml-2 text-sm">Terpenuhi</span>
                            </label>
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" id="tidakterpenuhi" name="tidakterpenuhi" class="form-checkbox"
                                    value="1">
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
                                    <input type="checkbox" name="test2aab" id="test2aab" value="1"
                                        class="form-checkbox absolute place-self-center">
                                    <div class="bg-green-500 h-16 flex items-center justify-center">
                                    </div>
                                    <div class="bg-orange-500 h-16 flex items-center justify-center">
                                    </div>
                                </div>
                                <div class="grid grid-cols-[70%_30%] relative">
                                    <h3 class="text-xs font-bold text-end">TEST 2b</h3>
                                    <input type="checkbox" name="test2bab" id="test2bab" value="1" class="absolute place-self-end form-checkbox">
                                </div>
                            </div>

                            <div class="p-2">
                                <h3 class="text-xs font-bold mb-2 text-center">TEST 3</h3>
                                <div class="grid grid-cols-9 gap-0 relative">
                                    <div class="bg-cyan-100 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3ab_14" id="test3ab_14" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-cyan-200 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3ab_16" id="test3ab_16" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-sky-400 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3ab_18" id="test3ab_18" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-blue-500 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3ab_20" id="test3ab_20" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-blue-500 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3ab_22" id="test3ab_22" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-blue-700 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3ab_24" id="test3ab_24" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-blue-700 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3ab_26" id="test3ab_26" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-blue-950 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3ab_28" id="test3ab_28" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-blue-950 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3ab_30" id="test3ab_30" class="form-checkbox justify-center absolute top-4" value="1">
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
                                        <input type="checkbox" id="test1aab_36" name="test1aab_36"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1aab_32" name="test1aab_32"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1aab_30" name="test1aab_30"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1aab_24" name="test1aab_24"
                                            class="form-checkbox" value="1">
                                    </div>
                                    <div class="grid grid-rows-4 bg-[#C5E0B3] border border-black items-center justify-center">
                                        <input type="checkbox" id="test1bab_36_1" name="test1bab_36_1"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bab_32_1" name="test1bab_32_1"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bab_30_1" name="test1bab_30_1"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bab_24_1" name="test1bab_24_1"
                                            class="form-checkbox" value="1">
                                    </div>
                                    <div class="grid grid-rows-4 bg-[#92D050] border-y border-black items-center justify-center">
                                        <input type="checkbox" id="test1bab_36_2" name="test1bab_36_2"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bab_32_2" name="test1bab_32_2"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bab_30_2" name="test1bab_30_2"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bab_24_2" name="test1bab_24_2"
                                            class="form-checkbox" value="1">
                                    </div>
                                    <div class="grid grid-rows-4 bg-green-500 border border-black items-center justify-center">
                                        <input type="checkbox" id="test1bab_36_3" name="test1bab_36_3"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bab_32_3" name="test1bab_32_3"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bab_30_3" name="test1bab_30_3"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bab_24_3" name="test1bab_24_3"
                                            class="form-checkbox" value="1">
                                    </div>
                                </div>
                                <div class="grid grid-cols-[50%_50%] mt-3">
                                    <h3 class="text-xs font-bold text-center">TEST 1a</h3>
                                    <h3 class="text-xs font-bold text-end">TEST 1b</h3>
                                </div>
                            </div>

                            <div class="">
                                <div class="grid grid-rows-3 gap-0">
                                    <div class="grid grid-cols-[40%_60%] bg-sky-400 items-center justify-center relative">
                                        <div class="grid grid-rows-4 text-xs h-6 pl-1">
                                            <p class="text-[8px] font-semibold absolute -top-1">1.5 mm gaps</p>
                                            <input type="checkbox" name="test4ab_h15mm" id="test4ab_h15mm" class="form-checkbox absolute place-self-center h-6 w-6" value="1">
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                        </div>
                                        <div class="grid grid-cols-4 gap-0 text-xs h-7 px-8">
                                            <input type="checkbox" name="test4ab_v15mm" id="test4ab_v15mm" class="form-checkbox absolute place-self-center h-5 w-5" value="1">
                                            <div class="border border-black bg-white w-1"></div>
                                            <div class="border border-black bg-white w-1"></div>
                                            <div class="border border-black bg-white w-1"></div>
                                            <div class="border border-black bg-white w-1"></div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-[65%_35%] bg-sky-400 items-center justify-end relative">
                                        <div class="grid grid-rows-4 text-xs h-6 px-1 pl-10">
                                            <p class="text-[8px] font-semibold absolute -top-1">2.0 mm gaps</p>
                                            <input type="checkbox" name="test4ab_h20mm" id="test4ab_h20mm" class="form-checkbox absolute place-self-center h-6 w-6" value="1">
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                        </div>
                                        <div class="grid grid-cols-4 gap-0 text-xs h-8 px-2">
                                            <input type="checkbox" name="test4ab_v20mm" id="test4ab_v20mm" class="form-checkbox absolute place-self-center h-4 w-4" value="1">
                                            <div class="border border-black bg-white w-1"></div>
                                            <div class="border border-black bg-white w-1"></div>
                                            <div class="border border-black bg-white w-1"></div>
                                            <div class="border border-black bg-white w-1"></div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-[40%_60%] bg-sky-400 items-center justify-center relative">
                                        <div class="grid grid-rows-4 text-xs h-6 pl-1">
                                            <p class="text-[8px] font-semibold absolute -top-1">1.0 mm gaps</p>
                                            <input type="checkbox" name="test4ab_h10mm" id="test4ab_h10mm" class="form-checkbox absolute place-self-center h-6 w-6" value="1">
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                        </div>
                                        <div class="grid grid-cols-4 gap-0 text-xs h-11 px-9 py-1">
                                            <input type="checkbox" name="test4ab_v10mm" id="test4ab_v10mm" class="form-checkbox absolute place-self-center h-5 w-5" value="1">
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
                                            <input type="checkbox" id="test5ab_05mm" name="test5ab_05mm" class="form-checkbox" value="1">
                                        </div>
                                        <div class="bg-[#A8D08D] h-10 flex items-center justify-center">
                                            <input type="checkbox" id="test5ab_10mm" name="test5ab_10mm" class="form-checkbox" value="1">
                                        </div>
                                        <div class="bg-[#548135] h-10 flex items-center justify-center">
                                            <input type="checkbox" id="test5ab_15mm" name="test5ab_15mm" class="form-checkbox" value="1">
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

                    <h2 class="font-bold text-sm mb-1 text-center">GENERATOR SAMPING</h2>
                    <div class="border-2 border-black p-2 mb-2">
                        <div class="grid grid-cols-[30%_70%] gap-2 mb-4">
                            <div class="p-2">
                                <h3 class="text-xs font-bold mb-2 text-center">TEST 2a</h3>
                                <div class="grid grid-cols-2 gap-0">
                                    <input type="checkbox" name="test2ab" id="test2ab" value="1"
                                        class="form-checkbox absolute place-self-center">
                                    <div class="bg-green-500 h-16 flex items-center justify-center">
                                    </div>
                                    <div class="bg-orange-500 h-16 flex items-center justify-center">
                                    </div>
                                </div>
                                <div class="grid grid-cols-[70%_30%] relative">
                                    <h3 class="text-xs font-bold text-end">TEST 2b</h3>
                                    <input type="checkbox" name="test2bb" id="test2bb" value="1" class="absolute place-self-end form-checkbox">
                                </div>
                            </div>

                            <div class="p-2">
                                <h3 class="text-xs font-bold mb-2 text-center">TEST 3</h3>
                                <div class="grid grid-cols-9 gap-0 relative">
                                    <div class="bg-cyan-100 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3b_14" id="test3b_14" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-cyan-200 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3b_16" id="test3b_16" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-sky-400 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3b_18" id="test3b_18" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-blue-500 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3b_20" id="test3b_20" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-blue-500 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3b_22" id="test3b_22" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-blue-700 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3b_24" id="test3b_24" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-blue-700 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3b_26" id="test3b_26" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-blue-950 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3b_28" id="test3b_28" class="form-checkbox justify-center absolute top-4" value="1">
                                    </div>
                                    <div class="bg-blue-950 h-16 flex items-center justify-center">
                                        <input type="checkbox" name="test3b_30" id="test3b_30" class="form-checkbox justify-center absolute top-4" value="1">
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
                                        <input type="checkbox" id="test1ab_36" name="test1ab_36"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1ab_32" name="test1ab_32"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1ab_30" name="test1ab_30"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1ab_24" name="test1ab_24"
                                            class="form-checkbox" value="1">
                                    </div>
                                    <div class="grid grid-rows-4 bg-[#C5E0B3] border border-black items-center justify-center">
                                        <input type="checkbox" id="test1bb_36_1" name="test1bb_36_1"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bb_32_1" name="test1bb_32_1"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bb_30_1" name="test1bb_30_1"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bb_24_1" name="test1bb_24_1"
                                            class="form-checkbox" value="1">
                                    </div>
                                    <div class="grid grid-rows-4 bg-[#92D050] border-y border-black items-center justify-center">
                                        <input type="checkbox" id="test1bb_36_2" name="test1bb_36_2"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bb_32_2" name="test1bb_32_2"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bb_30_2" name="test1bb_30_2"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bb_24_2" name="test1bb_24_2"
                                            class="form-checkbox" value="1">
                                    </div>
                                    <div class="grid grid-rows-4 bg-green-500 border border-black items-center justify-center">
                                        <input type="checkbox" id="test1bb_36_3" name="test1bb_36_3"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bb_32_3" name="test1bb_32_3"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bb_30_3" name="test1bb_30_3"
                                            class="form-checkbox" value="1">
                                        <input type="checkbox" id="test1bb_24_3" name="test1bb_24_3"
                                            class="form-checkbox" value="1">
                                    </div>
                                </div>
                                <div class="grid grid-cols-[50%_50%] mt-3">
                                    <h3 class="text-xs font-bold text-center">TEST 1a</h3>
                                    <h3 class="text-xs font-bold text-end">TEST 1b</h3>
                                </div>
                            </div>

                            <div class="">
                                <div class="grid grid-rows-3 gap-0">
                                    <div class="grid grid-cols-[40%_60%] bg-sky-400 items-center justify-center relative">
                                        <div class="grid grid-rows-4 text-xs h-6 pl-1">
                                            <p class="text-[8px] font-semibold absolute -top-1">1.5 mm gaps</p>
                                            <input type="checkbox" name="test4b_h15mm" id="test4b_h15mm" class="form-checkbox absolute place-self-center h-6 w-6" value="1">
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                        </div>
                                        <div class="grid grid-cols-4 gap-0 text-xs h-7 px-8">
                                            <input type="checkbox" name="test4b_v15mm" id="test4b_v15mm" class="form-checkbox absolute place-self-center h-5 w-5" value="1">
                                            <div class="border border-black bg-white w-1"></div>
                                            <div class="border border-black bg-white w-1"></div>
                                            <div class="border border-black bg-white w-1"></div>
                                            <div class="border border-black bg-white w-1"></div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-[65%_35%] bg-sky-400 items-center justify-end relative">
                                        <div class="grid grid-rows-4 text-xs h-6 px-1 pl-10">
                                            <p class="text-[8px] font-semibold absolute -top-1">2.0 mm gaps</p>
                                            <input type="checkbox" name="test4b_h20mm" id="test4b_h20mm" class="form-checkbox absolute place-self-center h-6 w-6" value="1">
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                        </div>
                                        <div class="grid grid-cols-4 gap-0 text-xs h-8 px-2">
                                            <input type="checkbox" name="test4b_v20mm" id="test4b_v20mm" class="form-checkbox absolute place-self-center h-4 w-4" value="1">
                                            <div class="border border-black bg-white w-1"></div>
                                            <div class="border border-black bg-white w-1"></div>
                                            <div class="border border-black bg-white w-1"></div>
                                            <div class="border border-black bg-white w-1"></div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-[40%_60%] bg-sky-400 items-center justify-center relative">
                                        <div class="grid grid-rows-4 text-xs h-6 pl-1">
                                            <p class="text-[8px] font-semibold absolute -top-1">1.0 mm gaps</p>
                                            <input type="checkbox" name="test4b_h10mm" id="test4b_h10mm" class="form-checkbox absolute place-self-center h-6 w-6" value="1">
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                            <div class="border border-black bg-white h-1"></div>
                                        </div>
                                        <div class="grid grid-cols-4 gap-0 text-xs h-11 px-9 py-1">
                                            <input type="checkbox" name="test4b_v10mm" id="test4b_v10mm" class="form-checkbox absolute place-self-center h-5 w-5" value="1">
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
                                            <input type="checkbox" id="test5b_05mm" name="test5b_05mm" class="form-checkbox" value="1">
                                        </div>
                                        <div class="bg-[#A8D08D] h-10 flex items-center justify-center">
                                            <input type="checkbox" id="test5b_10mm" name="test5b_10mm" class="form-checkbox" value="1">
                                        </div>
                                        <div class="bg-[#548135] h-10 flex items-center justify-center">
                                            <input type="checkbox" id="test5b_15mm" name="test5b_15mm" class="form-checkbox" value="1">
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
                                <input type="radio" id="resultPass" name="result" value="pass" class="form-radio"
                                    onclick="document.getElementById('result').value='pass'">
                                <label for="resultPass" class="text-sm ml-2">PASS</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="resultFail" name="result" value="fail" class="form-radio"
                                    onclick="document.getElementById('result').value='fail'">
                                <label for="resultFail" class="text-sm ml-2">FAIL</label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="notes" class="block text-gray-700 font-bold mb-2">CATATAN:</label>
                        <textarea id="notes" name="notes"
                            class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base"
                            rows="2"></textarea>
                    </div>
                </div>

                <input type="hidden" id="result" name="result" value="">

                <div class="border-t-2 border-black p-2 sm:p-4">
                    <h3 class="text-xs sm:text-sm font-bold mb-2">Personel Pengamanan Penerbangan</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4">
                        <div class="grid grid-rows-2 gap-1 sm:gap-2 items-center text-center">
                            <!-- Kolom Kiri (Label 1) -->
                            <div class="text-center self-end">
                                <h4 class="font-bold">
                                    {{ Auth::user()->name }}
                                </h4>
                                <input type="hidden" name="submittedByID"value="{{ Auth::user()->id }}">
                                <label for="securityOfficerSignature" class="text-gray-700 font-normal">1. Airport
                                    Security Officer</label>
                            </div>
                            <div class="text-center self-end">
                                <label for="securitySupervisorSignature" class="text-gray-700 font-normal">2. Airport
                                    Security Supervisor</label>
                            </div>
                        </div>
                        <div>
                            <!-- Kolom Kanan (Canvas dan Tombol Clear) -->
                            <div class="signature-section mt-4">
                                <h3 class="text-lg font-semibold mb-2">Tanda Tangan Officer</h3>
                                <div class="border p-4 rounded">
                                    <canvas id="signatureCanvas" class="border border-gray-300 rounded" width="400"
                                        height="200"></canvas>
                                    <div class="mt-2 flex space-x-2">
                                        <button type="button" id="clearSignature"
                                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                            Clear
                                        </button>
                                        <button type="button" id="saveOfficerSignature"
                                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                            Save Signature
                                        </button>
                                    </div>
                                    <input type="hidden" name="officer_signature_data" id="officerSignatureData">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="status" value="pending_supervisor">

            <div class="mt-2 sm:mt-4 px-2 sm:px-0">
                <div class="mb-2 sm:mb-4">
                    <label for="supervisor_id"
                        class="block text-gray-700 font-bold text-xs sm:text-base mb-1 sm:mb-2">Pilih
                        Supervisor:</label>

                    @php
                        $supervisors = \App\Models\User::whereHas('role', function($query) {
                        $query->where('name', \App\Models\Role::SUPERVISOR);
                        })->get();
                    @endphp

                    <select name="approvedByID" id="approvedByID"
                        class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base" required>
                        <option value="">Pilih Supervisor</option>
                        @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <button type="submit" id="submitButton"
                        class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-200">
                        <span id="buttonText">Submit Form</span>
                        <span id="buttonLoading" class="hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Submitting...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
