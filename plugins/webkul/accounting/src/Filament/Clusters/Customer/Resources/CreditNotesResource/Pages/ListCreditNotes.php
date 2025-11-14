<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\CreditNotesResource\Pages;

use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\ListCreditNotes as BaseListInvoices;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CreditNotesResource;

class ListCreditNotes extends BaseListInvoices
{
    protected static string $resource = CreditNotesResource::class;
}
