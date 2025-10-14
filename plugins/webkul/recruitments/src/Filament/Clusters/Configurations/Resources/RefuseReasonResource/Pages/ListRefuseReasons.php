<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasonResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasonResource;

class ListRefuseReasons extends ListRecords
{
    protected static string $resource = RefuseReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/configurations/resources/refuse-reason/pages/list-refuse-reasons.notification.title'))
                        ->body(__('recruitments::filament/clusters/configurations/resources/refuse-reason/pages/list-refuse-reasons.notification.body'))
                ),
        ];
    }
}
