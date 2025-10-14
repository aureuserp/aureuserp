<?php

namespace Webkul\Support\Filament\Resources\ActivityTypeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Support\Filament\Resources\ActivityTypeResource;

class ViewActivityType extends ViewRecord
{
    protected static string $resource = ActivityTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/activity-type/pages/view-activity-type.header-actions.delete.notification.title'))
                        ->body(__('support::filament/resources/activity-type/pages/view-activity-type.header-actions.delete.notification.body')),
                ),
        ];
    }
}
