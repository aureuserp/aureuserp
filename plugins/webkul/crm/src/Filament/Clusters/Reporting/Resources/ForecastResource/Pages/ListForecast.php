<?php

namespace Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages;

use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Tables\OpportunitiesTable;
use Webkul\Crm\Filament\Concerns\HasKanbanLayout;
use Webkul\Crm\Filament\Concerns\ManagesOpportunityKanbanCards;
use Webkul\Crm\Filament\Support\Reports\Dimension;
use Webkul\Crm\Filament\Support\Reports\Pages\ReportPage;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Filament\Support\Reports\Sql;
use Webkul\Crm\Models\Lead;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

class ListForecast extends ReportPage
{
    use HasKanbanBoard, HasKanbanLayout, ManagesOpportunityKanbanCards {
        HasKanbanLayout::content insteadof HasKanbanBoard;
        HasKanbanLayout::getKanbanQuery insteadof HasKanbanBoard;
    }

    protected static string $resource = ForecastResource::class;

    protected const NONE_COLUMN = 'none';

    protected const UPCOMING_MONTHS = 3;

    public function table(Table $table): Table
    {
        return $this->isViewLayout(ViewLayout::KANBAN)
            ? $this->configureKanbanLayoutTable(OpportunitiesTable::configure($table))
            : parent::table($table);
    }

    protected function modifyKanbanQuery(Builder $query): Builder
    {
        $query
            ->with(static::KANBAN_CARD_RELATIONS)
            ->withGlobalScope(SoftDeletingScope::class, new SoftDeletingScope)
            ->where(fn (Builder $query): Builder => $query
                ->whereNull('crm_leads.expected_closing_date')
                ->orWhereDate('crm_leads.expected_closing_date', '>=', today()->startOfMonth()));

        return $this->ignoreKanbanActivityFilter ? $query : $this->applyKanbanActivityFilter($query);
    }

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        $currency = Report::getCurrencyCode();

        return $kanban
            ->recordUrl(fn (Lead $record): string => OpportunityResource::getUrl('view', ['record' => $record]))
            ->columnSummary(function (Collection $records) use ($currency): ?string {
                $total = (float) $records->sum(fn (Lead $record): float => (float) $record->expected_revenue * (float) $record->probability / 100);

                return $total > 0 ? Number::currency($total, $currency) : null;
            })
            ->emptyState(
                __('crm::filament/clusters/reporting/resources/forecast.kanban.empty-state.heading'),
                __('crm::filament/clusters/reporting/resources/forecast.kanban.empty-state.description'),
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

        return $columns->values()->all();
    }

    protected function kanbanColumnKeyExpression(): string
    {
        return Sql::month('crm_leads.expected_closing_date');
    }

    public function moveRecord(string $recordId, string $newColumnValue, array $orderedIds): void
    {
        $record = ForecastResource::getEloquentQuery()->withoutTrashed()->find($recordId);

        if (! $record
            || ! OpportunityResource::canEdit($record)) {
            Notification::make()
                ->danger()
                ->title(__('crm::filament/clusters/reporting/resources/forecast.kanban.notifications.unauthorized'))
                ->send();

            return;
        }

        if ($newColumnValue !== self::NONE_COLUMN
            && ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $newColumnValue)) {
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
