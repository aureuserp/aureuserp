<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions as BaseActions;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\Support\Traits\RefreshesRecordState;

class EditQuotation extends EditRecord
{
    use HasRecordNavigationTabs;
    use RefreshesRecordState;
    use HasRepeaterColumnManager;

    protected static string $resource = QuotationResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('sales::filament/clusters/orders/resources/quotation/pages/edit-quotation.notification.title'))
            ->body(__('sales::filament/clusters/orders/resources/quotation/pages/edit-quotation.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->activityPlans($this->getRecord()->activityPlans())
                ->resource($this->getResource()),
            BaseActions\BackToQuotationAction::make(),
            BaseActions\CancelQuotationAction::make(),
            BaseActions\ConfirmAction::make(),
            BaseActions\CreateInvoiceAction::make(),
            BaseActions\PreviewAction::make(),
            BaseActions\SendByEmailAction::make(),
            BaseActions\LockAndUnlockAction::make(),
            DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->state == OrderState::SALE)
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('sales::filament/clusters/orders/resources/quotation/pages/edit-quotation.header-actions.notification.delete.title'))
                        ->body(__('sales::filament/clusters/orders/resources/quotation/pages/edit-quotation.header-actions.notification.delete.body')),
                ),
        ];
    }

    protected function afterSave(): void
    {
        try {
            SaleOrder::computeSaleOrder($this->getRecord());

            $this->refreshRecordState();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->body($e->getMessage())
                ->send();

            $this->halt(shouldRollbackDatabaseTransaction: true);
        }
    }

    public function refreshFormData(array $statePaths): void
    {
        parent::refreshFormData($statePaths);

        $this->rememberData();
    }
}
