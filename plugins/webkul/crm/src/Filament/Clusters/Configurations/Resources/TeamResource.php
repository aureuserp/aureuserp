<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\CreateTeam;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\EditTeam;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ListTeams;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ViewTeam;
use Webkul\Crm\Filament\Concerns\HasCrmAuthorization;
use Webkul\Crm\Policies\TeamPolicy;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource as BaseTeamResource;

class TeamResource extends BaseTeamResource
{
    use HasCrmAuthorization;

    protected static ?string $cluster = Configurations::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/team.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/team.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/team.navigation.title');
    }

    public static function getCrmPolicy(): string
    {
        return TeamPolicy::class;
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
