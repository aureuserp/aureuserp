<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewWorkCenter extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = WorkCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('manufacturing::filament/clusters/configurations/resources/work-center/pages/view-work-center.header-actions.delete.notification.title'))
                        ->body(__('manufacturing::filament/clusters/configurations/resources/work-center/pages/view-work-center.header-actions.delete.notification.body')),
                ),
        ];
    }
}
