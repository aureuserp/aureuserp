<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages;

use Filament\Actions\Action;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Livewire\Attributes\Url;
use Webkul\Crm\Enums\GridLayout;
use Webkul\Crm\Enums\LeadColor;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource as ConfigurationTeamResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Tables\TeamsTable;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Services\TeamDashboard;
use Webkul\Sale\Models\Team;

class ListTeams extends ListRecords
{
    use HasCRMTableViews;

    protected static string $resource = TeamResource::class;

    #[Url(as: 'layout')]
    public string $teamLayout = GridLayout::GRID->value;

    protected ?array $teamDashboards = null;

    public function mount(): void
    {
        $this->teamLayout = $this->getGridLayout()->value;

        parent::mount();
    }

    public function getGridLayout(): GridLayout
    {
        return GridLayout::tryFrom($this->teamLayout) ?? GridLayout::GRID;
    }

    public function table(Table $table): Table
    {
        return TeamsTable::configure($table, grid: $this->getGridLayout() === GridLayout::GRID);
    }

    public function getRecordNavigationTabs(): array
    {
        return array_map(
            fn (GridLayout $layout): NavigationItem => NavigationItem::make($layout->value)
                ->label($layout->getLabel())
                ->icon($layout->getIcon())
                ->url(TeamResource::getUrl('index', $layout === GridLayout::GRID ? [] : ['layout' => $layout->value]))
                ->isActiveWhen(fn (): bool => $this->getGridLayout() === $layout),
            GridLayout::cases(),
        );
    }

    public function getTeamDashboard(Team $team): array
    {
        $this->teamDashboards ??= app(TeamDashboard::class)->forTeams($this->getTableRecords()->modelKeys());

        return $this->teamDashboards[$team->getKey()]
            ?? app(TeamDashboard::class)->forTeams([$team->getKey()])[$team->getKey()];
    }

    public function setTeamColor(int $teamId, int $color): void
    {
        $team = Team::query()->find($teamId);

        if (! $team || ! ConfigurationTeamResource::canEdit($team) || ($color !== 0 && ! LeadColor::tryFrom($color))) {
            return;
        }

        $team->update(['color' => LeadColor::tryFrom($color)?->getHex()]);
    }

    public function getBreadcrumb(): string
    {
        return $this->getGridLayout()->getLabel();
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
