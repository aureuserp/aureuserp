<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMSources\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMSources\UTMSourceResource;

class ListUTMSources extends ListRecords
{
    protected static string $resource = UTMSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('recruitments::filament/clusters/configurations/resources/source/pages/list-source.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/configurations/resources/source/pages/list-source.header-actions.create.notification.title'))
                        ->body(__('recruitments::filament/clusters/configurations/resources/source/pages/list-source.header-actions.create.notification.body'))
                ),
        ];
    }
}
