<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages\ManageBills as BaseManageBills;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderBillResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource;

class ManageBills extends BaseManageBills
{
    protected static string $resource = PurchaseOrderResource::class;

    protected static ?string $relatedResource = PurchaseOrderBillResource::class;
}
