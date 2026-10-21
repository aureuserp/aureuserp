<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\InvoiceOrderAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\RefundOrderAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\RetryOrderPickingAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewOrder extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->activityPlans($this->getRecord()->activityPlans())
                ->resource($this->getResource()),
            InvoiceOrderAction::make(),
            RefundOrderAction::make(),
            RetryOrderPickingAction::make(),
        ];
    }
}
