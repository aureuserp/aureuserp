<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentsResource\Pages;

use Webkul\Account\Filament\Resources\PaymentsResource\Pages\ViewPayments as BaseViewPayments;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentsResource;

class ViewPayments extends BaseViewPayments
{
    protected static string $resource = PaymentsResource::class;
}
