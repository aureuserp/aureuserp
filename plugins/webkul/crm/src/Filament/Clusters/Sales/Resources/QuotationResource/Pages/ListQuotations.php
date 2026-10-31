<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;
use Webkul\Crm\Enums\QuotationBoardGroup;
use Webkul\Crm\Enums\TeamOrderScope;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Actions\CRMExportAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions\ConfirmQuotationsBulkAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions\MarkQuotationsAsSentBulkAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions\SendQuotationsEmailBulkAction;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Filament\Concerns\HasGroupBoardLayout;
use Webkul\Crm\Filament\Concerns\HasViewLayouts;
use Webkul\Crm\Filament\Exports\QuotationExporter;
use Webkul\Crm\Filament\Support\KanbanCard;
use Webkul\Crm\Filament\Support\Reports\Dimension;
use Webkul\Crm\Filament\Support\Reports\Sql;
use Webkul\Crm\Filament\Widgets\Concerns\StylesCRMDashboardCharts;
use Webkul\Crm\Filament\Widgets\RecordsChart;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ListQuotations as BaseListQuotations;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\Team;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

class ListQuotations extends BaseListQuotations
{
    use HasCRMTableViews, HasGroupBoardLayout, HasKanbanBoard, HasViewLayouts, StylesCRMDashboardCharts;

    protected static string $resource = QuotationResource::class;

    #[Url(as: 'team')]
    public ?string $teamFilter = null;

    #[Url(as: 'scope')]
    public ?string $teamOrderScope = null;

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

        if (! $team) {
            return $query;
        }

        $query->where('team_id', $team->getKey());

