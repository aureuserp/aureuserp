<?php

namespace Webkul\Account\Filament\Resources\BillResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Models\Journal;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreateBill extends CreateRecord
{
    use HasRepeaterColumnManager;

    protected static string $resource = BillResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/bill/pages/create-bill.notification.title'))
            ->body(__('accounts::filament/resources/bill/pages/create-bill.notification.body'));
    }

    public function mount(): void
    {
        parent::mount();

        $journal = Journal::where('type', JournalType::PURCHASE)
            ->where('company_id', Auth::user()->default_company_id)
            ->first();

        $this->data['move_type'] ??= MoveType::IN_INVOICE->value;

        $this->data['journal_id'] = $journal?->id;

        $this->form->fill($this->data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['move_type'] ??= MoveType::IN_INVOICE;

        $data['date'] = now();

        return $data;
    }

    protected function afterCreate(): void
    {
        AccountFacade::computeAccountMove($this->getRecord());
    }
}
