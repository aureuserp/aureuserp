<?php

namespace Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkills;

use BackedEnum;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Clusters\Reportings;
use Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkills\Pages\ListEmployeeSkills;
use Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkills\Schemas\EmployeeSkillInfolist;
use Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkills\Tables\EmployeeSkillsTable;
use Webkul\Employee\Models\EmployeeSkill;

class EmployeeSkillResource extends Resource
{
    protected static ?string $model = EmployeeSkill::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $pluralModelLabel = 'Skills';

    protected static ?string $cluster = Reportings::class;

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/reportings/resources/employee-skill.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/reportings/resources/employee-skill.navigation.title');
    }

    public static function table(Table $table): Table
    {
        return EmployeeSkillsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmployeeSkillInfolist::configure($schema);
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'employees/skills';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployeeSkills::route('/'),
        ];
    }
}
