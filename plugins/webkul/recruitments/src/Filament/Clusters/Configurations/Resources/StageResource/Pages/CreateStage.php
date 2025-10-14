<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource;

class CreateStage extends CreateRecord
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

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('recruitments::filament/clusters/configurations/resources/stage/pages/create-stage.notification.title'))
            ->body(__('recruitments::filament/clusters/configurations/resources/stage/pages/create-stage.notification.body'));
    }
}
