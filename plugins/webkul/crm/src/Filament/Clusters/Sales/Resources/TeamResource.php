<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages\ListTeams;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages\ManageTeamOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Tables\TeamsTable;
use Webkul\Crm\Filament\Concerns\HasCrmAuthorization;
use Webkul\Crm\Policies\TeamPolicy;
use Webkul\Sale\Models\Team;

/**
 * Pipeline overview of the sales teams managed under CRM → Configuration → Sales Teams.
 * It shares the `*_crm_team` permissions with that screen.
 */
class TeamResource extends Resource
{
    use HasCrmAuthorization;

    protected static ?string $model = Team::class;

    protected static ?string $slug = 'teams';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = Sales::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function getCrmPolicy(): string
    {
        return TeamPolicy::class;
    }

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/team.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/team.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['opportunities as open_opportunities_count' => fn (Builder $query): Builder => $query->opportunities()->open()])
            ->withSum(['opportunities as open_expected_revenue' => fn (Builder $query): Builder => $query->opportunities()->open()], 'expected_revenue');
    }

    public static function table(Table $table): Table
    {
        return TeamsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'         => ListTeams::route('/'),
            'opportunities' => ManageTeamOpportunities::route('/{record}/opportunities'),
        ];
    }
}
