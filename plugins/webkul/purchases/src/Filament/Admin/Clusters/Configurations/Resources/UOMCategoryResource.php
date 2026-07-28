<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources;

use Webkul\Purchase\Filament\Admin\Clusters\Configurations;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\UOMCategoryResource\Pages\CreateUOMCategory;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\UOMCategoryResource\Pages\EditUOMCategory;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\UOMCategoryResource\Pages\ListUOMCategories;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\UOMCategoryResource\Pages\ViewUOMCategory;
use Webkul\Purchase\Models\UOMCategory;
use Webkul\Support\Filament\Resources\UOMCategoryResource as BaseUOMCategoryResource;

class UOMCategoryResource extends BaseUOMCategoryResource
{
    protected static ?string $model = UOMCategory::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 11;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/configurations/resources/uom-category.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('purchases::filament/admin/clusters/configurations/resources/uom-category.navigation.group');
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
