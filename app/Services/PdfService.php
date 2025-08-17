<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class PdfService
{
    /**
     * Membuat file PDF dari satu jenis template dan kumpulan data.
     *
     * @param string $viewName Nama file Blade yang akan digunakan sebagai template.
     * @param Collection $forms Data laporan yang akan ditampilkan.
     * @param string $formType Nama tipe form untuk penamaan file (e.g., 'HHMD').
     * @return \Illuminate\Http\Response
     */
    public function generatePdfFromTemplate(string $viewName, Collection $forms, string $formType)
    {
        // Siapkan data umum seperti logo yang dibutuhkan oleh semua template
        // Sesuaikan path jika lokasi file Anda berbeda
        $logoAirportPath = public_path('images/airport-security-logo.png');
        $logoInjourneyPath = public_path('images/injourney-API.png');
        $tampakDepanPath = public_path('images/tampakdepan.png');
        $tampakBelakangPath = public_path('images/tampakbelakang.png');

        $viewData = [
            'forms' => $forms,
            'logoAirportBase64' => base64_encode(file_get_contents($logoAirportPath)),
            'logoInjourneyBase64' => base64_encode(file_get_contents($logoInjourneyPath)),
            'tampakDepanBase64' => base64_encode(file_get_contents($tampakDepanPath)),
            'tampakBelakangBase64' => base64_encode(file_get_contents($tampakBelakangPath)),
        ];

        // Load view yang spesifik, teruskan data, dan atur kertas
        $pdf = PDF::loadView($viewName, $viewData)
                   ->setPaper('a4', 'portrait');

        // Buat nama file yang dinamis berdasarkan tipe form
        $fileName = strtolower($formType) . '-report-' . date('Y-m-d-His') . '.pdf';

        return $pdf->download($fileName);
    }
}
