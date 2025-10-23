<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\ViewPaymentTerm as BaseViewPaymentTerm;

class ViewPaymentTerm extends BaseViewPaymentTerm
{
    protected static string $resource = PaymentTermResource::class;
}
