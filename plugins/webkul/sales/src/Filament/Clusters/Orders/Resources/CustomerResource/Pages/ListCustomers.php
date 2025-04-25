<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Filament\Actions;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\ListPartners as BaseListCustomers;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;

class ListCustomers extends BaseListCustomers
{
    protected static string $resource = CustomerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('sales::filament/clusters/orders/resources/customer/pages/list-customers.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('sales::filament/clusters/orders/resources/customer/pages/list-customers.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
