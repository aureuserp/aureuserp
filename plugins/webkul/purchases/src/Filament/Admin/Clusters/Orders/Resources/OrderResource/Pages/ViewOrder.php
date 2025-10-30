<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\QueryException;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\Purchase\Models\Order;
use Webkul\Support\Concerns\HasRepeatableEntryColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewOrder extends ViewRecord
{
    use HasRecordNavigationTabs, HasRepeatableEntryColumnManager;

    protected static string $resource = OrderResource::class;

    protected function configureAction(Action $action): void
    {
        if ($action instanceof ChatterAction) {
            $order = Order::find($this->getRecord()->id);

            $action
                ->record($order)
                ->recordTitle($this->getRecordTitle());

            return;
        }

        parent::configureAction($action);
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->record(\Webkul\Purchase\Models\Order::find($this->getRecord()->id))
                ->setResource(static::$resource),
            DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->state == OrderState::DONE)
                ->action(function (DeleteAction $action, Order $record) {
                    try {
                        $record->delete();

                        $action->success();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('inventories::filament/clusters/orders/resources/order/pages/view-order.header-actions.delete.notification.error.title'))
                            ->body(__('inventories::filament/clusters/orders/resources/order/pages/view-order.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->failure();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/orders/resources/order/pages/view-order.header-actions.delete.notification.success.title'))
                        ->body(__('inventories::filament/clusters/orders/resources/order/pages/view-order.header-actions.delete.notification.success.body')),
                ),
        ];
    }
}
