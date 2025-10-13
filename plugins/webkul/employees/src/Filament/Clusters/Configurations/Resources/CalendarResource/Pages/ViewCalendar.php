<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource;

class ViewCalendar extends ViewRecord
{
    protected static string $resource = CalendarResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('employees::filament/clusters/configurations/resources/activity-plan/pages/view-activity-plan.header-actions.delete.notification.title'))
                        ->body(__('employees::filament/clusters/configurations/resources/activity-plan/pages/view-activity-plan.header-actions.delete.notification.body')),
                ),
        ];
    }
}
