<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Actions\Print;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Webkul\Manufacturing\Models\Order;

class PrintMOAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'manufacturing.operations.print.manufacturing-order';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('manufacturing::filament/clusters/operations/actions/print/print-mo.label'))
            ->action(function (Order $record) {
                $record->load([
                    'product',
                    'uom',
                    'assignedUser',
                    'company',
                    'workOrders.operation',
                    'workOrders.workCenter',
                    'rawMaterialMoves.product',
                    'rawMaterialMoves.uom',
                ]);

                $pdf = Pdf::loadView('manufacturing::filament.clusters.operations.actions.print.print-mo', [
                    'record' => $record,
                ]);

                $pdf->setPaper('a4', 'portrait');

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->output();
                }, 'Manufacturing-Order-'.str_replace('/', '_', $record->name).'.pdf');
            });
    }
}
