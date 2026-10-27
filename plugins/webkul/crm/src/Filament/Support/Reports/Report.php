<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Webkul\Crm\Enums\ReportChartType;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas\OpportunityForm;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

abstract class Report
{
    abstract public static function dimensions(): array;

    abstract public static function measures(): array;

    abstract public static function presets(): array;

    abstract public static function getDefaultMeasure(): string;

    abstract public static function getDefaultGroupBy(): string;

    public static function getDefaultSplitBy(): ?string
    {
        return null;
    }

    public static function getDefaultChartType(): ReportChartType
    {
        return ReportChartType::BAR;
    }

    public static function getSalespersonColumn(): string
    {
        return 'user_id';
    }

    public static function getTeamColumn(): string
    {
        return 'team_id';
    }

    public static function getDefaultPresets(): array
    {
        return array_keys(array_filter(static::presets(), fn (array $preset): bool => $preset['default'] ?? false));
    }

    public static function getDefaultFilters(): array
    {
        return [
            'measure'    => static::getDefaultMeasure(),
            'group_by'   => static::getDefaultGroupBy(),
            'split_by'   => static::getDefaultSplitBy(),
            'chart_type' => static::getDefaultChartType()->value,
            'presets'    => static::getDefaultPresets(),
            'user_ids'   => [],
            'team_ids'   => [],
        ];
    }

    public static function getMeasure(?string $key): Measure
    {
        $measures = static::measures();

        return $measures[$key] ?? $measures[static::getDefaultMeasure()];
    }

    public static function getDimension(?string $key): ?Dimension
    {
        return filled($key) ? (static::dimensions()[$key] ?? null) : null;
    }

    public static function applyFilters(Builder $query, array $filters): Builder
    {
        $presets = static::presets();

        foreach ((array) ($filters['presets'] ?? []) as $preset) {
            if (isset($presets[$preset])) {
                $presets[$preset]['query']($query);
            }
        }

        $model = $query->getModel();

        return $query
            ->when(filled($filters['user_ids'] ?? null), fn (Builder $query): Builder => $query->whereIn($model->qualifyColumn(static::getSalespersonColumn()), (array) $filters['user_ids']))
            ->when(filled($filters['team_ids'] ?? null), fn (Builder $query): Builder => $query->whereIn($model->qualifyColumn(static::getTeamColumn()), (array) $filters['team_ids']));
    }

    public static function aggregate(Builder $query, string $groupBy, ?string $splitBy, string $measure): array
    {
        $groupDimension = static::getDimension($groupBy) ?? static::getDimension(static::getDefaultGroupBy());

        $splitDimension = $splitBy !== $groupBy ? static::getDimension($splitBy) : null;

        $measureDefinition = static::getMeasure($measure);

        $baseQuery = (clone $query)->toBase()->reorder();

        $baseQuery->columns = null;

        $baseQuery
            ->selectRaw($groupDimension->getKeyExpression().' as group_key')
            ->selectRaw($measureDefinition->aggregate.' as aggregate')
            ->groupByRaw($groupDimension->getKeyExpression());

        if ($splitDimension) {
            $baseQuery
                ->selectRaw($splitDimension->getKeyExpression().' as split_key')
                ->groupByRaw($splitDimension->getKeyExpression());
        }

        $rows = $baseQuery->get()->map(fn (object $row): array => [
            'group' => filled($row->group_key) ? (string) $row->group_key : null,
            'split' => $splitDimension && filled($row->split_key) ? (string) $row->split_key : null,
            'value' => $measureDefinition->round($row->aggregate),
        ]);

        $groupKeys = $groupDimension->sortKeys($rows->pluck('group')->unique()->values()->all());

        $splitKeys = $splitDimension
            ? $splitDimension->sortKeys($rows->pluck('split')->unique()->values()->all())
            : [null];

        $splitLabels = $splitDimension ? $splitDimension->labels($splitKeys) : [$measureDefinition->label];

        return [
            'labels' => $groupDimension->labels($groupKeys),
            'series' => collect($splitKeys)->values()->map(fn (?string $splitKey, int $index): array => [
                'label' => $splitLabels[$index],
                'data'  => array_map(
                    fn (?string $groupKey): float => (float) $rows
                        ->where('group', $groupKey)
                        ->where('split', $splitKey)
                        ->sum('value'),
                    $groupKeys,
                ),
            ])->all(),
        ];
    }

    public static function getMeasureOptions(): array
    {
        return array_map(fn (Measure $measure): string => $measure->label, static::measures());
    }

    public static function getDimensionOptions(): array
    {
        return array_map(fn (Dimension $dimension): string => $dimension->label, static::dimensions());
    }

    public static function getPresetOptions(): array
    {
        return array_map(fn (array $preset): string => $preset['label'], static::presets());
    }

    public static function getTableFilters(): array
    {
        return [
            ...collect(static::presets())
                ->map(fn (array $preset, string $key): Filter => Filter::make($key)
                    ->label($preset['label'])
                    ->toggle()
                    ->default($preset['default'] ?? false)
                    ->query(fn (Builder $query): Builder => $preset['query']($query)))
                ->values()
                ->all(),
            SelectFilter::make('user_ids')
                ->label(__('crm::filament/clusters/reporting/report.filters.salespeople'))
                ->options(fn (): array => static::getSalespersonOptions())
                ->multiple()
                ->query(fn (Builder $query, array $data): Builder => $query->when(
                    filled($data['values'] ?? null),
                    fn (Builder $query): Builder => $query->whereIn($query->getModel()->qualifyColumn(static::getSalespersonColumn()), $data['values']),
                )),
            SelectFilter::make('team_ids')
                ->label(__('crm::filament/clusters/reporting/report.filters.teams'))
                ->options(fn (): array => static::getTeamOptions())
                ->multiple()
                ->query(fn (Builder $query, array $data): Builder => $query->when(
                    filled($data['values'] ?? null),
                    fn (Builder $query): Builder => $query->whereIn($query->getModel()->qualifyColumn(static::getTeamColumn()), $data['values']),
                )),
        ];
    }

    public static function getTableGroups(): array
    {
        return collect(static::dimensions())
            ->map(fn (Dimension $dimension, string $key): Group => $dimension->toTableGroup($key))
            ->values()
            ->all();
    }

    public static function getSummaryColumns(?string $groupBy = null): array
    {
        $currency = static::getCurrencyCode();

        $groupDimension = static::getDimension($groupBy) ?? static::getDimension(static::getDefaultGroupBy());

        return [
            TextColumn::make($groupDimension->getGroupColumn())
                ->label($groupDimension->label),
            ...collect(static::measures())
                ->map(fn (Measure $measure, string $key): TextColumn => TextColumn::make('measure_'.$key)
                    ->label($measure->label)
                    ->summarize(
                        Summarizer::make()
                            ->using(fn (QueryBuilder $query): mixed => $query->selectRaw($measure->aggregate.' as aggregate')->value('aggregate'))
                            ->formatStateUsing(fn (mixed $state): string => $measure->format($state, $currency)),
                    ))
                ->values()
                ->all(),
        ];
    }

    public static function getSalespersonOptions(): array
    {
        return OpportunityForm::scopeSalespeople(User::query(), current_company_id())
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getTeamOptions(): array
    {
        return Team::query()
            ->where(owned_by_company(current_company_id()))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public static function getCurrencyCode(): string
    {
        return Company::query()->find(current_company_id())?->currency?->code ?? 'USD';
    }
}
