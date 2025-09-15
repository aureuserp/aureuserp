<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediumResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediumResource;

class ListUTMMedia extends ListRecords
{
    protected static string $resource = UTMMediumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('recruitments::filament/clusters/configurations/resources/medium/pages/list-medium.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/configurations/resources/medium/pages/list-medium.header-actions.create.notification.title'))
                        ->body(__('recruitments::filament/clusters/configurations/resources/medium/pages/list-medium.header-actions.create.notification.body'))
                ),
        ];
    }
}
