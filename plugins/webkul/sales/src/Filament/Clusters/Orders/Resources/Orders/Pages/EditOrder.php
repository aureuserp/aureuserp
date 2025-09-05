<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Orders\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\Orders\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\EditQuotation as BaseEditOrder;

class EditOrder extends BaseEditOrder
{
    protected static string $resource = OrderResource::class;
}
