<?php

namespace Webkul\Contact\Filament\Resources\ContactInvoiceResource\Pages;

use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\Contact\Filament\Resources\ContactInvoiceResource;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = ContactInvoiceResource::class;
}
