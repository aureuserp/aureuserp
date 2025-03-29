<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource as BaseProductAttributeResource;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages;
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
            'index'  => Pages\ListProductAttributes::route('/'),
            'create' => Pages\CreateProductAttribute::route('/create'),
            'view'   => Pages\ViewProductAttribute::route('/{record}'),
            'edit'   => Pages\EditProductAttribute::route('/{record}/edit'),
        ];
    }
}
