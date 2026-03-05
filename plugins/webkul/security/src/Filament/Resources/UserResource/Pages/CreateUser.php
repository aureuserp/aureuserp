<?php

namespace Webkul\Security\Filament\Resources\UserResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        UserResource::ensureAdminRoleConstraints(null, $data['roles'] ?? []);

        return $data;
    }

    protected function beforeCreate(): void
    {
        $rawState = $this->form->getRawState();

        try {
            UserResource::ensureAdminRoleConstraints(
                null,
                (array) ($rawState['roles'] ?? $this->data['roles'] ?? [])
            );
        } catch (ValidationException $exception) {
            Notification::make()
                ->danger()
                ->title(__('security::filament/resources/user.form.sections.permissions.fields.roles'))
                ->body(implode("\n", $exception->errors()['roles'] ?? []))
                ->send();

            $this->halt();
        }
    }
}
