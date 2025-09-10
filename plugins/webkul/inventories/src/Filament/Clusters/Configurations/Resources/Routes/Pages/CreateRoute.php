<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\Routes\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Routes\RouteResource;

class CreateRoute extends CreateRecord
{
    protected static string $resource = RouteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/route/pages/create-route.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/route/pages/create-route.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_id'] = Auth::id();

        $data['company_id'] = $data['company_id'] ?? Auth::user()->default_company_id;

        return $data;
    }
}
