<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages;

use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\ViewPaymentTerm as BaseViewPaymentTerm;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource;

class ViewPaymentTerm extends BaseViewPaymentTerm
{
    protected static string $resource = PaymentTermResource::class;
}
