<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\EditBill as BaseEditBill;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource;

class EditBill extends BaseEditBill
{
    protected static string $resource = QuotationBillResource::class;
}
