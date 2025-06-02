<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource;

class EditTeam extends EditRecord
{
    protected static string $resource = TeamResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('sales::filament/clusters/configurations/resources/team/pages/edit-team.notification.title'))
            ->body(__('sales::filament/clusters/configurations/resources/team/pages/edit-team.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->setResource(static::$resource),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('sales::filament/clusters/configurations/resources/team/pages/edit-team.header-actions.delete.notification.title'))
                        ->body(__('sales::filament/clusters/configurations/resources/team/pages/edit-team.header-actions.delete.notification.body'))
                ),
        ];
    }
}
