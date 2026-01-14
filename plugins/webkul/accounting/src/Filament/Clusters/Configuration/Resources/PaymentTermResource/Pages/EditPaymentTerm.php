<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages;

use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\EditPaymentTerm as BaseEditPaymentTerm;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource;

class EditPaymentTerm extends BaseEditPaymentTerm
{
    protected static string $resource = PaymentTermResource::class;
}
