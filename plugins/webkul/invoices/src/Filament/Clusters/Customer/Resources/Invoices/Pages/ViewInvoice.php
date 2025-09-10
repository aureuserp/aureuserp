<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Invoices\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Account\Filament\Resources\Invoices\Pages\ViewInvoice as BaseViewInvoice;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Invoices\InvoiceResource;

class ViewInvoice extends BaseViewInvoice
{
    protected static string $resource = InvoiceResource::class;

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/invoice.sub_navigation.view-invoice');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
