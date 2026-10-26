<?php

namespace Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages;

use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Crm\Filament\Concerns\ManagesOpportunityKanbanCards;
use Webkul\Crm\Filament\Support\Reports\Dimension;
use Webkul\Crm\Filament\Support\Reports\ForecastReport;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Filament\Support\Reports\Sql;
use Webkul\Crm\Models\Lead;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

class KanbanForecast extends ListRecords
{
    use HasCrmClusterNavigation, HasKanbanBoard, ManagesOpportunityKanbanCards;

    protected static string $resource = ForecastResource::class;

    protected const NONE_COLUMN = 'none';

    protected const UPCOMING_MONTHS = 3;

    public function mount(): void
    {
        parent::mount();

        $this->resetKanbanFilters();
    }

    public function resetKanbanFilters(): void
    {
        $this->kanbanFilters = $this->defaultKanbanFilters();
        $this->kanbanActivityFilter = [];
    }

    public function defaultKanbanFilters(): array
    {
        return [
            'user_id' => null,
            'team_id' => null,
            'period'  => 'all',
            'presets' => [],
        ];
    }

    public function getTitle(): string
    {
        return ForecastResource::getReportTitle();
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/reporting/report.views.kanban');
    }

    protected function getHeaderActions(): array
    {
        return [
            ...ForecastResource::getViewSwitcherActions('index'),
            ...ForecastResource::getExportActions(fn (Builder $query): Builder => $query->whereIn(
                'crm_leads.id',
                $this->getKanbanQuery($this->getKanbanBoard())->reorder()->pluck('crm_leads.id'),
            )),
        ];
    }

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        $currency = Report::getCurrencyCode();

        $label = fn (string $key): string => __('crm::filament/clusters/reporting/resources/forecast.kanban.'.$key);

