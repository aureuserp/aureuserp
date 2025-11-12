<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource;

class ViewTeam extends ViewRecord
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource),
            EditAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('sales::filament/clusters/configurations/resources/team/pages/view-team.header-actions.delete.notification.title'))
                        ->body(__('sales::filament/clusters/configurations/resources/team/pages/view-team.header-actions.delete.notification.body'))
                ),
        ];
    }
}
