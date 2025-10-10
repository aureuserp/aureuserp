<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\CreditNotesResource\Pages;

use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\CreateCreditNote as BaseCreateInvoice;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CreditNotesResource;

class CreateCreditNotes extends BaseCreateInvoice
{
    protected static string $resource = CreditNotesResource::class;
}
