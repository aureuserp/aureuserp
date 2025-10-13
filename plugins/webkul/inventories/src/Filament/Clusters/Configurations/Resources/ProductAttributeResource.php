<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributeResource\Pages\CreateProductAttribute;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributeResource\Pages\EditProductAttribute;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributeResource\Pages\ListProductAttributes;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributeResource\Pages\ViewProductAttribute;
use Webkul\Inventory\Models\Attribute;
use Webkul\Inventory\Settings\ProductSettings;
use Webkul\Product\Filament\Resources\AttributeResource;

class ProductAttributeResource extends AttributeResource
{
    protected static ?string $model = Attribute::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 9;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(ProductSettings::class)->enable_variants;
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/product-attribute.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/product-attribute.navigation.title');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProductAttributes::route('/'),
            'create' => CreateProductAttribute::route('/create'),
            'view'   => ViewProductAttribute::route('/{record}'),
            'edit'   => EditProductAttribute::route('/{record}/edit'),
        ];
    }
}