        return TeamOrderScope::tryFrom((string) $this->teamOrderScope)?->apply($query) ?? $query;
    }

    public function getSubheading(): string|Htmlable|null
    {
        $team = $this->getFilteredTeam();

        if (! $team) {
            return null;
        }

        $label = collect([
            __('crm::filament/clusters/sales/resources/quotation.team-filter.label', ['team' => $team->name]),
            TeamOrderScope::tryFrom((string) $this->teamOrderScope)?->getLabel(),
        ])->filter()->implode(' · ');

        return new HtmlString(e($label).' — <a href="'.e(static::getUrl()).'" class="text-primary-600 hover:underline dark:text-primary-400">'.e(__('crm::filament/clusters/sales/resources/quotation.team-filter.clear')).'</a>');
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/sales/resources/quotation.pages.list.title');
    }

    public function getBreadcrumb(): string
    {
        return $this->getViewLayout()->getLabel();
    }

    public function getViewLayouts(): array
    {
        return [ViewLayout::LIST, ViewLayout::KANBAN, ViewLayout::GRAPH];
    }

    public function getBoardGroup(): ?QuotationBoardGroup
    {
        return $this->isViewLayout(ViewLayout::KANBAN) ? QuotationBoardGroup::fromGrouping($this->tableGrouping) ?? QuotationBoardGroup::STATUS : null;
    }

    public function table(Table $table): Table
    {
        $table = parent::table($table);

        foreach (['amount_untaxed', 'amount_tax', 'amount_total'] as $column) {
            foreach ($table->getColumn($column)?->getSummarizers() ?? [] as $summarizer) {
                $summarizer->money(default_currency_code());
            }
        }

        return $this->configureGroupBoardTable($table)
            ->groups([
                ...$table->getGroups(),
                Group::make(QuotationBoardGroup::STATUS->value)
                    ->label(QuotationBoardGroup::STATUS->getLabel())
                    ->getTitleFromRecordUsing(fn (Order $record): string => $record->state?->getLabel() ?? Dimension::getNoneLabel())
                    ->collapsible(),
            ])
            ->emptyState(fn () => match (true) {
                $this->isViewLayout(ViewLayout::GRAPH) => RecordsChart::embedGraph($this->getRecordsGraph()),
                (bool) $this->getBoardGroup()          => view($this->getKanbanBoard()->getBoardView()),
                default                                => null,
            })
            ->checkIfRecordIsSelectableUsing(fn (): bool => true)
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkActionGroup::make([
                        ExportBulkAction::make()
                            ->label(__('crm::filament/transfers.actions.export.label'))
                            ->icon('heroicon-o-arrow-up-tray')
                            ->exporter(QuotationExporter::class),
                        RestoreBulkAction::make()
                            ->successNotification(
                                Notification::make()
                                    ->success()
                                    ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.restore.notification.title'))
                                    ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.restore.notification.body')),
                            ),
                        DeleteBulkAction::make()
                            ->authorizeIndividualRecords(fn (Order $record): bool => $record->state !== OrderState::SALE && QuotationResource::canDelete($record))
                            ->successNotification(
                                Notification::make()
                                    ->success()
                                    ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.delete.notification.title'))
                                    ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.delete.notification.body')),
                            ),
                        ForceDeleteBulkAction::make()
                            ->successNotification(
                                Notification::make()
                                    ->success()
                                    ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.force-delete.notification.title'))
                                    ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.force-delete.notification.body')),
                            ),
                    ])->dropdown(false),
                    BulkActionGroup::make([
                        SendQuotationsEmailBulkAction::make(),
                        MarkQuotationsAsSentBulkAction::make(),
                        ConfirmQuotationsBulkAction::make(),
                    ])->dropdown(false),
                ]),
            ]);
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return $this->isViewLayout(ViewLayout::LIST) ? parent::getTableRecords() : new Collection;
    }

    public function getRecordsGraph(): array
    {
        $query = $this->filterTableQuery($this->modifyQueryWithActiveTab(QuotationResource::getEloquentQuery()))->reorder();
        $grouping = (string) str((string) $this->tableGrouping)->before(':');

        if ($group = QuotationBoardGroup::fromGrouping($grouping)) {
            $groups = $query
                ->with($group->relation() ? [$group->relation()] : [])
                ->get()
                ->groupBy(fn (Order $record): string => $this->boardValue($record, $group));

            $labels = $this->boardLabels($groups, $group);
            $counts = $labels->keys()->map(fn (string|int $value): int => $groups->get((string) $value, new Collection)->count());
        } else {
            $dateColumn = in_array($grouping, ['date_order', 'commitment_date'], true) ? $grouping : 'date_order';

            $months = $query
                ->selectRaw(Sql::month('sales_orders.'.$dateColumn).' as graph_month, count(*) as aggregate')
                ->groupBy('graph_month')
                ->orderBy('graph_month')
                ->pluck('aggregate', 'graph_month');

            $labels = $months->keys()->map(fn (?string $month): string => filled($month) ? Dimension::monthLabel($month) : Dimension::getNoneLabel());
            $counts = $months->values()->map(fn (mixed $count): int => (int) $count);
        }

        return [
            'data' => [
                'datasets' => [$this->barDataset(QuotationResource::getPluralModelLabel(), $counts->values()->all())],
                'labels'   => $labels->values()->all(),
            ],
            'options' => $this->barOptions(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CRMExportAction::make()
                ->exporter(QuotationExporter::class),
            ...parent::getHeaderActions(),
        ];
    }

    public function getPresetTableViews(): array
    {
        return parent::getPresetTableViews();
    }

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        return $kanban
            ->recordUrl(fn (Order $record): string => QuotationResource::getUrl('view', ['record' => $record]))
            ->canMove(fn (): bool => (bool) $this->getBoardGroup()?->isMovable())
            ->columnWidth('300px')
            ->boardView('crm::filament.kanban.board');
    }

    public function getKanbanCard(Order $record): KanbanCard
    {
        return KanbanCard::make($record->name)
            ->url($this->getKanbanBoard()->getRecordUrl($record), __('crm::filament/clusters/sales/resources/quotation.board.card.open', ['name' => $record->name]))
            ->badge($record->state?->getLabel(), $record->state?->getColor())
            ->line($record->partner?->name, 'heroicon-m-building-office-2', __('crm::filament/clusters/sales/resources/quotation.board.card.customer'))
            ->start(KanbanCard::amount(
                money((float) $record->amount_total, $record->currency?->name),
                $record->date_order?->toFormattedDateString(),
                __('crm::filament/clusters/sales/resources/quotation.board.card.total'),
                __('crm::filament/clusters/sales/resources/quotation.board.card.date'),
            ))
            ->end(KanbanCard::user($record->user, __('crm::filament/clusters/sales/resources/quotation.board.card.salesperson')));
    }

    protected function getKanbanQuery(KanbanBoard $board): Builder
    {
        return $this->filterTableQuery($this->modifyQueryWithActiveTab(QuotationResource::getEloquentQuery()))
            ->with(['partner', 'user', 'currency']);
    }

    public function getKanbanColumns(): array
    {
        $group = $this->getBoardGroup();

        if (! $group) {
            return [];
        }

        $records = $this->getKanbanQuery($this->getKanbanBoard())
            ->with($group->relation() ? [$group->relation()] : [])
            ->reorder()
            ->orderByDesc('date_order')
            ->get();

        $groups = $records->groupBy(fn (Order $record): string => $this->boardValue($record, $group));

        return $this->boardLabels($groups, $group)
            ->map(function (string $label, string|int $value) use ($groups): KanbanColumn {
                $column = new KanbanColumn(value: (string) $value, label: $label, color: 'gray');

                $column->records = $groups->get((string) $value, new Collection)->values();
                $column->count = $column->records->count();

                return $column;
            })
            ->values()
            ->all();
    }

    protected function boardValue(Order $record, QuotationBoardGroup $group): string
    {
        $value = $record->getAttribute($group->field());

        if ($value instanceof OrderState) {
            return $value->value;
        }

        return filled($value) ? (string) $value : 'none';
    }

    protected function boardLabels(SupportCollection $groups, QuotationBoardGroup $group): SupportCollection
    {
        if (! $group->relation()) {
            return collect(OrderState::cases())
                ->filter(fn (OrderState $state): bool => $groups->has($state->value))
                ->mapWithKeys(fn (OrderState $state): array => [$state->value => $state->getLabel()]);
        }

        $labels = $groups
            ->map(fn (Collection $records, string $value): string => $value === 'none' ? Dimension::getNoneLabel() : (string) $records->first()->{$group->relation()}?->name)
            ->sort();

        return $labels->has('none') ? collect(['none' => $labels->pull('none')])->union($labels) : $labels;
    }

    public function moveRecord(string $recordId, string $newColumnValue, array $orderedIds): void
    {
        $group = $this->getBoardGroup();

        $record = $group?->isMovable() ? QuotationResource::getEloquentQuery()->find($recordId) : null;

        $allowed = collect($this->getKanbanColumns())->pluck('value')->all();

        if (! $record
            || $record->trashed()
            || ! QuotationResource::canEdit($record)
            || ! in_array($newColumnValue, $allowed, true)) {
            Notification::make()
                ->danger()
                ->title(__('crm::filament/clusters/sales/resources/quotation.board.unauthorized'))
                ->send();

            return;
        }

        $record->{$group->field()} = $newColumnValue === 'none' ? null : (int) $newColumnValue;
        $record->save();
    }
}
