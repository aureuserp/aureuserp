<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DegreeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DegreeResource;

class ListDegrees extends ListRecords
{
    protected static string $resource = DegreeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/configurations/resources/degree/pages/list-degree.notification.title'))
                        ->body(__('recruitments::filament/clusters/configurations/resources/degree/pages/list-degree.notification.body'))
                ),
        ];
    }
}
