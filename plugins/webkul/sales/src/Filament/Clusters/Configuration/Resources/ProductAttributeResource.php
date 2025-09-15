<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource as BaseProductAttributeResource;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\CreateProductAttribute;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\EditProductAttribute;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\ListProductAttributes;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\ViewProductAttribute;
use Webkul\Sale\Models\Attribute;

class ProductAttributeResource extends BaseProductAttributeResource
{
    protected static ?string $model = Attribute::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 9;

    protected static ?string $cluster = Configuration::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string
    {
        return __('sales::filament/clusters/configurations/resources/product-attribute.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/product-attribute.navigation.title');
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
