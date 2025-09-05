<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypes;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypes\Pages\ListEmploymentTypes;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypes\Schemas\EmploymentTypeForm;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypes\Schemas\EmploymentTypeInfolist;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypes\Tables\EmploymentTypesTable;
use Webkul\Employee\Models\EmploymentType;

class EmploymentTypeResource extends Resource
{
    protected static ?string $model = EmploymentType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube-transparent';

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/employment-type.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/clusters/configurations/resources/employment-type.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/employment-type.navigation.title');
    }

    protected static ?string $cluster = Configurations::class;

    public static function form(Schema $schema): Schema
    {
        return EmploymentTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmploymentTypesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmploymentTypeInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmploymentTypes::route('/'),
        ];
    }
}
