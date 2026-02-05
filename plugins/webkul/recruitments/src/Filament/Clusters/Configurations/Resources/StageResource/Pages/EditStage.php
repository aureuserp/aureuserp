<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource;

class EditStage extends EditRecord
{
    protected static string $resource = StageResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('recruitments::filament/clusters/configurations/resources/stage/pages/edit-stage.notification.title'))
            ->body(__('recruitments::filament/clusters/configurations/resources/stage/pages/edit-stage.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('recruitments::filament/clusters/configurations/resources/stage/pages/edit-stage.header-actions.delete.notification.title'))
                        ->body(__('recruitments::filament/clusters/configurations/resources/stage/pages/edit-stage.header-actions.delete.notification.body'))
                ),
        ];
    }
}
