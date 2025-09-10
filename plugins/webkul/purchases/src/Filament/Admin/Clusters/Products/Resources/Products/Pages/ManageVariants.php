<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\Products\Pages;

use Webkul\Product\Filament\Resources\Products\Pages\ManageVariants as BaseManageVariants;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\Products\ProductResource;
use Webkul\Purchase\Settings\ProductSettings;

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
