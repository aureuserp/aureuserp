<?php

namespace Webkul\Product\Filament\Resources\Products;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Product\Filament\Resources\Products\Schemas\ProductForm;
use Webkul\Product\Filament\Resources\Products\Schemas\ProductInfolist;
use Webkul\Product\Filament\Resources\Products\Tables\ProductsTable;
use Webkul\Product\Models\Product;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }
}
