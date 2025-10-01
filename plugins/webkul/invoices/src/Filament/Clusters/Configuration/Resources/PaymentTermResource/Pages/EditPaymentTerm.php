<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages;

use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\EditPaymentTerm as BaseEditPaymentTerm;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTermResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditPaymentTerm extends BaseEditPaymentTerm
{
    use HasRecordNavigationTabs;

    protected static string $resource = PaymentTermResource::class;
}
