<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource;

class EditPackageType extends EditRecord
{
    protected static string $resource = PackageTypeResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/package-type/pages/edit-package-type.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/package-type/pages/edit-package-type.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/package-type/pages/edit-package-type.header-actions.delete.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/package-type/pages/edit-package-type.header-actions.delete.notification.body')),
                ),
        ];
    }
}
