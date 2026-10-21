<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources;

use BackedEnum;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\AttributeResource\Pages\CreateAttribute;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\AttributeResource\Pages\EditAttribute;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\AttributeResource\Pages\ListAttributes;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\AttributeResource\Pages\ViewAttribute;
use Webkul\PointOfSale\Models\Attribute;
use Webkul\Product\Filament\Resources\AttributeResource as BaseAttributeResource;

class AttributeResource extends BaseAttributeResource
{
    protected static ?string $model = Attribute::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 7;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationGroup(): ?string
    {
        return __('point-of-sale::filament/admin/clusters/configurations.groups.products');
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/configurations/resources/attribute.navigation.title');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAttributes::route('/'),
            'create' => CreateAttribute::route('/create'),
            'view'   => ViewAttribute::route('/{record}'),
            'edit'   => EditAttribute::route('/{record}/edit'),
        ];
    }
}
