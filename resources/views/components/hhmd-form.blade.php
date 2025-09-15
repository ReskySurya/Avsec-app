@push('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

<div class="bg-white p-4 mt-10 w-full max-w-full"
    x-data="{
        hhmdLocations: {{ Js::from($hhmdLocations) }},
        selectedLocationId: '',
        deviceInfo: '',
        certificateInfo: '',
        updateFields() {
            const selectedLocation = this.hhmdLocations.find(loc => loc.location_id == this.selectedLocationId);
            if (selectedLocation) {
                this.deviceInfo = selectedLocation.merk_type;
                this.certificateInfo = selectedLocation.certificateInfo;
            } else {
                this.deviceInfo = '';
                this.certificateInfo = '';
            }
        }
    }">


    <!-- <p>INI FORM HHMD</p> -->
    <div id="format" class="mx-auto w-full">
        <!-- <div class="border-t-2 border-x-2 border-black bg-white shadow-md p-4">
            <div class="flex flex-col sm:flex-row items-center justify-between">
                <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo" class="w-20 h-20 mb-2 sm:mb-0">
                <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
                    CHECK LIST PENGUJIAN HARIAN<br>
                    PENDETEKSI LOGAM GENGGAM<br>
                    (HAND HELD METAL DETECTOR/HHMD)<br>
                    PADA KONDISI NORMAL (HIJAU)
                </h1>
                <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo" class="w-20 h-20 mt-2 sm:mt-0">
            </div>
        </div> -->
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-3 sm:px-6 py-4 sm:py-6 text-white">
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                {{-- Logo kiri - Hidden on mobile atau ukuran kecil --}}
                <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                    class="w-12 h-12 sm:w-20 sm:h-20 order-first sm:order-none">

                {{-- Teks tengah --}}
                <div class="text-center flex-1 px-2">
                    <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
                        CHECK LIST PENGUJIAN HARIAN<br>
                        PENDETEKSI LOGAM GENGGAM<br>
                        (HAND HELD METAL DETECTOR/HHMD)<br>
                        PADA KONDISI NORMAL (HIJAU)
                    </h1>
                </div>

                {{-- Logo kanan --}}
                <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo"
                    class="w-16 h-16 sm:w-24 sm:h-24 order-last sm:order-none">
            </div>
        </div>

        <form id="hhmdForm" method="POST" action="{{ route('daily-test.hhmd.store') }}" enctype="multipart/form-data" onsubmit="onFormSubmit(event)" class="mt-0">
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
                                    class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base"
                                    x-model="selectedLocationId" @change="updateFields">
                                    <option value="">Pilih Lokasi</option>
                                    @if(isset($hhmdLocations))
                                    @foreach($hhmdLocations as $location)
                                    <option value="{{ $location['location_id'] }}">
                                        {{ $location['location_name'] }}
                                    </option>
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
                                    class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base"
                                    x-model="deviceInfo">
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="w-1/3 text-left p-1 sm:p-2">
                                <label for="certificateInfo" class="text-gray-700 font-bold">Nomor dan Tanggal
                                    Sertifikat:</label>
                            </th>
                            <td class="w-2/3 p-2">
                                <input type="text" id="certificateInfo" name="certificateInfo"
                                    class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base"
                                    x-model="certificateInfo" readonly>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="px-4">
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
                    <div class="border-x-2 border-t-2 border-black text-center items-center pt-10">
                        <div>
                            <h2 class="font-bold mb-2">TEST 1</h2>
                            <div class="w-20 h-20 mx-auto border-2 border-black flex items-center justify-center">
                                <input type="checkbox" id="test1" name="test1" class="form-checkbox h-5 w-5"
                                    onchange="updateResult()" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="border-x-2 border-black pt-10 pb-10">
                        <div class="flex items-center mb-0 pl-4">
                            <input type="checkbox" id="testCondition1" name="testCondition1" class="form-checkbox"
                                value="1" checked>
                            <label for="testCondition1" class="ml-2 text-sm">Letak alat uji OTP dan HHMD pada saat
                                pengujian harus > 1m dari benda logam lain disekelilingnya.</label>
                        </div>
                        <div class="flex items-center mb-0 pl-4">
                            <input type="checkbox" id="testCondition2" name="testCondition2" class="form-checkbox"
                                value="1" checked>
                            <label for="testCondition2" class="ml-2 text-sm">Jarak antara HHMD dan OTP > 3-5 cm.</label>
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
                                <input type="hidden" name="submittedByID" value="{{ Auth::user()->id }}">
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
                                    <canvas id="signatureCanvas" class="border border-gray-300 rounded" width="220"
                                        height="200"></canvas>
                                    <div class="mt-2 flex space-x-2">
                                        <button type="button" id="clearSignature"
                                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                            Hapus
                                        </button>
                                        <button type="button" id="saveSubmitterSignature"
                                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                            Simpan
                                        </button>
                                    </div>
                                    <input type="hidden" name="submitterSignature" id="submitterSignatureData">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-2 sm:mt-4 px-2 sm:px-0">
                <div class="mb-2 sm:mb-4">
                    <label for="approvedByID"
                        class="block text-gray-700 font-bold text-xs sm:text-base mb-1 sm:mb-2">Pilih
                        Supervisor:
                    </label>

                    @php
                    $supervisors = \App\Models\User::whereHas('role', function ($query) {
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
                        <span id="buttonText">Kirim Form</span>
                        <span id="buttonLoading" class="hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Mengirim...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>