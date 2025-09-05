<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams;

use BackedEnum;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams\Pages\CreateTeam;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams\Pages\EditTeam;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams\Pages\ListTeams;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams\Pages\ViewTeam;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams\Schemas\SaleTeamForm;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams\Schemas\SaleTeamInfolist;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams\Tables\SaleTeamsTable;
use Webkul\Sale\Models\Team;

class SaleTeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $cluster = Configuration::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'sales/teams';

    public static function getSlug(?Panel $panel = null): string
    {
        return static::$slug ?? parent::getSlug($panel);
    }

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
        return SaleTeamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SaleTeamsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SaleTeamInfolist::configure($schema);
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
