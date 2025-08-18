<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource;

class ViewStages extends ViewRecord
{
    protected static string $resource = StageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/configurations/resources/stage/pages/view-stage.header-actions.delete.notification.title'))
                        ->body(__('recruitments::filament/clusters/configurations/resources/stage/pages/view-stage.header-actions.delete.notification.body'))
                ),
        ];
    }
}
