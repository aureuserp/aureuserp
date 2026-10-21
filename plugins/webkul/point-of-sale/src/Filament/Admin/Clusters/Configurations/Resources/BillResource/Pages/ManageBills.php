<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\BillResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\BillResource;

class ManageBills extends ManageRecords
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/bill/pages/manage-bills.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/configurations/resources/bill/pages/manage-bills.header-actions.create.notification.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/configurations/resources/bill/pages/manage-bills.header-actions.create.notification.body')),
                ),
        ];
    }
}
