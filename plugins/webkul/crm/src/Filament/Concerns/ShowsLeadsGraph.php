<?php

namespace Webkul\Crm\Filament\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Support\Reports\Dimension;
use Webkul\Crm\Filament\Support\Reports\Sql;
use Webkul\Crm\Filament\Widgets\Concerns\StylesCRMDashboardCharts;
use Webkul\Crm\Models\Lead;

trait ShowsLeadsGraph
{
    use StylesCRMDashboardCharts;

    protected function groupValue(Lead $record, string $field): string
    {
        $value = $record->getAttribute($field);

        if ($value instanceof OpportunityPriority) {
            return (string) $value->value;
        }

        return filled($value) ? (string) $value : 'none';
    }

    protected function groupLabels(SupportCollection $groups, array $group): SupportCollection
    {
        $relation = $group['relation'] ?? null;

        if (! $relation) {
            return collect(OpportunityPriority::cases())->mapWithKeys(fn (OpportunityPriority $priority): array => [(string) $priority->value => $priority->getLabel()]);
        }

        $labels = $groups->map(fn (Collection $records, string $value): string => $value === 'none' ? Dimension::getNoneLabel() : (string) $records->first()->{$relation}?->name);

        $labels = isset($group['order'])
            ? $labels->sortBy(fn (string $label, string $value): mixed => $groups->get($value)->first()->{$relation}?->{$group['order']})
            : $labels->sort();

        return $labels->has('none') ? collect(['none' => $labels->pull('none')])->union($labels) : $labels;
    }

    protected function buildLeadsGraph(Builder $query, array $group, string $datasetLabel): array
    {
        $query->reorder();

        if (isset($group['month'])) {
            $months = $query
                ->selectRaw(Sql::month('crm_leads.'.$group['month']).' as graph_month, count(*) as aggregate')
                ->groupBy('graph_month')
                ->orderBy('graph_month')
                ->pluck('aggregate', 'graph_month');

            $labels = $months->keys()->map(fn (?string $month): string => filled($month) ? Dimension::monthLabel($month) : Dimension::getNoneLabel());
            $counts = $months->values()->map(fn (mixed $count): int => (int) $count);
        } else {
            $groups = $query
                ->with(isset($group['relation']) ? [$group['relation']] : [])
                ->get()
                ->groupBy(fn (Lead $record): string => $this->groupValue($record, $group['field']));

            $labels = $this->groupLabels($groups, $group);
            $counts = $labels->keys()->map(fn (string|int $value): int => $groups->get((string) $value, new Collection)->count());
        }

        return [
            'data' => [
                'datasets' => [$this->barDataset($datasetLabel, $counts->values()->all())],
                'labels'   => $labels->values()->all(),
            ],
            'options' => $this->barOptions(),
        ];
    }
}
