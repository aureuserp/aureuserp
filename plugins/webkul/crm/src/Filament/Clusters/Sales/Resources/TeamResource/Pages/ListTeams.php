<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource as ConfigurationTeamResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Tables\TeamsTable;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Filament\Concerns\HasViewLayouts;
use Webkul\Crm\Services\TeamDashboard;
use Webkul\Sale\Models\Team;

class ListTeams extends ListRecords
{
    use HasCRMTableViews, HasViewLayouts;

    protected static string $resource = TeamResource::class;

    protected ?array $teamDashboards = null;

    public function getViewLayouts(): array
    {
        return [ViewLayout::GRID, ViewLayout::LIST];
    }

    public function table(Table $table): Table
    {
        return TeamsTable::configure($table, grid: $this->isViewLayout(ViewLayout::GRID));
    }

    public function getTeamDashboard(Team $team): array
    {
        $this->teamDashboards ??= app(TeamDashboard::class)->forTeams($this->getTableRecords()->modelKeys());

        return $this->teamDashboards[$team->getKey()]
            ?? app(TeamDashboard::class)->forTeams([$team->getKey()])[$team->getKey()];
    }

    public function getBreadcrumb(): string
    {
        return $this->getViewLayout()->getLabel();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label(__('crm::filament/clusters/sales/resources/team/pages/list-teams.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->url(fn (): string => ConfigurationTeamResource::getUrl('create'))
                ->visible(fn (): bool => ConfigurationTeamResource::canCreate()),
        ];
    }
}
