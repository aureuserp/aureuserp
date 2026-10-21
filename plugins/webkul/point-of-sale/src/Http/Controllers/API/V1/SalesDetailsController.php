<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Services\SessionSalesDetailsReport;
use Webkul\PointOfSale\Support\PosAccess;

class SalesDetailsController extends Controller
{
    public function __construct(
        protected SessionSalesDetailsReport $report,
    ) {}

    public function show(Session $session): StreamedResponse
    {
        abort_unless(PosAccess::reachesSession($session), 403);

        $session = $session->refresh();

        $report = $this->report->build($session);

        $currency = $session->config?->currency?->name ?? $session->config?->company?->currency?->name;

        $pdf = Pdf::loadView('point-of-sale::filament.pos.reports.sales-details', [
            ...$report,
            'money' => fn (float $amount): string => money($amount, $currency),
        ]);

        $pdf->setPaper('a4', 'portrait');

        $name = __('point-of-sale::filament/pos/reports/sales-details.filename')
            .' - '.str_replace('/', '_', (string) $session->name).'.pdf';

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->output();
        }, $name);
    }
}
