<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotesResource\Pages;

use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\EditCreditNote as BaseCreditNote;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotesResource;

class EditCreditNotes extends BaseCreditNote
{
    protected static string $resource = CreditNotesResource::class;
}
