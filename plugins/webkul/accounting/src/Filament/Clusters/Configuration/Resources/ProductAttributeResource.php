<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\CreateProductAttribute;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\EditProductAttribute;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\ListProductAttributes;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\ViewProductAttribute;
use Webkul\Accounting\Models\Attribute;
use Webkul\Product\Filament\Resources\AttributeResource;

class ProductAttributeResource extends AttributeResource
{
    protected static ?string $model = Attribute::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = Configuration::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string
    {
        return __('accounting::filament/clusters/configurations/resources/product-attribute.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/product-attribute.navigation.title');
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
