<?php

namespace App\Services;

use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Orientation;
use Illuminate\Support\Collection;

class PdfService
{
    /**
     * Membuat file PDF dari satu jenis template dan kumpulan data.
     *
     * @param string $viewName Nama file Blade.
     * @param Collection $forms Data laporan.
     * @param string $formType Nama tipe form.
     * @param string $paperSize Ukuran kertas (e.g., 'A4', 'F4').
     * @return \Illuminate\Http\Response
     */
    public function generatePdfFromTemplate(string $viewName, Collection $forms, string $formType, string $paperSize = 'A4', string $orientation = 'portrait')
    {
        $viewData = [
            'forms' => $forms,
        ];

        $fileName = strtolower($formType) . '-report-' . date('Y-m-d-His') . '.pdf';

        $pdfOrientation = ($orientation === 'landscape')
            ? Orientation::Landscape
            : Orientation::Portrait;

        $pdf = Pdf::view($viewName, $viewData)
                ->orientation($pdfOrientation)
                ->margins(10, 10, 10, 10);

        // Cek apakah ukuran kertas adalah F4
        if ($paperSize === 'F4') {
            // Gunakan paperSize() untuk ukuran kustom
            // F4 memiliki dimensi 215.9 mm x 330.2 mm
            $pdf->paperSize(215.9, 330.2);
        } else {
            // Gunakan format() untuk ukuran standar seperti A4
            $pdf->format($paperSize);
        }

        return $pdf->download($fileName);
    }
}
