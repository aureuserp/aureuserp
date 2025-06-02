<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource;

class ListDepartureReasons extends ListRecords
{
    protected static string $resource = DepartureReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->label(__('employees::filament/clusters/configurations/resources/departure-reason/pages/list-departure.header-actions.create.label'))
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('employees::filament/clusters/configurations/resources/departure-reason/pages/list-departure.header-actions.create.notification.title'))
                        ->body(__('employees::filament/clusters/configurations/resources/departure-reason/pages/list-departure.header-actions.create.notification.body')),
                )
                ->mutateFormDataUsing(function (array $data): array {
                    $data['reason_code'] = crc32($data['name']) % 100000;

                    return $data;
                }),
        ];
    }
}
