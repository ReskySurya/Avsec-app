@extends('layouts.app')

@section('content')
<div class="container mx-auto px-1 py-8">
    <div class="bg-white shadow-md w-fit rounded pt-6 pb-8 mb-4">
        <h1 class="text-2xl font-bold pl-6">Edit Formulir WTMD</h1>

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

        <form action="{{ route('officer.wtmd.update', ['id' => $form->reportID]) }}" method="POST">
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
                                GAWANG PENDETEKSI LOGAM<br>
                                (WALK THROUGH METAL DETECTOR/WTMD)
                            </h1>
                            <img src="https://via.placeholder.com/80x80" alt="Additional Logo"
                                class="w-20 h-20 mt-2 sm:mt-0">
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
                                            class="w-full border rounded px-2 py-1" readonly>
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
                                            @if(isset($wtmdLocations))
                                            @foreach($wtmdLocations as $location)
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

                        <div class="px-4">
                            <div class="p-2">
                                <div class="mb-0">
                                    <label class="inline-flex items-center">
                                        <input type="hidden" name="terpenuhi" value="0">
                                        <input type="checkbox" name="terpenuhi" value="1" {{ old('terpenuhi',
                                        $form->isFullFilled) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm">Terpenuhi</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="hidden" name="tidakterpenuhi" value="0">
                                        <input type="checkbox" name="tidakterpenuhi" value="1" {{ old('tidakterpenuhi',
                                        $form->tidakterpenuhi) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm">Tidak Terpenuhi</span>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 border-x-2 border-t-2 border-black text-center items-center">
                                <div class="relative">
                                    <div>
                                        <img src="{{asset('images/tampakdepan.png')}}" alt="tampakdepan"
                                            class="w-full scale-75">
                                        <p class="text-sm font-semibold pb-20">DEPAN</p>
                                    </div>

                                    <div class="absolute inset-0 flex flex-col items-start pt-44 pointer-events-auto">
                                        <div class="mb-1">
                                            <div class="flex items-center gap-1">
                                                <div class="flex flex-col gap-2">
                                                    <div class="flex items-center gap-1 pl-2.5">
                                                        <span class="text-[10px]">IN</span>
                                                        <input type="hidden" name="test1_in_depan" value="0">
                                                        <input type="checkbox" id="test1_in_depan" name="test1_in_depan" value="1" {{ old('test1_in_depan', $details->test1_in_depan) ? 'checked' : '' }}
                                                            class="form-checkbox h-4 w-4 bg-white" onchange="updateRadioResult()">
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[10px]">OUT</span>
                                                        <input type="hidden" name="test1_out_depan" value="0">
                                                        <input type="checkbox" name="test1_out_depan" {{ old('test1_out_depan', $details->test1_out_depan) ? 'checked' : '' }}
                                                            id="test1_out_depan" class="form-checkbox h-4 w-4 bg-white"
                                                            value="1">
                                                    </div>
                                                </div>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="4" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                                <span class="text-xs font-bold">TEST 1</span>
                                            </div>
                                        </div>

                                        <div class="mb-28">
                                            <div class="flex items-center gap-1">
                                                <div class="flex flex-col gap-2">
                                                    <div class="flex items-center gap-1 pl-2.5">
                                                        <span class="text-[10px]">IN</span>
                                                        <input type="hidden" name="test2_in_depan" value="0">
                                                        <input type="checkbox" name="test2_in_depan" {{ old('test2_in_depan', $details->test2_in_depan) ? 'checked' : '' }} id="test2_in_depan"
                                                            class="form-checkbox h-4 w-4 bg-white" value="1">
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[10px]">OUT</span>
                                                        <input type="hidden" name="test2_out_depan" value="0">
                                                        <input type="checkbox" name="test2_out_depan" {{ old('test2_out_depan', $details->test2_out_depan) ? 'checked' : '' }}
                                                            id="test2_out_depan" class="form-checkbox h-4 w-4 bg-white" value="1">
                                                    </div>
                                                </div>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="4" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                                <span class="text-xs font-bold">TEST 2</span>
                                            </div>
                                        </div>

                                        <div class="mb-8">
                                            <div class="flex items-center gap-1">
                                                <div class="flex flex-col gap-2">
                                                    <div class="flex items-center gap-1 pl-2.5">
                                                        <span class="text-[10px]">IN</span>
                                                        <input type="hidden" name="test4_in_depan" value="0">
                                                        <input type="checkbox" name="test4_in_depan" id="test4_in_depan" {{ old('test4_in_depan', $details->test4_in_depan) ? 'checked' : '' }}
                                                            class="form-checkbox h-4 w-4 bg-white" value="1">
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[10px]">OUT</span>
                                                        <input type="hidden" name="test4_out_depan" value="0">
                                                        <input type="checkbox" name="test4_out_depan"
                                                            id="test4_out_depan" {{ old('test4_out_depan', $details->test4_out_depan) ? 'checked' : '' }} class="form-checkbox h-4 w-4 bg-white"
                                                            value="1">
                                                    </div>
                                                </div>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="4" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                                <span class="text-xs font-bold">TEST 4</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative">
                                    <div>
                                        <img src="{{asset('images/tampakbelakang.png')}}" alt="tampakbelakang"
                                            class="w-full scale-75">
                                        <p class="text-sm font-semibold pb-20">BELAKANG</p>
                                    </div>

                                    <div class="absolute inset-0 flex flex-col items-end pr-2 pt-4 pointer-events-auto">
                                        <div class="mt-52">
                                            <div class="flex items-center gap-1">
                                                <span class="text-xs font-bold">TEST 3</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 rotate-180"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="4" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                                <div class="flex flex-col gap-2">
                                                    <div class="flex items-center gap-1 pr-2.5">
                                                        <input type="hidden" name="test3_in_belakang" value="0">
                                                        <input type="checkbox" name="test3_in_belakang"
                                                            id="test3_in_belakang" {{ old('test3_in_belakang', $details->test3_in_belakang) ? 'checked' : '' }} class="form-checkbox h-4 w-4 bg-white"
                                                            class="form-checkbox h-4 w-4 bg-white" value="1">
                                                        <span class="text-[10px]">IN</span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <input type="hidden" name="test3_out_belakang" value="0">
                                                        <input type="checkbox" name="test3_out_belakang"
                                                            id="test3_out_belakang" {{ old('test3_out_belakang', $details->test3_out_belakang) ? 'checked' : '' }}
                                                            class="form-checkbox h-4 w-4 bg-white" value="1">
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
                                <textarea name="note" class="w-full border rounded px-2 py-1" rows="3">{{ old('note', $form->note) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between m-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Kirim Ulang Laporan
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
        const test1Checkboxes = [
            document.getElementById('test1_in_depan'),
            document.getElementById('test1_out_depan')
        ];
        const test2Checkboxes = [
            document.getElementById('test2_in_depan'),
            document.getElementById('test2_out_depan')
        ];
        const test3Checkboxes = [
            document.getElementById('test3_in_belakang'),
            document.getElementById('test3_out_belakang')
        ];
        const test4Checkboxes = [
            document.getElementById('test4_in_depan'),
            document.getElementById('test4_out_depan')
        ];

        const resultPass = document.getElementById('resultPass');
        const resultFail = document.getElementById('resultFail');
        const resultHidden = document.getElementById('result');

        if (resultPass && resultFail && resultHidden) {
            // Cek apakah semua checkbox tercentang
            const allChecked = [...test1Checkboxes, ...test2Checkboxes, ...test3Checkboxes, ...test4Checkboxes]
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
            'test1_in_depan', 'test1_out_depan',
            'test2_in_depan', 'test2_out_depan',
            'test3_in_belakang', 'test3_out_belakang',
            'test4_in_depan', 'test4_out_depan'
        ];

        allCheckboxIds.forEach(id => {
            const checkbox = document.getElementById(id);
            if (checkbox) {
                checkbox.addEventListener('change', updateRadioResult);
            }
        });

        // Nonaktifkan radio button agar tidak bisa diklik manual
        const resultPass = document.getElementById('resultPass');
        const resultFail = document.getElementById('resultFail');

        if (resultPass) {
            resultPass.addEventListener('click', (e) => e.preventDefault());
        }
        if (resultFail) {
            resultFail.addEventListener('click', (e) => e.preventDefault());
        }

        // Inisialisasi status awal
        updateRadioResult();
    });
</script>
@endpush
@endsection