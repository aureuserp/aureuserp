<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderBillResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderBillResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource\Pages\ManagePayments as BaseManagePayments;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = PurchaseOrderBillResource::class;
}
