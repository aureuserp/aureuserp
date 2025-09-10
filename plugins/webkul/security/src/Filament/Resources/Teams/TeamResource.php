<?php

namespace Webkul\Security\Filament\Resources\Teams;

use BackedEnum;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Security\Filament\Resources\Teams\Pages\ManageTeams;
use Webkul\Security\Filament\Resources\Teams\Schemas\TeamForm;
use Webkul\Security\Filament\Resources\Teams\Schemas\TeamInfolist;
use Webkul\Security\Filament\Resources\Teams\Tables\TeamsTable;
use Webkul\Security\Models\Team;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'teams';

    public static function getSlug(?Panel $panel = null): string
    {
        return static::$slug ?? parent::getSlug($panel);
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/resources/team.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('security::filament/resources/team.navigation.group');
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
            'index' => ManageTeams::route('/'),
        ];
    }
}
