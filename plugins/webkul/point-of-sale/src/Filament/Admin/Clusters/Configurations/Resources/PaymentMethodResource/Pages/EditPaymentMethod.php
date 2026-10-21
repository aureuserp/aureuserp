<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditPaymentMethod extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(fn (DeleteAction $action) => PaymentMethodResource::haltWhenInOpenSession($action, [$this->getRecord()]))
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/edit-payment-method.header-actions.delete.notification.success.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/edit-payment-method.header-actions.delete.notification.success.body')),
                ),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/edit-payment-method.notification.success.title'))
            ->body(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/edit-payment-method.notification.success.body'));
    }
}
