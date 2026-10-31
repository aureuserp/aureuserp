<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Actions\CRMExportAction;
use Webkul\Crm\Filament\Actions\CRMImportAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Tables\LeadsTable;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Filament\Concerns\HasGroupBoardLayout;
use Webkul\Crm\Filament\Concerns\HasViewLayouts;
use Webkul\Crm\Filament\Concerns\ManagesOpportunityKanbanCards;
use Webkul\Crm\Filament\Concerns\ShowsRecordsGraph;
use Webkul\Crm\Filament\Exports\LeadExporter;
use Webkul\Crm\Filament\Imports\LeadImporter;
use Webkul\Crm\Filament\Support\Reports\Sql;
use Webkul\Crm\Filament\Widgets\RecordsChart;
use Webkul\Crm\Models\Lead;
use Webkul\Sale\Models\Team;
use Webkul\TableViews\Filament\Components\PresetView;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

class ListLeads extends ListRecords
{
    use HasCRMClusterNavigation, HasCRMTableViews, HasGroupBoardLayout, HasKanbanBoard, HasViewLayouts, ManagesOpportunityKanbanCards, ShowsRecordsGraph;

    public const BOARD_GROUPS = [
        'user.name'   => ['field' => 'user_id', 'relation' => 'user'],
        'team.name'   => ['field' => 'team_id', 'relation' => 'team'],
        'source.name' => ['field' => 'utm_source_id', 'relation' => 'source'],
        'priority'    => ['field' => 'priority'],
    ];

    protected static string $resource = LeadResource::class;

    #[Url(as: 'team')]
    public ?string $teamFilter = null;

    public function mount(): void
    {
        parent::mount();

        if (filled($this->teamFilter)) {
            $this->activeTableView ??= 'all';
        }
    }

    public function content(Schema $schema): Schema
    {
        return parent::content($schema);
    }

    public function getFilteredTeam(): ?Team
    {
        return filled($this->teamFilter) ? Team::query()->find((int) $this->teamFilter) : null;
    }

    protected function modifyQueryWithActiveTab(Builder $query, bool $isResolvingRecord = false): Builder
    {
        $query = parent::modifyQueryWithActiveTab($query, $isResolvingRecord);

        return $isResolvingRecord ? $query : $this->applyTeamFilter($query);
    }

    protected function getTableViewCountQuery(): Builder
    {
        return $this->applyTeamFilter($this->getTableQuery());
    }

    protected function applyTeamFilter(Builder $query): Builder
    {
        $team = $this->getFilteredTeam();

        return $team ? $query->where('crm_leads.team_id', $team->getKey()) : $query;
    }

