<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource\Pages;

use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\CreateCreditNote as BaseCreateInvoice;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource;

class CreateCreditNote extends BaseCreateInvoice
{
    protected static string $resource = CreditNoteResource::class;
}
