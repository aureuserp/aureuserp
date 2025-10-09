<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\PaymentsResource\Pages;

use Webkul\Account\Filament\Resources\PaymentsResource\Pages\EditPayments as BaseEditPayments;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\PaymentsResource;

class EditPayments extends BaseEditPayments
{
    protected static string $resource = PaymentsResource::class;
}
