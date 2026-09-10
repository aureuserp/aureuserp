<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Maintenance\Filament\Clusters\Configurations;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ManageTeams;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\TeamResource\Schemas\TeamForm;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\TeamResource\Tables\TeamsTable;
use Webkul\Maintenance\Models\Team;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('maintenance::models/team.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/clusters/configurations/resources/team.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return TeamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeamsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTeams::route('/'),
        ];
    }
}
