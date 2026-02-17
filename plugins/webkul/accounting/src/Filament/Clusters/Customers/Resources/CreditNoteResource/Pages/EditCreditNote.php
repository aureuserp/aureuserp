<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource\Pages;

use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\EditCreditNote as BaseCreditNote;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource;

class EditCreditNote extends BaseCreditNote
{
    protected static string $resource = CreditNoteResource::class;
}
