<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\CreateTeam;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\EditTeam;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\ListTeams;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\ViewTeam;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Schemas\TeamForm;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Schemas\TeamInfolist;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Tables\TeamsTable;
use Webkul\Sale\Models\Team;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $cluster = Configuration::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 9;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/team.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/team.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return TeamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeamsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TeamInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTeams::route('/'),
            'create' => CreateTeam::route('/create'),
            'view'   => ViewTeam::route('/{record}'),
            'edit'   => EditTeam::route('/{record}/edit'),
        ];
    }
}
