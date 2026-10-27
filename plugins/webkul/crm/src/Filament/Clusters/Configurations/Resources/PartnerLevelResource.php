<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\PartnerLevelResource\Pages\ManagePartnerLevels;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\PartnerLevelResource\Schemas\PartnerLevelForm;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\PartnerLevelResource\Tables\PartnerLevelsTable;
use Webkul\Crm\Models\PartnerLevel;

class PartnerLevelResource extends Resource
{
    protected static ?string $model = PartnerLevel::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/partner-level.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/partner-level.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/partner-level.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/partner-level.navigation.group');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function form(Schema $schema): Schema
    {
        return PartnerLevelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartnerLevelsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePartnerLevels::route('/'),
        ];
    }
}
