<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Partners\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Partners\PartnerResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages\ViewVendor as BaseViewPartner;

class ViewPartner extends BaseViewPartner
{
    protected static string $resource = PartnerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('invoices::filament/clusters/customers/resources/partner/pages/view-partner.title');
    }
     public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/partner/pages/view-partner.sub-navigation.view-partner');
    }
}
