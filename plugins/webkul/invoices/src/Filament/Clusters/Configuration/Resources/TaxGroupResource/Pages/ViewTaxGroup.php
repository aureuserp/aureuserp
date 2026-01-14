<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroupResource\Pages;

use Webkul\Account\Filament\Resources\TaxGroupResource\Pages\ViewTaxGroup as BaseViewTaxGroup;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroupResource;

class ViewTaxGroup extends BaseViewTaxGroup
{
    protected static string $resource = TaxGroupResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
