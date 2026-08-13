<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\StageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\StageResource;

class ManageStages extends ManageRecords
{
    protected static string $resource = StageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('maintenance::filament/clusters/configurations/resources/stage/pages/manage-stages.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('maintenance::filament/clusters/configurations/resources/stage/pages/manage-stages.header-actions.create.notification.title'))
                        ->body(__('maintenance::filament/clusters/configurations/resources/stage/pages/manage-stages.header-actions.create.notification.body')),
                ),
        ];
    }
}
