<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewPurchaseAgreement extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = PurchaseAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/edit-purchase-agreement.header-actions.delete.notification.title'))
                        ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/edit-purchase-agreement.header-actions.delete.notification.body')),
                ),
        ];
    }
}
