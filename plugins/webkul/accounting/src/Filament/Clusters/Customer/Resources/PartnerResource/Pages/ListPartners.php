<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\PartnerResource\Pages;

use Filament\Actions\CreateAction;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\PartnerResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ListVendors as BaseListPartners;

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
            CreateAction::make()
                ->label(__('New Customer'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
