<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\QueryException;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Filament\Clusters\Operations\Actions as OperationActions;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource;
use Webkul\Inventory\Models\Receipt;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewReceipt extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->setResource(static::$resource),
            ActionGroup::make([
                OperationActions\Print\PickingOperationAction::make(),
                OperationActions\Print\DeliverySlipAction::make(),
                OperationActions\Print\PackageAction::make(),
                OperationActions\Print\LabelsAction::make(),
            ])
                ->label(__('inventories::filament/clusters/operations/resources/receipt/pages/edit-receipt.header-actions.print.label'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->button(),
            DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->state == OperationState::DONE)
                ->action(function (DeleteAction $action, Receipt $record) {
                    try {
                        $record->delete();

                        $action->success();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('inventories::filament/clusters/operations/resources/receipt/pages/view-receipt.header-actions.delete.notification.error.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/receipt/pages/view-receipt.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->failure();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/operations/resources/receipt/pages/view-receipt.header-actions.delete.notification.success.title'))
                        ->body(__('inventories::filament/clusters/operations/resources/receipt/pages/view-receipt.header-actions.delete.notification.success.body')),
                ),
        ];
    }
}
