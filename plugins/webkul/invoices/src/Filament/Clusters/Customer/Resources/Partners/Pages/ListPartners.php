<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Partners\Pages;

use Filament\Actions\CreateAction;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Partners\PartnerResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages\ListVendors as BaseListPartners;

class ListPartners extends BaseListPartners
{
    protected static string $resource = PartnerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('invoices::filament/clusters/customers/resources/partner/pages/list-partner.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('invoices::filament/clusters/customers/resources/partner/pages/list-partner.header-actions.create.title'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
