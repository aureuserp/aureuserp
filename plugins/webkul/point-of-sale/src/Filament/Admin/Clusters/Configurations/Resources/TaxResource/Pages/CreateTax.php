<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\TaxResource\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Account\Filament\Resources\TaxResource\Pages\CreateTax as BaseCreateTax;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\TaxResource;

class CreateTax extends BaseCreateTax
{
    protected static string $resource = TaxResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
