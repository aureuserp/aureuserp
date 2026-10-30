<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
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

    public function teamColorAction(): Action
    {
        return Action::make('teamColor')
            ->record(fn (array $arguments): ?Team => Team::query()->find($arguments['team'] ?? null))
            ->modalHeading(__('crm::filament/clusters/sales/resources/team.grid-menu.color'))
            ->modalWidth('sm')
            ->modalFooterActionsAlignment(Alignment::End)
            ->authorize(fn (?Team $record): bool => $record && ConfigurationTeamResource::canEdit($record))
            ->fillForm(fn (Team $record): array => ['color' => $record->color])
            ->schema([
                ColorPicker::make('color')
                    ->label(__('crm::filament/clusters/sales/resources/team.grid-menu.color'))
                    ->hexColor(),
            ])
            ->action(fn (array $data, Team $record): bool => $record->update(['color' => filled($data['color'] ?? null) ? $data['color'] : null]));
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
