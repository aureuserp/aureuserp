<?php

namespace Webkul\Account\Filament\Resources\BillResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Support\Concerns\HasRepeaterColumnManager;

class CreateBill extends CreateRecord
{
    use HasRepeaterColumnManager;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = BillResource::class;

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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['move_type'] ??= MoveType::IN_INVOICE;

        $data['date'] = now();

        return $data;
    }

    protected function afterCreate(): void
    {
        Account::computeAccountMove($this->getRecord());
    }
}
