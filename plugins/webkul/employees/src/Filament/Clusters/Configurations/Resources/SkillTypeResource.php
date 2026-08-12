<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\Pages\EditSkillType;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\Pages\ListSkillTypes;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\Pages\ViewSkillType;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\RelationManagers\SkillLevelRelationManager;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\RelationManagers\SkillsRelationManager;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\Schemas\SkillTypeForm;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\Schemas\SkillTypeInfolist;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\Tables\SkillTypesTable;
use Webkul\Employee\Models\SkillType;

class SkillTypeResource extends Resource
{
    protected static ?string $model = SkillType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Employee';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/skill-type.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/clusters/configurations/resources/skill-type.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/skill-type.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return SkillTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SkillTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SkillsRelationManager::class,
            SkillLevelRelationManager::class,
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return SkillTypeInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSkillTypes::route('/'),
            'view'  => ViewSkillType::route('/{record}'),
            'edit'  => EditSkillType::route('/{record}/edit'),
        ];
    }
}
