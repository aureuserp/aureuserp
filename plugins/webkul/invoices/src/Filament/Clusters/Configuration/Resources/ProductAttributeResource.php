<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources;

use Webkul\Product\Filament\Resources\AttributeResource;
use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages;
use Webkul\Invoice\Models\Attribute;

class ProductAttributeResource extends AttributeResource
{
    protected static ?string $model = Attribute::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 9;

    protected static ?string $cluster = Configuration::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string
    {
        return __('invoices::filament/clusters/configurations/resources/product-attribute.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/configurations/resources/product-attribute.navigation.title');
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
