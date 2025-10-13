<?php

namespace Webkul\Support\Filament\Resources\ActivityTypeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Support\Filament\Resources\ActivityTypeResource;

class EditActivityType extends EditRecord
{
    protected static string $resource = ActivityTypeResource::class;

    protected static ?string $pluginName = 'support';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected static function getPluginName()
    {
        return static::$pluginName;
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('support::filament/resources/activity-type/pages/edit-activity-type.notification.title'))
            ->body(__('support::filament/resources/activity-type/pages/edit-activity-type.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/activity-type/pages/edit-activity-type.header-actions.delete.notification.title'))
                        ->body(__('support::filament/resources/activity-type/pages/edit-activity-type.header-actions.delete.notification.body')),
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['plugin'] = static::getPluginName();

        return $data;
    }
}
