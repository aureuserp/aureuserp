<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages;

use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\ViewPaymentTerm as BaseViewPaymentTerm;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTermResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewPaymentTerm extends BaseViewPaymentTerm
{
    use HasRecordNavigationTabs;

    protected static string $resource = PaymentTermResource::class;
}
