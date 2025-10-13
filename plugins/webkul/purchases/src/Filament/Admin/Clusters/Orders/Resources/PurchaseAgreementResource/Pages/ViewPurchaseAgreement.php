<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Purchase\Enums\RequisitionState;
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
                ->setResource(static::$resource),
            DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->state == RequisitionState::CLOSED)
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/orders/resources/purchase-agreement/pages/view-purchase-agreement.header-actions.delete.notification.title'))
                        ->body(__('inventories::filament/clusters/orders/resources/purchase-agreement/pages/view-purchase-agreement.header-actions.delete.notification.body')),
                ),
        ];
    }
}
