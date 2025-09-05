<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Quotations\Pages;

use Filament\Actions;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Pages\ListOrders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Quotations\QuotationResource;

class ListQuotations extends ListOrders
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('purchases::filament/admin/clusters/orders/resources/quotation/pages/list-quotation.header-actions.create'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
