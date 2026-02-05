<?php

namespace Webkul\Account\Filament\Resources\TaxResource\Pages;

use Exception;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Filament\Resources\TaxResource;

class CreateTax extends CreateRecord
{
    protected static string $resource = TaxResource::class;

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

    protected function beforeCreate(): void
    {
        $data = $this->data;
        try {
            TaxResource::validateRepartitionData(
                $data['invoiceRepartitionLines'] ?? [],
                $data['refundRepartitionLines'] ?? []
            );
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title('Invalid Repartition Lines')
                ->body($e->getMessage())
                ->send();

            $this->halt();
        }
    }
}
