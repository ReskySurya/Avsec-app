<?php

namespace App\Services;

use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Orientation;
use Spatie\Browsershot\Browsershot; // <-- PENTING: Tambahkan use statement ini
use Illuminate\Support\Collection;
use Exception;

class PdfService
{
    /**
     * Membuat file PDF dari satu jenis template dan kumpulan data.
     * Disesuaikan untuk versi lama spatie/laravel-pdf di Windows Server.
     */
    public function generatePdfFromTemplate(string $viewName, Collection $forms, string $formType, string $paperSize = 'A4', string $orientation = 'portrait')
    {
        try {
            $viewData = [
                'forms' => $forms,
            ];

            $fileName = strtolower($formType) . '-report-' . date('Y-m-d-His') . '.pdf';

			$pdfOrientation = ($orientation === 'landscape')
				? Orientation::Landscape
				: Orientation::Portrait;

        
            $pdf = Pdf::view($viewName, $viewData)
                ->orientation($pdfOrientation)
                ->margins(10, 10, 10, 10)
                // Menggunakan metode withBrowsershot() untuk meneruskan konfigurasi
                // ke package Browsershot yang ada di belakangnya.
                ->withBrowsershot(function (Browsershot $browsershot) {
                    $browsershot
                        // Menambahkan argumen ke Chrome menggunakan metode yang benar
                        ->addChromiumArguments([
                            '--disable-setuid-sandbox',
                            '--disable-dev-shm-usage',
                            '--disable-accelerated-2d-canvas',
                            '--no-first-run',
                            '--no-zygote',
                            '--single-process', // Penting untuk Windows Server
                            '--disable-gpu',
                            '--disable-web-security',
                            '--disable-features=VizDisplayCompositor',
                            '--disable-background-timer-throttling',
                            '--disable-backgrounding-occluded-windows',
                            '--disable-renderer-backgrounding',
                            // '--user-data-dir=C:\\inetpub\\temp\\chrome', // Aktifkan jika masih ada masalah izin tulis
                        ])
                        // Mengatur timeout menggunakan metode yang benar
                        ->timeout(180);
                });

            // Set paper size
            if ($paperSize === 'F4') {
                $pdf->paperSize(215.9, 330.2);
            } else {
                $pdf->format($paperSize);
            }

            return $pdf->download($fileName);

        } catch (Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            // Fallback ke DomPDF jika Chrome gagal
            return $this->fallbackToDomPdf($viewName, $viewData, $fileName, $paperSize);
        }
    }

    /**
     * Fallback menggunakan DomPDF jika Chrome gagal
     */
    private function fallbackToDomPdf($viewName, $viewData, $fileName, $paperSize)
    {
        try {
            // Pastikan DomPDF terinstall: composer require barryvdh/laravel-dompdf
            $domPdf = \PDF::loadView($viewName, $viewData);

            if ($paperSize === 'F4') {
                $domPdf->setPaper([0, 0, 612, 936], 'portrait'); // F4 dalam points
            } else {
                $domPdf->setPaper($paperSize, 'portrait');
            }

            \Log::info("Fallback to DomPDF successful for: $fileName");
            return $domPdf->download($fileName);

        } catch (Exception $e) {
            \Log::error('DomPDF Fallback Error: ' . $e->getMessage());
            throw new Exception('Both Chrome and DomPDF PDF generation failed. Please check system configuration.');
        }
    }
}