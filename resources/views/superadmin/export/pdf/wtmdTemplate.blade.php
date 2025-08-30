<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WTMD Forms</title>
    <link rel="stylesheet" href="css/forms-common.css">
    
</head>
<body>
    <!-- Loop untuk multiple forms: @foreach($forms as $form) -->
    <div class="page-break-after">
        <div class="wtmd-container">
            <!-- Header -->
            <div class="wtmd-header">
                <div class="flex items-center justify-between">
                    <div class="logo-placeholder">LOGO<br>AIRPORT</div>
                    <h1 class="text-xl font-bold text-center flex-grow">
                        CHECK LIST PENGUJIAN HARIAN<br>
                        GAWANG PENDETEKSI LOGAM<br>
                        (WALK THROUGH METAL DETECTOR/WTMD)
                    </h1>
                    <div class="logo-placeholder">INJOURNEY<br>LOGO</div>
                </div>
            </div>

            <!-- Content -->
            <div class="wtmd-content">
                <!-- Info Table -->
                <table class="info-table">
                    <tr>
                        <th class="label-col">Nama Operator Penerbangan:</th>
                        <td class="value-col">{{ $form->operatorName ?? 'Default Operator' }}</td>
                    </tr>
                    <tr>
                        <th class="label-col">Tanggal & Waktu Pengujian:</th>
                        <td class="value-col">{{ date('d-m-Y H:i', strtotime($form->testDateTime ?? '2023-06-07 03:05:00')) }} WIB</td>
                    </tr>
                    <tr>
                        <th class="label-col">Lokasi Penempatan:</th>
                        <td class="value-col">{{ $form->location ?? 'Default Location' }}</td>
                    </tr>
                    <tr>
                        <th class="label-col">Merk/Tipe/Nomor Seri:</th>
                        <td class="value-col">{{ $form->deviceInfo ?? 'Default Device' }}</td>
                    </tr>
                    <tr>
                        <th class="label-col">Nomor dan Tanggal Sertifikat:</th>
                        <td class="value-col">{{ $form->certificateInfo ?? 'Default Certificate' }}</td>
                    </tr>
                </table>

                <!-- Checkbox Section -->
                <div class="checkbox-section">
                    <div class="checkbox-row">
                        <input class="custom-checkbox-alt" type="checkbox" checked disabled>
                        <span class="text-sm ml-2">Terpenuhi</span>
                    </div>
                    <div class="checkbox-row">
                        <input class="custom-checkbox-alt" type="checkbox" disabled>
                        <span class="text-sm ml-2">Tidak Terpenuhi</span>
                    </div>
                </div>

                <!-- WTMD Diagram Section -->
                <div class="wtmd-diagram-container">
                    <!-- Front View -->
                    <div class="diagram-section">
                        <div class="relative">
                            <!-- Placeholder untuk gambar tampak depan -->
                            <div style="width: 115px; height: 200px; border: 2px solid #ccc; margin: 0 auto; background: #f5f5f5; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                TAMPAK DEPAN
                            </div>
                            <p class="diagram-label">DEPAN</p>
                            
                            <!-- Test Controls Overlay -->
                            <div class="test-controls">
                                <!-- TEST 1 -->
                                <div class="test-group">
                                    <div class="test-item">
                                        <div class="in-out-controls">
                                            <div class="in-out-item">
                                                <span class="in-out-label">IN</span>
                                                <input class="custom-checkbox-alt" type="checkbox" checked disabled>
                                            </div>
                                            <div class="in-out-item">
                                                <span class="in-out-label">OUT</span>
                                                <input class="custom-checkbox-alt" type="checkbox" checked disabled>
                                            </div>
                                        </div>
                                        <svg class="arrow-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                        <span class="test-label">TEST 1</span>
                                    </div>
                                </div>

                                <!-- TEST 2 -->
                                <div class="test-group test-group-bottom">
                                    <div class="test-item">
                                        <div class="in-out-controls">
                                            <div class="in-out-item">
                                                <span class="in-out-label">IN</span>
                                                <input class="custom-checkbox-alt" type="checkbox" checked disabled>
                                            </div>
                                            <div class="in-out-item">
                                                <span class="in-out-label">OUT</span>
                                                <input class="custom-checkbox-alt" type="checkbox" checked disabled>
                                            </div>
                                        </div>
                                        <svg class="arrow-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                        <span class="test-label">TEST 2</span>
                                    </div>
                                </div>

                                <!-- TEST 4 -->
                                <div class="test-group test-group-end">
                                    <div class="test-item">
                                        <div class="in-out-controls">
                                            <div class="in-out-item">
                                                <span class="in-out-label">IN</span>
                                                <input class="custom-checkbox-alt" type="checkbox" checked disabled>
                                            </div>
                                            <div class="in-out-item">
                                                <span class="in-out-label">OUT</span>
                                                <input class="custom-checkbox-alt" type="checkbox" checked disabled>
                                            </div>
                                        </div>
                                        <svg class="arrow-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                        <span class="test-label">TEST 4</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Back View -->
                    <div class="diagram-section">
                        <div class="relative">
                            <!-- Placeholder untuk gambar tampak belakang -->
                            <div style="width: 115px; height: 200px; border: 2px solid #ccc; margin: 0 auto; background: #f5f5f5; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                TAMPAK BELAKANG
                            </div>
                            <p class="diagram-label">BELAKANG</p>
                            
                            <!-- Test Controls Overlay -->
                            <div class="test-controls test-controls-right">
                                <div style="margin-top: 144px;">
                                    <!-- TEST 3 -->
                                    <div class="test-group">
                                        <div class="test-item test-item-right">
                                            <span class="test-label">TEST 3</span>
                                            <svg class="arrow-icon arrow-left" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                            <div class="in-out-controls">
                                                <div class="in-out-item in-out-item-right">
                                                    <input class="custom-checkbox-alt" type="checkbox" checked disabled>
                                                    <span class="in-out-label">IN</span>
                                                </div>
                                                <div class="in-out-item">
                                                    <input class="custom-checkbox-alt" type="checkbox" checked disabled>
                                                    <span class="in-out-label">OUT</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="results-section">
                    <div class="results-row">
                        <span class="results-label">Hasil:</span>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input class="custom-radio" type="radio" checked disabled>
                                <span class="text-sm ml-2">PASS</span>
                            </div>
                            <div class="radio-option">
                                <input class="custom-radio" type="radio" disabled>
                                <span class="text-sm ml-2">FAIL</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="notes-section">
                        <div class="notes-label">CATATAN:</div>
                        <div class="notes-content">{{ $form->notes ?? 'Catatan pengujian dapat ditulis di sini...' }}</div>
                    </div>
                </div>

                <!-- Signature Section -->
                <div class="signature-section">
                    <div class="signature-title">Personel Pengamanan Penerbangan</div>
                    <table class="signature-table">
                        <tr>
                            <td>
                                <div class="signature-name">
                                    <div class="name-text">{{ $form->officerName ?? 'Officer Name' }}</div>
                                    <div class="title-text">1. Airport Security Officer</div>
                                </div>
                                <div class="signature-name">
                                    <div class="name-text">{{ $form->supervisor->name ?? 'Supervisor Name' }}</div>
                                    <div class="title-text">2. Airport Security Supervisor</div>
                                </div>
                            </td>
                            <td>
                                <div class="signature-space">Tanda Tangan Officer</div>
                                <div class="signature-space">Tanda Tangan Supervisor</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End loop: @endforeach -->
</body>
</html>