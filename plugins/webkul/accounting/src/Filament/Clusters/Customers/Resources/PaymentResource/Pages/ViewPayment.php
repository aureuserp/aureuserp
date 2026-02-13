<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource\Pages;

use Webkul\Account\Filament\Resources\PaymentResource\Pages\ViewPayment as BaseViewPayment;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource;

class ViewPayment extends BaseViewPayment
{
    protected static string $resource = PaymentResource::class;
}
