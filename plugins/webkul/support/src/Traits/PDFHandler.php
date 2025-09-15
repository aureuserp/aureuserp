<?php

namespace Webkul\Support\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait PDFHandler
{
    /**
     * Generate a PDF from HTML content.
     *
     * @param  string  $html  HTML content to convert to PDF.
     * @return PDF Returns the generated PDF instance.
     */
    protected function generatePDF(string $html)
    {
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');

        return Pdf::loadHTML($html)
            ->setPaper('A4', 'portrait')
            ->setOption('defaultFont', 'Arial')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
    }

    /**
     * Save the generated PDF to storage and return its path.
     *
     * @param  string  $html  HTML content to convert to PDF.
     * @param  string|null  $fileName  Optional filename (without extension).
     * @return string Returns the file path relative to the storage disk.
     */
    protected function savePDF(string $html, ?string $fileName = null): string
    {
        $fileName = $fileName ? Str::slug($fileName).'.pdf' : Str::uuid().'.pdf';
        $filePath = "pdfs/{$fileName}";

        $pdf = $this->generatePDF($html);
        Storage::disk('public')->put($filePath, $pdf->output());

        return $filePath;
    }

    /**
     * Generate and return a downloadable PDF response.
     *
     * @param  string  $html  HTML content to convert to PDF.
     * @param  string|null  $fileName  Optional filename (without extension).
     * @return BinaryFileResponse Returns a response for downloading the PDF.
     */
    protected function downloadPDF(string $html, ?string $fileName = null): Response
    {
        $fileName = $fileName ? Str::slug($fileName).'.pdf' : 'document-'.date('Y-m-d').'.pdf';

        return $this->generatePDF($html)->download($fileName);
    }

    /**
     * Generate a PDF, save it, and return both the file path and download response.
     *
     * @param  string  $html  HTML content to convert to PDF.
     * @param  string|null  $fileName  Optional filename (without extension).
     * @return array Returns an array with the file path and download response.
     */
    protected function saveAndDownloadPDF(string $html, ?string $fileName = null): array
    {
        $filePath = $this->savePDF($html, $fileName);
        $downloadResponse = $this->downloadPDF($html, $fileName);

        return ['path' => $filePath, 'download' => $downloadResponse];
    }
}
