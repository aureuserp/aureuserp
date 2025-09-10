<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Partners\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Partners\PartnerResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages\EditVendor as BaseEditPartner;

class EditPartner extends BaseEditPartner
{
    protected static string $resource = PartnerResource::class;

     public function getTitle(): string|Htmlable
    {
        return __('invoices::filament/clusters/customers/resources/partner/pages/edit-partner.title');
    }

     public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/partner/pages/edit-partner.sub-navigation.edit-partner');
    }
}
