<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources\Products\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Products\Pages\ManageVariants as BaseManageVariants;
use Webkul\Sale\Filament\Clusters\Products\Resources\Products\ProductResource;
use Webkul\Sale\Settings\ProductSettings;

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
