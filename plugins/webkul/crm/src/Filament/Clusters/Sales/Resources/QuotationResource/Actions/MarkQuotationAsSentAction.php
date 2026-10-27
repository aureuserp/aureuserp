<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Webkul\Sale\Models\Order;

class MarkQuotationAsSentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'markQuotationAsSent';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/quotation.actions.mark-as-sent.label'))
            ->icon('heroicon-o-paper-airplane')
            ->color('gray')
            ->visible(fn (Order $record): bool => MarkQuotationsAsSentBulkAction::canMarkAsSent($record))
            ->action(function (Order $record): void {
                MarkQuotationsAsSentBulkAction::markAsSent($record);

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/quotation.actions.mark-as-sent.notification', ['count' => 1]))
                    ->send();
            });
    }
}
