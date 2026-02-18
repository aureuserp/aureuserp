<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource\Pages;

use Webkul\Account\Filament\Resources\PaymentResource\Pages\EditPayment as BaseEditPayment;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource;

class EditPayment extends BaseEditPayment
{
    protected static string $resource = PaymentResource::class;
}
