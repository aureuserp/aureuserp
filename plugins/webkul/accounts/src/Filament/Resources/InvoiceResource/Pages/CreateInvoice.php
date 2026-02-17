<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Models\Journal;
use Illuminate\Support\Facades\Auth;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreateInvoice extends CreateRecord
{
    use HasRepeaterColumnManager;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = InvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/invoice/pages/create-invoice.notification.title'))
            ->body(__('accounts::filament/resources/invoice/pages/create-invoice.notification.body'));
    }

    public function mount(): void
    {
        parent::mount();

        $journal = Journal::where('type', JournalType::SALE)
            ->where('company_id', Auth::user()->default_company_id)
            ->first();

        $this->data['move_type'] ??= MoveType::OUT_INVOICE->value;

        $this->data['journal_id'] = $journal?->id;

        $this->form->fill($this->data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['move_type'] ??= MoveType::OUT_INVOICE;

        return $data;
    }

    protected function afterCreate(): void
    {
        AccountFacade::computeAccountMove($this->getRecord());
    }
}
