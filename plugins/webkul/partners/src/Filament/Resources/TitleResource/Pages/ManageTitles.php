<?php

namespace Webkul\Partner\Filament\Resources\TitleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Partner\Filament\Resources\TitleResource;

class ManageTitles extends ManageRecords
{
    protected static string $resource = TitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('partners::filament/resources/title/pages/manage-titles.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('partners::filament/resources/title/pages/manage-titles.header-actions.create.notification.title'))
                        ->body(__('partners::filament/resources/title/pages/manage-titles.header-actions.create.notification.body')),
                ),
        ];
    }
}
