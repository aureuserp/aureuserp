<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\Pages;

use Webkul\Account\Filament\Resources\PaymentTerms\Pages\ListPaymentTerms as BaseListPaymentTerms;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\PaymentTermResource;

class ListPaymentTerms extends BaseListPaymentTerms
{
    protected static string $resource = PaymentTermResource::class;
}
