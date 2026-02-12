<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource\Pages;

use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\ViewCreditNote as BaseViewInvoice;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource;

class ViewCreditNote extends BaseViewInvoice
{
    protected static string $resource = CreditNoteResource::class;
}
