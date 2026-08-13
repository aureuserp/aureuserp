<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages\ManageReceipts as BaseManageReceipts;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderReceiptResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource;

class ManageReceipts extends BaseManageReceipts
{
    protected static string $resource = PurchaseOrderResource::class;

    protected static ?string $relatedResource = PurchaseOrderReceiptResource::class;
}
