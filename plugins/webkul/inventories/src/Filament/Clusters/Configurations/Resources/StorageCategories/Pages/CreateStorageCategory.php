<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategories\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategories\StorageCategoryResource;

class CreateStorageCategory extends CreateRecord
{
    protected static string $resource = StorageCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/storage-category/pages/create-storage-category.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/storage-category/pages/create-storage-category.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_id'] = Auth::id();

        return $data;
    }
}
