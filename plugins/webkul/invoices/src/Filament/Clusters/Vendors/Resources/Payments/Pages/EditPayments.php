<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\Payments\Pages;

use Webkul\Account\Filament\Resources\Payments\Pages\EditPayments as BaseEditPayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Payments\PaymentsResource;

class EditPayments extends BaseEditPayments
{
    protected static string $resource = PaymentsResource::class;
}
