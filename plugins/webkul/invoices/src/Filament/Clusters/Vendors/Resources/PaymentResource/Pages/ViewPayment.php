<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource\Pages;

use Webkul\Account\Filament\Resources\PaymentResource\Pages\ViewPayment as BaseViewPayment;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource;

class ViewPayment extends BaseViewPayment
{
    protected static string $resource = PaymentResource::class;
}
