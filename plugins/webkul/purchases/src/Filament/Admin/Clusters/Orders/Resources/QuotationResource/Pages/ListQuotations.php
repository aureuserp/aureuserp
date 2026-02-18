<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource\Pages;

use Filament\Actions\CreateAction;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages\ListOrders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource;

class ListQuotations extends ListOrders
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('purchases::filament/admin/clusters/orders/resources/quotation/pages/list-quotation.header-actions.create'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
