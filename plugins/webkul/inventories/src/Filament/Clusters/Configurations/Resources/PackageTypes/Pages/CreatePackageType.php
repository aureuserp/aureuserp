<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\PackageTypeResource;

class CreatePackageType extends CreateRecord
{
    protected static string $resource = PackageTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/package-type/pages/create-package-type.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/package-type/pages/create-package-type.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_id'] = Auth::id();

        $data['company_id'] = Auth::user()->default_company_id;

        return $data;
    }
}
