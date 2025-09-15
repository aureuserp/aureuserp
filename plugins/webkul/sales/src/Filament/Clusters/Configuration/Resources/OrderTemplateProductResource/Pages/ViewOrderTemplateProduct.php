<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProductResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProductResource;

class ViewOrderTemplateProduct extends ViewRecord
{
    protected static string $resource = OrderTemplateProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('sales::filament/clusters/configurations/resources/order-template/pages/view-order-template.header-actions.notification.delete.title'))
                        ->body(__('sales::filament/clusters/configurations/resource/order-template/pages/view-order-template.header-actions.notification.delete.body'))
                ),
        ];
    }
}
