<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\UOMCategoryResource\Pages\CreateUOMCategory;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\UOMCategoryResource\Pages\EditUOMCategory;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\UOMCategoryResource\Pages\ListUOMCategories;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\UOMCategoryResource\Pages\ViewUOMCategory;
use Webkul\Sale\Models\UOMCategory;
use Webkul\Support\Filament\Resources\UOMCategoryResource as BaseUOMCategoryResource;

class UOMCategoryResource extends BaseUOMCategoryResource
{
    protected static ?string $model = UOMCategory::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = Configuration::class;

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/uom-category.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('sales::filament/clusters/configurations/resources/uom-category.navigation.group');
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
