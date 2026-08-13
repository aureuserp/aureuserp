<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\UOMCategoryResource\Pages\CreateUOMCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\UOMCategoryResource\Pages\EditUOMCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\UOMCategoryResource\Pages\ListUOMCategories;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\UOMCategoryResource\Pages\ViewUOMCategory;
use Webkul\Inventory\Models\UOMCategory;
use Webkul\Support\Filament\Resources\UOMCategoryResource as BaseUOMCategoryResource;

class UOMCategoryResource extends BaseUOMCategoryResource
{
    protected static ?string $model = UOMCategory::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 11;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/uom-category.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/uom-category.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUOMCategories::route('/'),
            'create' => CreateUOMCategory::route('/create'),
            'view'   => ViewUOMCategory::route('/{record}'),
            'edit'   => EditUOMCategory::route('/{record}/edit'),
        ];
    }
}
