<?php

namespace App\Services;

// 1. Ganti 'use' statement dari DomPDF ke Laravel PDF
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Collection;

class PdfService
{
    /**
     * Membuat file PDF dari satu jenis template dan kumpulan data menggunakan spatie/laravel-pdf.
     *
     * @param string $viewName Nama file Blade yang akan digunakan sebagai template.
     * @param Collection $forms Data laporan yang akan ditampilkan.
     * @param string $formType Nama tipe form untuk penamaan file (e.g., 'HHMD').
     * @return \Illuminate\Http\Response
     */
    public function generatePdfFromTemplate(string $viewName, Collection $forms, string $formType)
    {
        // 2. Siapkan data untuk view. Kita tidak perlu lagi Base64 encode.
        //    Cukup kirim data 'forms' saja. Gambar akan dipanggil langsung di Blade.
        $viewData = [
            'forms' => $forms,
        ];

        // 3. Buat nama file yang dinamis
        $fileName = strtolower($formType) . '-report-' . date('Y-m-d-His') . '.pdf';

        // 4. Gunakan sintaks baru dari spatie/laravel-pdf.
        //    Jauh lebih sederhana dan mudah dibaca.
        return Pdf::view($viewName, $viewData)
            ->format('A4') // Mengatur format kertas ke A4
            ->margins(10, 10, 10, 10) // Mengatur margin (top, right, bottom, left) dalam milimeter
            ->download($fileName);
    }
}
