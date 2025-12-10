<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ManageAttributes as BaseManageAttributes;
use Webkul\Product\Settings\ProductSettings;

class ManageAttributes extends BaseManageAttributes
{
    protected static string $resource = ProductResource::class;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function canAccess(array $parameters = []): bool
    {
        $canAccess = parent::canAccess($parameters);

        if (! $canAccess) {
            return false;
        }

        return app(ProductSettings::class)->enable_variants;
    }
}
