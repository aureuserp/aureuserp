<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\Payments\Pages;

use Webkul\Account\Filament\Resources\Payments\Pages\ViewPayments as BaseViewPayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Payments\PaymentsResource;

class ViewPayments extends BaseViewPayments
{
    protected static string $resource = PaymentsResource::class;
}
