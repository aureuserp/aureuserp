<?php

namespace Webkul\Contact\Filament\Resources\ContactBillResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\ViewBill as BaseViewBill;
use Webkul\Contact\Filament\Resources\ContactBillResource;

class ViewBill extends BaseViewBill
{
    protected static string $resource = ContactBillResource::class;
}
