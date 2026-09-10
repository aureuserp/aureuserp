<?php

namespace Webkul\Purchase\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentGenerator
{
    public function requestForQuotationPdf($record): string
    {
        return $this->render(
            'Request for Quotation-'.str_replace('/', '_', $record->name).'.pdf',
            'purchases::filament.admin.clusters.orders.orders.actions.print-quotation',
            $record
        );
    }

    public function purchaseOrderPdf($record): string
    {
        return $this->render(
            'Purchase Order-'.str_replace('/', '_', $record->name).'.pdf',
            'purchases::filament.admin.clusters.orders.orders.actions.print-purchase-order',
            $record
        );
    }

    protected function render(string $path, string $view, $record): string
    {
        if (! Storage::exists($path)) {
            Storage::disk('public')->put($path, Pdf::loadView($view, ['records' => [$record]])->output());
        }

        return $path;
    }
}
