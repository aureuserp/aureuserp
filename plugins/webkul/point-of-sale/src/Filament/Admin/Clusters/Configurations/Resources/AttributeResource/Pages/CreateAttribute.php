<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\AttributeResource\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\AttributeResource;
use Webkul\Product\Filament\Resources\AttributeResource\Pages\CreateAttribute as BaseCreateAttribute;

class CreateAttribute extends BaseCreateAttribute
{
    protected static string $resource = AttributeResource::class;

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
