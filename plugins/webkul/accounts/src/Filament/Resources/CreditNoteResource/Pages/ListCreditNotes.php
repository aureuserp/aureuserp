<?php

namespace Webkul\Account\Filament\Resources\CreditNoteResource\Pages;

use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices as ListRecords;

class ListCreditNotes extends ListRecords
{
    protected static string $resource = CreditNoteResource::class;
}
