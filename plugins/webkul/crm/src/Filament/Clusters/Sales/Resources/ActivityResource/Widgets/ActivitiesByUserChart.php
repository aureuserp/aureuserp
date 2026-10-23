<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets;

use Filament\Widgets\ChartWidget;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Models\Activity;

class ActivitiesByUserChart extends ChartWidget
{
    public ?string $scope = 'mine';

    protected ?string $maxHeight = '220px';

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('crm::filament/clusters/sales/resources/activity/pages/graph-activities.widgets.by-user');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins'   => ['legend' => ['display' => false]],
            'scales'    => ['x' => ['ticks' => ['precision' => 0]]],
        ];
    }

    protected function getData(): array
    {
        $counts = ActivityResource::getScopedQuery($this->scope)
            ->get()
            ->groupBy(fn (Activity $activity): string => $activity->assignedTo?->name ?? '—')
            ->map->count()
            ->sortDesc();

        return [
            'datasets' => [
                [
                    'label'           => __('crm::filament/clusters/sales/resources/activity.plural-model-label'),
                    'data'            => $counts->values()->all(),
                    'backgroundColor' => '#3b82f6',
                    'maxBarThickness' => 32,
                ],
            ],
            'labels' => $counts->keys()->all(),
        ];
    }
}
