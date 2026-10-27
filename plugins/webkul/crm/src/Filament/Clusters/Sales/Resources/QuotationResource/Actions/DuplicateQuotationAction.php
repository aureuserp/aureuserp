<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Sale\Models\Order;

class DuplicateQuotationAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'duplicateQuotation';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/quotation.actions.duplicate.label'))
            ->icon('heroicon-o-document-duplicate')
            ->color('gray')
            ->visible(fn (Order $record): bool => DuplicateQuotationsBulkAction::canDuplicate($record))
            ->action(function (Order $record, Action $action): void {
                $copy = DuplicateQuotationsBulkAction::duplicate($record);

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/quotation.actions.duplicate.notification', ['count' => 1]))
                    ->send();

                $action->redirect(QuotationResource::getUrl('edit', ['record' => $copy]));
            });
    }
}
