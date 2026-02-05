<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNoteResource\Pages;

use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\ListCreditNotes as BaseListInvoices;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNoteResource;

class ListCreditNotes extends BaseListInvoices
{
    protected static string $resource = CreditNoteResource::class;
}
