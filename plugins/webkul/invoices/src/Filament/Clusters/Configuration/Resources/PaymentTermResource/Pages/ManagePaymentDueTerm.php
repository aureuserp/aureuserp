<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages;

use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\ManagePaymentDueTerm as BaseManagePaymentDueTerm;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTermResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManagePaymentDueTerm extends BaseManagePaymentDueTerm
{
    use HasRecordNavigationTabs;

    protected static string $resource = PaymentTermResource::class;
}
