<?php

namespace Webkul\Security\Filament\Resources\TeamResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Security\Filament\Resources\TeamResource;

class ManageTeams extends ManageRecords
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-o-plus-circle')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['creator_id'] = filament()->auth()->user()->id;

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('security::filament/resources/team/pages/manage-team.header-actions.create.notification.title'))
                        ->body(__('security::filament/resources/team/pages/manage-team.header-actions.create.notification.body'))
                ),
        ];
    }
}
