<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Actions\OpenTerminalAction;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditConfig extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            OpenTerminalAction::make(),

            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/configurations/resources/config/pages/edit-config.header-actions.delete.notification.success.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/configurations/resources/config/pages/edit-config.header-actions.delete.notification.success.body')),
                ),
        ];
    }

    protected function afterSave(): void
    {
        $this->getRecord()->syncRestaurantFloors();
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('point-of-sale::filament/admin/clusters/configurations/resources/config/pages/edit-config.notification.success.title'))
            ->body(__('point-of-sale::filament/admin/clusters/configurations/resources/config/pages/edit-config.notification.success.body'));
    }
}
