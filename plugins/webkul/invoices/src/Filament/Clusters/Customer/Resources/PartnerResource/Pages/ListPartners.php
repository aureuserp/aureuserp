<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages;

use Filament\Actions;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ListVendors as BaseListPartners;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource;

class ListPartners extends BaseListPartners
{
    protected static string $resource = PartnerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Customer');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('New Customer'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
