<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPrices\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPrices\VendorPriceResource;

class ViewVendorPrice extends ViewRecord
{
    protected static string $resource = VendorPriceResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('purchases::filament/admin/clusters/configurations/resources/vendor-price/pages/view-vendor-price.navigation.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
