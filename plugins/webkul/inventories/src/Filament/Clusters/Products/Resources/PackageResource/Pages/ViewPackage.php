<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\QueryException;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Inventory\Models\Package;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewPackage extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('print-without-content')
                    ->label(__('inventories::filament/clusters/products/resources/package/pages/view-package.header-actions.print.actions.without-content.label'))
                    ->color('gray')
                    ->action(function (Package $record) {
                        $pdf = PDF::loadView('inventories::filament.clusters.products.packages.actions.print-without-content', [
                            'records' => collect([$record]),
                        ]);

                        $pdf->setPaper('a4', 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'Package-'.$record->name.'.pdf');
                    }),
                Action::make('print-with-content')
                    ->label(__('inventories::filament/clusters/products/resources/package/pages/view-package.header-actions.print.actions.with-content.label'))
                    ->color('gray')
                    ->action(function (Package $record) {
                        $pdf = PDF::loadView('inventories::filament.clusters.products.packages.actions.print-with-content', [
                            'records' => collect([$record]),
                        ]);

                        $pdf->setPaper('a4', 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'Package-'.$record->name.'.pdf');
                    }),
            ])
                ->label(__('inventories::filament/clusters/products/resources/package/pages/view-package.header-actions.print.label'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->button(),
            DeleteAction::make()
                ->action(function (DeleteAction $action, Package $record) {
                    try {
                        $record->delete();

                        $action->success();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('inventories::filament/clusters/products/resources/package/pages/view-package.header-actions.delete.notification.error.title'))
                            ->body(__('inventories::filament/clusters/products/resources/package/pages/view-package.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->failure();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/products/resources/package/pages/view-package.header-actions.delete.notification.success.title'))
                        ->body(__('inventories::filament/clusters/products/resources/package/pages/view-package.header-actions.delete.notification.success.body')),
                ),
        ];
    }
}
