<?php

namespace Webkul\Contact\Filament\Resources\ContactBillResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\Contact\Filament\Resources\ContactBillResource;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = ContactBillResource::class;
}
