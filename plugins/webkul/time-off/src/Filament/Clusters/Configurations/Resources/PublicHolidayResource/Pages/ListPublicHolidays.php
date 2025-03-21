<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidayResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidayResource;

class ListPublicHolidays extends ListRecords
{
    protected static string $resource = PublicHolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('time-off::filament/clusters/configurations/resources/public-holiday/pages/list-public-holiday.header-actions.create.title'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/clusters/configurations/resources/public-holiday/pages/list-public-holiday.header-actions.create.notification.created.title'))
                        ->body(__('time-off::filament/clusters/configurations/resources/public-holiday/pages/list-public-holiday.header-actions.create.notification.created.body'))
                )
                ->mutateFormDataUsing(function ($data) {
                    $user = Auth::user();

                    $data['company_id'] = $user->default_company_id;
                    $data['creator_id'] = $user->id;

                    return $data;
                }),
        ];
    }
}
