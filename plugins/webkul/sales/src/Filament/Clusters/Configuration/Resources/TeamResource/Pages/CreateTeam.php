<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('sales::filament/clusters/configurations/resources/team/pages/create-team.notification.title'))
            ->body(__('sales::filament/clusters/configurations/resources/team/pages/create-team.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $data['creator_id'] = $user->id;

        return $data;
    }
}
