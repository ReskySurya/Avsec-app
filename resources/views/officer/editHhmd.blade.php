@extends('layouts.app')

@section('content')
<div class="container mx-auto px-1 py-8">
    <div class="bg-white shadow-md w-fit rounded pt-6 pb-8 mb-4">
        <h1 class="text-2xl font-bold pl-6">Edit Formulir HHMD</h1>

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

        <form action="{{ route('officer.hhmd.update', ['id' => $form->reportID]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white p-4" id="format" class="mx-auto">
                <div class="border-t-2 border-x-2 border-black bg-white shadow-md">
                    <div class="flex flex-col sm:flex-row items-center justify-between">
                        <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                            class="w-20 h-20 mb-2 sm:mb-0">
                        <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
                            CHECK LIST PENGUJIAN HARIAN<br>
                            PENDETEKSI LOGAM GENGGAM<br>
                            (HAND HELD METAL DETECTOR/HHMD)<br>
                            PADA KONDISI NORMAL (HIJAU)
                        </h1>
                        <img src="{{ asset('images/injourney-API.png') }}" alt="Injourney Logo" class="w-20 h-20 mt-2 sm:mt-0">
                    </div>
                </div>

                <div class="border-2 border-black bg-white shadow">
                    <table class="w-full text-sm">
                        <tbody>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Nama Operator Penerbangan:</th>
                                <td class="w-2/3 p-2">
                                    <input type="text" name="operatorName"
                                        value="Bandar Udara Adisutjipto Yogyakarta"
                                        class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
                                </td>
                            </tr>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Tanggal & Waktu Pengujian:</th>
                                <td class="w-2/3 p-2">
                                    <input type="datetime-local" name="testDateTime"
                                        value="{{ old('testDateTime', optional($form->testDate)->format('Y-m-d\TH:i')) }}"
                                        class="w-full border rounded px-2 py-1" readonly>
                                </td>
                            </tr>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Lokasi Penempatan:</th>
                                <td class="w-2/3 p-2">
                                    <select id="location" name="location_display"
                                        class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base bg-gray-100" disabled>
                                        @if(isset($hhmdLocations))
                                        @foreach($hhmdLocations as $location)
                                        <option value="{{ $location->id }}"
                                            {{ old('location', $form->equipmentLocation->location_id) == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" name="location" value="{{ $form->equipmentLocation->location_id }}">
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
                                        class="w-full border rounded px-2 py-1" readonly>
                                </td>
                            </tr>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Nomor dan Tanggal Sertifikat:</th>
                                <td class="w-2/3 p-2">
                                    <input type="text" name="certificateInfo"
                                        value="{{ old('certificateInfo', $form->certificateInfo) }}"
                                        class="w-full border rounded px-2 py-1" readonly>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="px-4">
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

                        <div class="border-x-2 border-t-2 border-black text-center items-center pt-4">
                            <div>
                                <h2 class="font-bold mb-2">TEST 1</h2>
                                <div class="w-20 h-20 mx-auto border-2 border-black flex items-center justify-center">
                                    <input type="checkbox" id="test1" name="test1" value="1" {{ old('test1',
                                        $details->test1) ? 'checked' : '' }}
                                        onchange="updateRadioResult()">
                                </div>
                            </div>
                        </div>

                        <div class="border-x-2 border-black pt-10 pb-4">
                            <div class="flex items-center mb-0 pl-4">
                                <input type="hidden" name="testCondition1" value="0">
                                <input type="checkbox" name="testCondition1" value="1" {{ old('testCondition1',
                                    $details->testCondition1) ? 'checked' : '' }}>
                                <label class="ml-2 text-sm">Letak alat uji OTP dan HHMD pada saat pengujian harus > 1m
                                    dari benda logam lain disekelilingnya.</label>
                            </div>
                            <div class="flex items-center mb-0 pl-4">
                                <input type="hidden" name="testCondition2" value="0">
                                <input type="checkbox" name="testCondition2" value="1" {{ old('testCondition2',
                                    $details->testCondition2) ? 'checked' : '' }}>
                                <label class="ml-2 text-sm">Jarak antara HHMD dan OTP > 3-5 cm.</label>
                            </div>
                        </div>
                    </div>

                    <div class="border-t-2 border-black p-4">
                        <div class="flex items-start mb-2">
                            <label class="text-gray-700 font-bold mr-4">Hasil:</label>
                            <div class="flex flex-col">
                                <div class="flex items-center mb-0">
                                    <input type="radio" id="resultPass" name="result" value="pass" {{ old('result',
                                        $form->result) == 'pass' ? 'checked' : '' }}>
                                    <label class="text-sm ml-2">PASS</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="resultFail" name="result" value="fail" {{ old('result',
                                        $form->result) == 'fail' ? 'checked' : '' }}>
                                    <label class="text-sm ml-2">FAIL</label>
                                </div>
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
            <div class="flex items-center justify-between m-4 space-x-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Kirim Ulang Laporan
                </button>
                <a href="{{ route('dashboard.officer') }}" class="text-gray-600 hover:text-gray-800">
                    Kembali ke Dashboard
                </a>
            </div>
    </div>

    </form>
</div>
</div>

@push('scripts')
<script>
    // Fungsi untuk mengecek status checkbox dan mengupdate radio button
    function updateRadioResult() {
        const test1Checkbox = document.getElementById('test1');
        const resultPass = document.getElementById('resultPass');
        const resultFail = document.getElementById('resultFail');

        if (test1Checkbox && resultPass && resultFail) {
            if (test1Checkbox.checked) {
                resultPass.checked = true;
            } else {
                resultFail.checked = true;
            }
        }
    }

    // Event listener untuk checkbox setelah DOM sepenuhnya dimuat
    document.addEventListener('DOMContentLoaded', function() {
        const test1Checkbox = document.getElementById('test1');
        if (test1Checkbox) {
            test1Checkbox.addEventListener('change', updateRadioResult);
        }

        // Inisialisasi status awal
        updateRadioResult();
    });
</script>
@endpush
@endsection