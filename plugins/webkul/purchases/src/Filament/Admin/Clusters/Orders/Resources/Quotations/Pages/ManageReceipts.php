<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Quotations\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Pages\ManageReceipts as BaseManageReceipts;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Quotations\QuotationResource;

class ManageReceipts extends BaseManageReceipts
{
    protected static string $resource = QuotationResource::class;
}
