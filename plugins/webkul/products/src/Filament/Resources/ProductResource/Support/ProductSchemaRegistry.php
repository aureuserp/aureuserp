<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Support;

use Webkul\Support\Filament\Contributions\AbstractSchemaRegistry;

class ProductSchemaRegistry extends AbstractSchemaRegistry
{
    protected static function scope(): string
    {
        return 'product';
    }
}
