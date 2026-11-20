<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\PaymentResource\Pages;

use Webkul\Crm\Filament\Clusters\Sales\Resources\PaymentResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource\Pages\ViewPayment as BaseViewPayment;

class ViewPayment extends BaseViewPayment
{
    protected static string $resource = PaymentResource::class;
}
