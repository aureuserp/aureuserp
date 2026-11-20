<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\PaymentResource\Pages;

use Webkul\Crm\Filament\Clusters\Sales\Resources\PaymentResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource\Pages\EditPayment as BaseEditPayment;

class EditPayment extends BaseEditPayment
{
    protected static string $resource = PaymentResource::class;
}
