<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotes\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Account\Filament\Resources\CreditNotes\Pages\ViewCreditNote as BaseViewInvoice;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotes\CreditNotesResource;

class ViewCreditNote extends BaseViewInvoice
{
    protected static string $resource = CreditNotesResource::class;

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/credit-note.sub_navigation.view-credit-note');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
