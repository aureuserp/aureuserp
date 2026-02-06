<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveType;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Journal;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreateJournalEntry extends CreateRecord
{
    use HasRepeaterColumnManager;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = JournalEntryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounting::filament/clusters/accounting/resources/journal-entry/pages/create-journal-entry.notification.title'))
            ->body(__('accounting::filament/clusters/accounting/resources/journal-entry/pages/create-journal-entry.notification.body'));
    }

    public function mount(): void
    {
        parent::mount();

        $journal = Journal::where('type', JournalType::GENERAL)
            ->where('company_id', Auth::user()->default_company_id)
            ->first();

        $this->data['move_type'] ??= MoveType::ENTRY->value;

        $this->data['journal_id'] = $journal?->id;

        $this->form->fill($this->data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['move_type'] ??= MoveType::ENTRY;

        return $data;
    }

    protected function afterCreate(): void
    {
        AccountFacade::computeAccountMove($this->getRecord());
    }
}
