<?php

namespace Webkul\Product\Filament\Resources\AttributeResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Product\Filament\Resources\AttributeResource;

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
}