        return $kanban
            ->recordUrl(fn (Lead $record): string => OpportunityResource::getUrl('view', ['record' => $record]))
            ->columnSummary(function (Collection $records) use ($currency): ?string {
                $total = (float) $records->sum(fn (Lead $record): float => (float) $record->expected_revenue * (float) $record->probability / 100);

                return $total > 0 ? Number::currency($total, $currency) : null;
            })
            ->searchable(['name', 'email', 'partner.name'])
            ->filters([
                Select::make('user_id')
                    ->label($label('filters.salesperson'))
                    ->options(fn (): array => Report::getSalespersonOptions())
                    ->searchable()
                    ->placeholder($label('filters.all')),
                Select::make('team_id')
                    ->label($label('filters.team'))
                    ->options(fn (): array => Report::getTeamOptions())
                    ->searchable()
                    ->placeholder($label('filters.all')),
                Select::make('period')
                    ->label($label('filters.period'))
                    ->options([
                        'upcoming' => $label('filters.upcoming'),
                        'all'      => $label('filters.all-dates'),
                    ])
                    ->selectablePlaceholder(false),
                Select::make('presets')
                    ->label($label('filters.presets'))
                    ->options(fn (): array => collect(static::kanbanPresets())->map(fn (array $preset): string => $preset['label'])->all())
                    ->multiple()
                    ->placeholder($label('filters.all')),
            ])
            ->filtersColumns(1)
            ->emptyState(
                $label('empty-state.heading'),
                $label('empty-state.description'),
                'heroicon-o-arrow-trending-up',
            )
            ->columnWidth('300px')
            ->boardView('crm::filament.kanban.board')
            ->columnView('crm::filament.kanban.column')
            ->cardView('crm::filament.kanban.opportunity-card');
    }

    public function getKanbanColumns(): array
    {
        $query = $this->getKanbanQuery($this->getKanbanBoard());

        $monthExpression = Sql::month('crm_leads.expected_closing_date');

        $months = (clone $query)
            ->reorder()
            ->whereNotNull('crm_leads.expected_closing_date')
            ->selectRaw("{$monthExpression} as closing_month")
            ->distinct()
            ->pluck('closing_month')
            ->map(fn (mixed $month): string => (string) $month);

        for ($offset = 0; $offset <= self::UPCOMING_MONTHS; $offset++) {
            $months->push(today()->startOfMonth()->addMonths($offset)->format('Y-m'));
        }

        $records = (clone $query)->orderBy('crm_leads.expected_closing_date')->orderByDesc('crm_leads.expected_revenue')->get();

        $currentMonth = today()->format('Y-m');

        $columns = collect([self::NONE_COLUMN])
            ->merge($months->unique()->sort()->values())
            ->map(function (string $month) use ($records, $currentMonth): KanbanColumn {
                $column = new KanbanColumn(
                    value: $month,
                    label: $month === self::NONE_COLUMN ? Dimension::getNoneLabel() : Dimension::monthLabel($month),
                    color: $month === $currentMonth ? 'primary' : 'gray',
                );

                $column->records = $records
                    ->filter(fn (Lead $record): bool => ($record->expected_closing_date?->format('Y-m') ?? self::NONE_COLUMN) === $month)
                    ->values();

                $column->count = $column->records->count();

                return $column;
            });

        if (($this->kanbanFilters['period'] ?? 'all') === 'upcoming') {
            $columns = $columns->reject(fn (KanbanColumn $column): bool => $column->value === self::NONE_COLUMN && $column->count === 0);
        }

        return $columns->values()->all();
    }

    public static function kanbanPresets(): array
    {
        return collect(ForecastReport::presets())
            ->only(['unassigned', 'ongoing', 'won', 'lost', 'created_this_year', 'closed_this_year'])
            ->all();
    }

    protected function getKanbanQuery(KanbanBoard $board): Builder
    {
        $filters = $this->kanbanFilters;

        $presets = array_intersect_key(static::kanbanPresets(), array_flip(array_filter((array) ($filters['presets'] ?? []))));

        $query = ForecastResource::getEloquentQuery()
            ->with(static::KANBAN_CARD_RELATIONS)
            ->when(! isset($presets['lost']), fn (Builder $query): Builder => $query->whereNull('crm_leads.deleted_at'));

        foreach ($presets as $preset) {
            $query = $preset['query']($query);
        }

        $query
            ->when(filled($filters['user_id'] ?? null), fn (Builder $query): Builder => $query->where('crm_leads.user_id', $filters['user_id']))
            ->when(filled($filters['team_id'] ?? null), fn (Builder $query): Builder => $query->where('crm_leads.team_id', $filters['team_id']))
            ->when(($filters['period'] ?? 'all') === 'upcoming', fn (Builder $query): Builder => $query->whereDate('crm_leads.expected_closing_date', '>=', today()->startOfMonth()));

        if (filled($search = $this->kanbanSearch)) {
            $query->where(fn (Builder $query): Builder => $query
                ->where('crm_leads.name', 'like', "%{$search}%")
                ->orWhere('crm_leads.email', 'like', "%{$search}%")
                ->orWhereHas('partner', fn (Builder $query): Builder => $query->where('name', 'like', "%{$search}%")));
        }

        return $this->ignoreKanbanActivityFilter ? $query : $this->applyKanbanActivityFilter($query);
    }

    protected function kanbanColumnKeyExpression(): string
    {
        return Sql::month('crm_leads.expected_closing_date');
    }

    public function moveRecord(string $recordId, string $newColumnValue, array $orderedIds): void
    {
        $record = ForecastResource::getEloquentQuery()->whereNull('crm_leads.deleted_at')->find($recordId);

        if (! $record || ! OpportunityResource::canEdit($record)) {
            Notification::make()
                ->danger()
                ->title(__('crm::filament/clusters/reporting/resources/forecast.kanban.notifications.unauthorized'))
                ->send();

            return;
        }

        if ($newColumnValue !== self::NONE_COLUMN && ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $newColumnValue)) {
            return;
        }

        $record->expected_closing_date = $newColumnValue === self::NONE_COLUMN
            ? null
            : Carbon::createFromFormat('!Y-m', $newColumnValue)->endOfMonth()->toDateString();

        $record->save();

        Notification::make()
            ->success()
            ->title(__('crm::filament/clusters/reporting/resources/forecast.kanban.notifications.moved'))
            ->send();
    }
}
