<?php

namespace Webkul\Security\Filament\Resources\UserResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Security\Filament\Resources\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('security::filament/resources/user/pages/create-user.notification.title'))
            ->body(__('security::filament/resources/user/pages/create-user.notification.body'));
    }
}
