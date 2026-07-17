<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Product\Filament\Resources\ProductResource\Pages\ManageAttributes as BaseManageAttributes;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource;
use Webkul\Purchase\Settings\ProductSettings;

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

        return settings(ProductSettings::class)->enable_variants;
    }
}
