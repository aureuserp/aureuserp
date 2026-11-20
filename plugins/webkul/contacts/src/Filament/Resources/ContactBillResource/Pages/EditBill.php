<?php

namespace Webkul\Contact\Filament\Resources\ContactBillResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill as BaseEditBill;
use Webkul\Contact\Filament\Resources\ContactBillResource;

class EditBill extends BaseEditBill
{
    protected static string $resource = ContactBillResource::class;
}
