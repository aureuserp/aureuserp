<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProductResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProductResource;

class EditOrderTemplateProduct extends EditRecord
{
    protected static string $resource = OrderTemplateProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('sales::filament/clusters/configurations/resources/order-template/pages/edit-order-template.notification.title'))
            ->body(__('sales::filament/clusters/configurations/resource/order-template/pages/edit-order-template.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon('heroicon-o-pencil-square'),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('sales::filament/clusters/configurations/resources/order-template/pages/edit-order-template.header-actions.notification.delete.title'))
                        ->body(__('sales::filament/clusters/configurations/resource/order-template/pages/edit-order-template.header-actions.notification.delete.body'))
                ),
        ];
    }
}
