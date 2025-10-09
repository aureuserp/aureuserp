<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\CreditNotesResource\Pages;

use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\ViewCreditNote as BaseViewInvoice;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CreditNotesResource;

class ViewCreditNote extends BaseViewInvoice
{
    protected static string $resource = CreditNotesResource::class;
}