    public function getSubheading(): string|Htmlable|null
    {
        $team = $this->getFilteredTeam();

        if (! $team) {
            return null;
        }

        return new HtmlString(
            e(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.team-filter.label', ['team' => $team->name]))
            .' — <a href="'.e(LeadResource::getUrl('index', ['layout' => $this->getViewLayout()->value])).'" class="text-primary-600 hover:underline dark:text-primary-400">'
            .e(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.team-filter.clear')).'</a>'
        );
    }

    public function getViewLayouts(): array
    {
        return [ViewLayout::LIST, ViewLayout::KANBAN, ViewLayout::GRAPH];
    }

    public function getTableGroup(): ?array
    {
        return self::BOARD_GROUPS[(string) str((string) $this->tableGrouping)->before(':')] ?? null;
    }

    public function getBoardGroup(): ?array
    {
        return $this->isViewLayout(ViewLayout::KANBAN) ? $this->getTableGroup() : null;
    }

    public function table(Table $table): Table
    {
        return $this->configureGroupBoardTable(LeadsTable::configure($table))
            ->emptyState(fn () => match (true) {
                $this->isViewLayout(ViewLayout::GRAPH) => $this->getRecordsGraphChart(),
                (bool) $this->getBoardGroup()          => view($this->getKanbanBoard()->getBoardView()),
                default                                => null,
            });
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return $this->isViewLayout(ViewLayout::LIST) ? parent::getTableRecords() : new Collection;
    }

    public function getRecordsGraph(): array
    {
        return $this->buildRecordsGraph(
            $this->filterTableQuery($this->modifyQueryWithActiveTab(LeadResource::getEloquentQuery())),
            $this->getTableGroup() ?? ['month' => 'created_at'],
            LeadResource::getPluralModelLabel(),
        );
    }

    public function getRecordsGraphHeading(): string
    {
        return match ((string) str((string) $this->tableGrouping)->before(':')) {
            'user.name'   => __('crm::filament/clusters/leads/resources/lead/pages/list-leads.graph.heading.salesperson'),
            'team.name'   => __('crm::filament/clusters/leads/resources/lead/pages/list-leads.graph.heading.team'),
            'source.name' => __('crm::filament/clusters/leads/resources/lead/pages/list-leads.graph.heading.source'),
            'priority'    => __('crm::filament/clusters/leads/resources/lead/pages/list-leads.graph.heading.priority'),
            default       => __('crm::filament/clusters/leads/resources/lead/pages/list-leads.graph.heading.month'),
        };
    }

    protected function getRecordsGraphChart(): Htmlable
    {
        $graph = $this->getRecordsGraph();

        return RecordsChart::embed($graph['data'], $graph['options'], $this->getRecordsGraphHeading(), '300px', 'crm-records-graph');
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/leads/resources/lead.navigation.title');
    }

    public function getBreadcrumb(): string
    {
        return $this->getViewLayout()->getLabel();
    }

    protected function getHeaderActions(): array
    {
        return [
            CRMImportAction::make()
                ->importer(LeadImporter::class)
                ->visible(fn (): bool => LeadResource::canCreate()),
            CRMExportAction::make()
                ->exporter(LeadExporter::class),
            CreateAction::make()
                ->label(__('crm::filament/clusters/leads/resources/lead.actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        return $kanban
            ->recordUrl(fn (Lead $record): string => LeadResource::getUrl('view', ['record' => $record]))
            ->columnWidth('300px')
            ->boardView('crm::filament.kanban.board');
    }

    protected function getKanbanQuery(KanbanBoard $board): Builder
    {
        $query = $this->filterTableQuery($this->modifyQueryWithActiveTab(LeadResource::getEloquentQuery()))
            ->with(static::KANBAN_CARD_RELATIONS);

        return $this->ignoreKanbanActivityFilter ? $query : $this->applyKanbanActivityFilter($query);
    }

    protected function kanbanColumnKeyExpression(): string
    {
        return Sql::text('crm_leads.'.($this->getBoardGroup()['field'] ?? 'id'));
    }

    public function getKanbanColumns(): array
    {
        $group = $this->getBoardGroup();

        if (! $group) {
            return [];
        }

        $records = $this->getKanbanQuery($this->getKanbanBoard())->reorder()->orderBy('crm_leads.name')->get();

        $groups = $records->groupBy(fn (Lead $record): string => $this->groupValue($record, $group['field']));

        return $this->groupLabels($groups, $group)
            ->map(function (string $label, string|int $value) use ($groups): KanbanColumn {
                $column = new KanbanColumn(value: (string) $value, label: $label, color: 'gray');

                $column->records = $groups->get((string) $value, new Collection)->values();
                $column->count = $column->records->count();

                return $column;
            })
            ->values()
            ->all();
    }

    public function moveRecord(string $recordId, string $newColumnValue, array $orderedIds): void
    {
        $field = $this->getBoardGroup()['field'] ?? null;

        $record = $field ? LeadResource::getEloquentQuery()->withoutTrashed()->find($recordId) : null;

        $allowed = collect($this->getKanbanColumns())->pluck('value')->all();

        if (! $record
            || ! LeadResource::canEdit($record)
            || ! in_array($newColumnValue, $allowed, true)) {
            Notification::make()
                ->danger()
                ->title(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.board.notifications.unauthorized'))
                ->send();

            return;
        }

        $record->{$field} = match (true) {
            $field === 'priority'                  => OpportunityPriority::from((int) $newColumnValue),
            $newColumnValue === 'none'             => null,
            default                                => (int) $newColumnValue,
        };

        $record->save();
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_leads' => PresetView::make(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.tabs.my-leads'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->where('user_id', Auth::id())),

            'unassigned' => PresetView::make(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.tabs.unassigned'))
                ->icon('heroicon-s-user-minus')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->whereNull('user_id')),

            'all' => PresetView::make(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.tabs.all'))
                ->icon('heroicon-s-inbox-stack')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()),

            'lost' => PresetView::make(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.tabs.lost'))
                ->icon('heroicon-s-hand-thumb-down')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->onlyTrashed()),
        ];
    }
}
