<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\OrderTemplateProductResource;

class CreateOrderTemplateProduct extends CreateRecord
{
    protected static string $resource = OrderTemplateProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('sales::filament/clusters/configurations/resources/order-template/pages/create-order-template.notification.title'))
            ->body(__('sales::filament/clusters/configurations/resources/order-template/pages/create-order-template.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $data['company_id'] = $user->default_company_id;

        $data['creator_id'] = $user->id;

        return $data;
    }
}
