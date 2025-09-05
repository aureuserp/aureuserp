<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\Taxes\Pages;

use Webkul\Account\Filament\Resources\Taxes\Pages\ManageDistributionForInvoice as BaseManageDistributionForInvoice;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\Taxes\TaxResource;

class ManageDistributionForInvoice extends BaseManageDistributionForInvoice
{
    protected static string $resource = TaxResource::class;
}
