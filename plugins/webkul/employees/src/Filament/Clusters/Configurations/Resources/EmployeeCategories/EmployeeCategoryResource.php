<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategories;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategories\Pages\ListEmployeeCategories;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategories\Schemas\EmployeeCategoryForm;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategories\Schemas\EmployeeCategoryInfolist;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategories\Tables\EmployeeCategoriesTable;
use Webkul\Employee\Models\EmployeeCategory;

class EmployeeCategoryResource extends Resource
{
    protected static ?string $model = EmployeeCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/employee-category.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/clusters/configurations/resources/employee-category.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/employee-category.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return EmployeeCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeCategoriesTable::configuration($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmployeeCategoryInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployeeCategories::route('/'),
        ];
    }
}
