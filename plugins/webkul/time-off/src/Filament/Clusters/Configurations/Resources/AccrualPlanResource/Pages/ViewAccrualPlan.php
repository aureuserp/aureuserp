<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\ViewRecord;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource;

class ViewAccrualPlan extends ViewRecord
{
    protected static string $resource = AccrualPlanResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/clusters/configurations/resources/leave-type/pages/view-leave-type.header-actions.delete.notification.title'))
                        ->body(__('time-off::filament/clusters/configurations/resources/leave-type/pages/view-leave-type.header-actions.delete.notification.body'))
                ),
        ];
    }
}
