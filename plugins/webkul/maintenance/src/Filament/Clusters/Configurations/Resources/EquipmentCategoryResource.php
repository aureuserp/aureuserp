<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Maintenance\Filament\Clusters\Configurations;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Pages\CreateEquipmentCategory;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Pages\EditEquipmentCategory;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Pages\ListEquipmentCategories;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Pages\ViewEquipmentCategory;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Schemas\EquipmentCategoryForm;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Schemas\EquipmentCategoryInfolist;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Tables\EquipmentCategoriesTable;
use Webkul\Maintenance\Models\EquipmentCategory;

class EquipmentCategoryResource extends Resource
{
    protected static ?string $model = EquipmentCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('maintenance::models/equipment-category.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/clusters/configurations/resources/equipment-category.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return EquipmentCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EquipmentCategoriesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EquipmentCategoryInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEquipmentCategories::route('/'),
            'create' => CreateEquipmentCategory::route('/create'),
            'view'   => ViewEquipmentCategory::route('/{record}'),
            'edit'   => EditEquipmentCategory::route('/{record}/edit'),
        ];
    }
}
