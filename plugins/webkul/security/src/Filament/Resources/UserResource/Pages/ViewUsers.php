<?php

namespace Webkul\Security\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Security\Filament\Resources\UserResource;

class ViewUsers extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        $getRecord = $this->record->role;

        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->hidden(fn ($record) => $record->trashed() || $record->hasRole('admin'))
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('security::filament/resources/user/pages/view-user.header-actions.delete.notification.title'))
                        ->body(__('security::filament/resources/user/pages/view-user.header-actions.delete.notification.body')),
                ),
        ];
    }
}
