<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\Taxes\Pages;

use Webkul\Account\Filament\Resources\Taxes\Pages\ManageDistributionForRefund as BaseManageDistributionForRefund;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\Taxes\TaxResource;

class ManageDistributionForRefund extends BaseManageDistributionForRefund
{
    protected static string $resource = TaxResource::class;
}
