<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Support\Filament\Concerns\HasRepeatableEntryColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewJournalEntry extends ViewRecord
{
    use HasRecordNavigationTabs, HasRepeatableEntryColumnManager;

    protected static string $resource = JournalEntryResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Redirect to InvoiceResource for customer invoices and credit notes
        if (in_array($this->record->move_type, [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])) {
            $this->redirect(InvoiceResource::getUrl('view', ['record' => $this->record]));

            return;
        }

        // Redirect to BillResource for vendor bills and refunds
        if (in_array($this->record->move_type, [MoveType::IN_INVOICE, MoveType::IN_REFUND])) {
            $this->redirect(BillResource::getUrl('view', ['record' => $this->record]));

            return;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->resource($this->getResource()),
            BaseActions\ConfirmAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\ReverseAction::make(),
            BaseActions\ResetToDraftAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.delete.notification.body'))
                ),
        ];
    }
}
