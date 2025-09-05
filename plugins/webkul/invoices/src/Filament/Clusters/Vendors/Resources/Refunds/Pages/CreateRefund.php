<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\Refunds\Pages;

use Webkul\Account\Filament\Resources\Refunds\Pages\CreateRefund as BaseCreateRefund;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Refunds\RefundResource;

class CreateRefund extends BaseCreateRefund
{
    protected static string $resource = RefundResource::class;
}
