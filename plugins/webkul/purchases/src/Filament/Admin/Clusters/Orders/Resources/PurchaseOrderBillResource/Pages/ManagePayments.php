<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderBillResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderBillResource;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = PurchaseOrderBillResource::class;
}
