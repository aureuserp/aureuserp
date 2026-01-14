<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages;

use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\ListPaymentTerms as BaseListPaymentTerms;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource;

class ListPaymentTerms extends BaseListPaymentTerms
{
    protected static string $resource = PaymentTermResource::class;
}
