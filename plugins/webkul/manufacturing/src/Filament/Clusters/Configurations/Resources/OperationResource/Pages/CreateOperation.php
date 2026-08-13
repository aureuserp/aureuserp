<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource;

class CreateOperation extends CreateRecord
{
    protected static string $resource = OperationResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('manufacturing::filament/clusters/configurations/resources/operation/pages/create-operation.notification.title'))
            ->body(__('manufacturing::filament/clusters/configurations/resources/operation/pages/create-operation.notification.body'));
    }
}
