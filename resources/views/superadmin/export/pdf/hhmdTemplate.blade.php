<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HHMD Forms</title>
    <style>
        {!! file_get_contents(public_path('css/pdf.css')) !!}
    </style>
</head>
<body class="m-0 p-0">
    @foreach($forms as $form)
    <div class="page-break-after">
        <div class="bg-white p-4" style="width: 210mm;">
            <div id="format" class="mx-auto">
                <div class="border-t-2 border-x-2 border-black bg-white shadow-md px-4 py-2">
                    <div class="flex items-center justify-between">
                        <img src="data:image/png;base64,{{ $logoAirportBase64 }}" alt="Logo" class="w-16 h-16">
                        <h1 class="text-sm font-bold text-center flex-grow px-2">
                            CHECK LIST PENGUJIAN HARIAN<br>
                            PENDETEKSI LOGAM GENGGAM<br>
                            (HAND HELD METAL DETECTOR/HHMD)<br>
                            PADA KONDISI NORMAL (HIJAU)
                        </h1>
                        <img src="data:image/png;base64,{{ $logoInjourneyBase64 }}" alt="Injourney Logo" class="w-20 h-16">
                    </div>
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

                    <div class="px-4">
                        <div class="p-4">
                            <div class="mb-0">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="custom-checkbox-alt" {{ $form->terpenuhi ? 'checked' : '' }} disabled>
                                    <span class="ml-2 text-sm">Terpenuhi</span>
                                </label>
                            </div>
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="custom-checkbox-alt" {{ $form->tidakTerpenuhi ? 'checked' : '' }} disabled>
                                    <span class="ml-2 text-sm">Tidak Terpenuhi</span>
                                </label>
                            </div>
                        </div>
                        <div class="border-x-2 border-t-2 border-black text-center items-center pt-10">
                            <div>
                                <h2 class="font-bold mb-1">TEST 1</h2>
                                <div class="w-20 h-20 mx-auto border-2 border-black flex items-center justify-center">
                                    <input type="checkbox" class="custom-checkbox-alt" {{ $form->test1 ? 'checked' : '' }} disabled>
                                </div>
                            </div>
                        </div>

                        <div class="border-x-2 border-black pt-10 pb-10">
                            <div class="flex items-center mb-0 pl-4">
                                <input type="checkbox" class="custom-checkbox-alt" {{ $form->testCondition1 ? 'checked' : '' }} disabled>
                                <label class="ml-2 text-sm">Letak alat uji OTP dan HHMD pada saat pengujian harus > 1m dari benda logam lain disekelilingnya.</label>
                            </div>
                            <div class="flex items-center mb-0 pl-4">
                                <input type="checkbox" class="custom-checkbox-alt" {{ $form->testCondition2 ? 'checked' : '' }} disabled>
                                <label class="ml-2 text-sm">Jarak antara HHMD dan OTP > 3-5 cm.</label>
                            </div>
                        </div>
                    </div>

                    <div class="border-t-2 border-black p-4">
                        <div class="flex items-start mb-4">
                            <label class="text-gray-700 font-bold mr-4">Hasil:</label>
                            <div class="flex flex-col">
                                <div class="flex items-center mb-0">
                                    <input type="radio" class="custom-radio" {{ $form->result == 'pass' ? 'checked' : '' }} disabled>
                                    <label class="text-sm ml-2">PASS</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" class="custom-radio" {{ $form->result == 'fail' ? 'checked' : '' }} disabled>
                                    <label class="text-sm ml-2">FAIL</label>
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
