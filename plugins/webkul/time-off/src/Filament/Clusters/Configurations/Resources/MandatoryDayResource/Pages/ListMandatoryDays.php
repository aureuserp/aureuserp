<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDayResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDayResource;

class ListMandatoryDays extends ListRecords
{
    protected static string $resource = MandatoryDayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days/pages/list-mandatory-days.header-actions.create.title'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/clusters/configurations/resources/mandatory-days/pages/list-mandatory-days.header-actions.create.notification.created.title'))
                        ->body(__('time-off::filament/clusters/configurations/resources/mandatory-days/pages/list-mandatory-days.header-actions.create.notification.created.body'))
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
