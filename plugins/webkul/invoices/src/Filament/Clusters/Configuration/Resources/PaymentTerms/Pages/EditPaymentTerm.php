<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\Pages;

use Webkul\Account\Filament\Resources\PaymentTerms\Pages\EditPaymentTerm as BaseEditPaymentTerm;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\PaymentTermResource;

class EditPaymentTerm extends BaseEditPaymentTerm
{
    protected static string $resource = PaymentTermResource::class;
}
