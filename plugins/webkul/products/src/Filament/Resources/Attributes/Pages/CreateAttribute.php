<?php

namespace Webkul\Product\Filament\Resources\Attributes\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Filament\Resources\Attributes\AttributeResource;

class CreateAttribute extends CreateRecord
{
    protected static string $resource = AttributeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('products::filament/resources/attribute/pages/create-attribute.notification.title'))
            ->body(__('products::filament/resources/attribute/pages/create-attribute.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_id'] = Auth::id();

        return $data;
    }
}
