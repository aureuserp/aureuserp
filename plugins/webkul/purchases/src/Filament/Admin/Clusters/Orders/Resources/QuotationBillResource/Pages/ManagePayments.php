<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = QuotationBillResource::class;
}
