<?php

namespace Webkul\Asset\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Filament\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Asset\Filament\Resources\ProductResource\Pages\EditProduct;
use Webkul\Asset\Filament\Resources\ProductResource\Pages\ListProducts;
use Webkul\Asset\Filament\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Asset\Filament\Resources\ProductResource\RelationManagers\ProductAssetsRelationManager;
use Webkul\Asset\Filament\Resources\ProductResource\Schemas\ProductForm;
use Webkul\Asset\Filament\Resources\ProductResource\Schemas\ProductInfolist;
use Webkul\Asset\Filament\Resources\ProductResource\Tables\ProductsTable;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Support\Enums\NavigationGroup;

class ProductResource extends Resource
{
    protected static ?string $model = AssetProduct::class;

    protected static ?string $slug = 'assets/products';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::Assets;
    }

    public static function getNavigationLabel(): string
    {
        return __('assets::filament/resources/product.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('assets::filament/resources/product.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('assets::filament/resources/product.plural-label');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'category',
            'company',
        ]);
    }

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

    public static function getRelations(): array
    {
        return [
            ProductAssetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view'   => ViewProduct::route('/{record}'),
            'edit'   => EditProduct::route('/{record}/edit'),
        ];
    }
}
