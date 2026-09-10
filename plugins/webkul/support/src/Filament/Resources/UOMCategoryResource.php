<?php

namespace Webkul\Support\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Pages\CreateUOMCategory;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Pages\EditUOMCategory;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Pages\ListUOMCategories;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Pages\ViewUOMCategory;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Schemas\UOMCategoryForm;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Tables\UOMCategoriesTable;
use Webkul\Support\Models\UOMCategory;

class UOMCategoryResource extends Resource
{
    protected static ?string $model = UOMCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/resources/uom-category.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return UOMCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UOMCategoriesTable::configure($table);
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
