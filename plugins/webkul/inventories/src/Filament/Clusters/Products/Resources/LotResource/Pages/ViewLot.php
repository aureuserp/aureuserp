<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\QueryException;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Inventory\Models\Lot;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewLot extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = LotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label(__('inventories::filament/clusters/products/resources/lot/pages/view-lot.header-actions.print.label'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function (Lot $record) {
                    $pdf = PDF::loadView('inventories::filament.clusters.products.lots.actions.print', [
                        'records' => collect([$record]),
                    ]);

                    $pdf->setPaper('a4', 'portrait');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'Lot-'.str_replace('/', '_', $record->name).'.pdf');
                }),
            DeleteAction::make()
                ->action(function (DeleteAction $action, Lot $record) {
                    try {
                        $record->delete();

                        $action->success();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('inventories::filament/clusters/products/resources/lot/pages/view-lot.header-actions.delete.notification.error.title'))
                            ->body(__('inventories::filament/clusters/products/resources/lot/pages/view-lot.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->failure();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/products/resources/lot/pages/view-lot.header-actions.delete.notification.success.title'))
                        ->body(__('inventories::filament/clusters/products/resources/lot/pages/view-lot.header-actions.delete.notification.success.body')),
                ),
        ];
    }
}
