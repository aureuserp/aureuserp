<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Actions\Print;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;

class PackageAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'inventories.operations.print.package';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('inventories::filament/clusters/operations/actions/print/packages.label'))
            ->action(function ($record) {
                $packages = $record->packages()->distinct()->get();

                $pdf = Pdf::loadView('inventories::filament.clusters.products.packages.actions.print-with-content', [
                    'records'  => $packages,
                ]);

                $pdf->setPaper('a4', 'portrait');

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->output();
                }, 'Package-'.str_replace('/', '_', $record->name).'.pdf');
            });
    }
}
