<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\InvoiceOrderAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\RefundOrderAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions\RetryOrderPickingAction;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\PointOfSale\Models\Order;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditOrder extends EditRecord
{
    use HasRecordNavigationTabs, HasRepeaterColumnManager;

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

    /**
     * Payment lines added here change what the order is worth, so the totals are
     * rebuilt the same way the till rebuilds them.
     */
    protected function afterSave(): void
    {
        $record = $this->getRecord();

        if ($record instanceof Order) {
            PointOfSale::recomputeOrder($record);
        }
    }
}
