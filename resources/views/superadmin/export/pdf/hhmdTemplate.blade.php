<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HHMD Forms</title>
    {{-- domPDF requires full path for CSS --}}
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>

<body>
    @foreach($forms as $form)
    <div class="form-page">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('images/airport-security-logo.png') }}" alt="Logo" style="max-width: 100%;">
                </td>
                <td class="title-cell">
                    CHECK LIST PENGUJIAN HARIAN<br>
                    PENDETEKSI LOGAM GENGGAM<br>
                    (HAND HELD METAL DETECTOR/HHMD)<br>
                    PADA KONDISI NORMAL (HIJAU)
                </td>
                <td class="logo-right">
                    <img src="{{ public_path('images/injourney-API.png') }}" alt="Injourney Logo" style="max-width: 100%;">
                </td>
            </tr>
        </table>

        <!-- Info Table -->
        <table class="info-table">
            <tr>
                <td class="label-col">Nama Operator Penerbangan:</td>
                <td class="value-col">{{ $form->operatorName ?? 'Bandar Udara Adisutcipto Yogyakarta' }}</td>
            </tr>
            <tr>
                <td class="label-col">Tanggal & Waktu Pengujian:</td>
                <td class="value-col">{{ date('d-m-Y H:i', strtotime($form->testDateTime ?? '-')) }} WIB</td>
            </tr>
            <tr>
                <td class="label-col">Lokasi Penempatan:</td>
                <td class="value-col">{{ $form->location ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Merk/Tipe/Nomor Seri:</td>
                <td class="value-col">{{ $form->deviceInfo ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Nomor dan Tanggal Sertifikat:</td>
                <td class="value-col">{{ $form->certificateInfo ?? '-' }}</td>
            </tr>
        </table>

        <!-- Checkbox Section -->
        <div class="checkbox-section">
            <div class="mb-0">
                <label class="inline-flex items-center">
                    <input type="checkbox" class="custom-checkbox-alt" {{ $form->terpenuhi ? 'checked' : '' }} disabled>
                    <span class="ml-2">Terpenuhi</span>
                </label>
            </div>
            <div class="checkbox-row">
                <label class="inline-flex items-center">
                    <input type="checkbox" class="custom-checkbox-alt" {{ ($form->tidakTerpenuhi ?? false) ? 'checked' : '' }} disabled>
                    <span class="ml-2">Tidak Terpenuhi</span>
                </label>
            </div>
        </div>


        <!-- Test 1 Section -->
        <div class="test-section">
            <div class="test-title">TEST 1</div>
            <div class="test-box">
                <div class="test-checkbox {{ ($form->test1 ?? false) ? 'checked' : '' }}">
                    <input type="checkbox" class="custom-checkbox-alt" {{ ($form->test1 ?? false) ? 'checked' : '' }} disabled>
                </div>
            </div>
        </div>

        <!-- Conditions Section -->
        <div class="conditions-section">
            <div class="condition-row">
                <label class="inline-flex items-center">
                    <input type="checkbox" class="custom-checkbox-alt" {{ ($form->testCondition1 ?? false) ? 'checked' : '' }} disabled>
                    <span class="condition-text">Letak alat uji OTP dan HHMD pada saat pengujian harus > 1m dari benda logam lain disekelilingnya.</span>
                </label>
            </div>
            <div class="condition-row">
                <label class="inline-flex items-center">
                    <input type="checkbox" class="custom-checkbox-alt" {{ ($form->testCondition2 ?? false) ? 'checked' : '' }} disabled>
                    <span class="condition-text">Jarak antara HHMD dan OTP > 3-5 cm.</span>
                </label>
            </div>
        </div>

        <!-- Results Section -->
        <div class="results-section">
            <div class="results-row">
                <span class="results-label">Hasil:</span>
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

            <div class="notes-section">
                <div class="notes-label">CATATAN:</div>
                <div class="notes-content">{{ $form->notes ?? 'Tidak ada catatan' }}</div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-title">Personel Pengamanan Penerbangan</div>
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-name">
                            <div class="name-text">{{ $form->officerName ?? 'John Doe' }}</div>
                            <div class="title-text">1. Airport Security Officer</div>
                        </div>
                        <div class="signature-name">
                            <div class="name-text">{{ $form->supervisor->name ?? 'Supervisor' }}</div>
                            <div class="title-text">2. Airport Security Supervisor</div>
                        </div>
                    </td>
                    <td>
                        <div class="signature-space">
                            @if($form->officer_signature)
                            <img src="{{ $form->officer_signature }}" alt="Officer Signature" style="max-height: 50px; width: auto;">
                            @else
                            Tanda Tangan Officer
                            @endif
                        </div>
                        <div class="signature-space">
                            @if($form->supervisor_signature)
                            <img src="{{ $form->supervisor_signature }}" alt="Supervisor Signature" style="max-height: 50px; width: auto;">
                            @else
                            Tanda Tangan Supervisor
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Add a page break after each form except the last one --}}
    @if (!$loop->last)
    <div class="page-break-after"></div>
    @endif
    @endforeach
</body>

</html>