<?php

namespace Webkul\Account\Filament\Resources\TaxResource\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Filament\Resources\TaxResource;

class CreateTax extends CreateRecord
{
    protected static string $resource = TaxResource::class;

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/tax/pages/create-tax.notification.title'))
            ->body(__('accounts::filament/resources/tax/pages/create-tax.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $data['company_id'] = $user->default_company_id;
        $data['creator_id'] = $user->id;

        return $data;
    }
}
