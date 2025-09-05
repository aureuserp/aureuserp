<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Invoices\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Account\Filament\Resources\Invoices\Pages\EditInvoice as BaseEditInvoice;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Invoices\InvoiceResource;

class EditInvoice extends BaseEditInvoice
{
    protected static string $resource = InvoiceResource::class;

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/invoice.sub_navigation.edit-invoice');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
