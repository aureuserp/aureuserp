<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotes\Pages;

use Webkul\Account\Filament\Resources\CreditNotes\Pages\ListCreditNotes as BaseListInvoices;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotes\CreditNotesResource;

class ListCreditNotes extends BaseListInvoices
{
    protected static string $resource = CreditNotesResource::class;
}
