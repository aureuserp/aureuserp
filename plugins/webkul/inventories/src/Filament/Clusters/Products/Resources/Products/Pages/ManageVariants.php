<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Products\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\Products\ProductResource;
use Webkul\Inventory\Settings\ProductSettings;
use Webkul\Product\Filament\Resources\Products\Pages\ManageVariants as BaseManageVariants;

class ManageVariants extends BaseManageVariants
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
