<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource\Pages;

use Webkul\Account\Filament\Resources\RefundResource\Pages\ViewRefund as BaseViewRefund;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource;

class ViewRefund extends BaseViewRefund
{
    protected static string $resource = RefundResource::class;
}
