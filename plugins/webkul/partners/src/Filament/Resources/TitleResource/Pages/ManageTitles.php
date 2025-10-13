<?php

namespace Webkul\Partner\Filament\Resources\TitleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
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
                ->mutateDataUsing(function (array $data): array {
                    $data['creator_id'] = Auth::id();

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('partners::filament/resources/title/pages/manage-titles.header-actions.create.notification.title'))
                        ->body(__('partners::filament/resources/title/pages/manage-titles.header-actions.create.notification.body')),
                ),
        ];
    }
}
