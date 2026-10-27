<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

class QuotationRecordActions
{
    public static function make(): ActionGroup
    {
        return ActionGroup::make([
            DuplicateQuotationAction::make(),
            MarkQuotationAsSentAction::make(),
            DeleteAction::make()
                ->hidden(fn (Order $record): bool => $record->state === OrderState::SALE)
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('sales::filament/clusters/orders/resources/quotation/pages/view-quotation.header-actions.notification.delete.title'))
                        ->body(__('sales::filament/clusters/orders/resources/quotation/pages/view-quotation.header-actions.notification.delete.body')),
                ),
        ])
            ->label(__('crm::filament/clusters/sales/resources/quotation.actions.group.label'))
            ->icon('heroicon-m-cog-6-tooth')
            ->color('gray')
            ->button();
    }
}
