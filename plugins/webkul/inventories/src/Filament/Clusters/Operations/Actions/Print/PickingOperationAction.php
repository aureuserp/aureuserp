<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions\Print;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;

class PickingOperationAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.print.picking-operation';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('inventories::filament/clusters/operations/actions/print/picking-operations.label'))
            ->action(function ($record) {
                $pdf = Pdf::loadView('inventories::filament.clusters.operations.actions.print-picking-operations', [
                    'records'  => [$record],
                ]);

                $pdf->setPaper('a4', 'portrait');

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->output();
                }, 'Picking Operations-'.str_replace('/', '_', $record->name).'.pdf');
            });
    }
}
