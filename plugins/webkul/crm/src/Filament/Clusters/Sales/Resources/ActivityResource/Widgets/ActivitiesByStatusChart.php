<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets;

use Filament\Widgets\ChartWidget;
use Webkul\Crm\Enums\ActivityDueStatus;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;

class ActivitiesByStatusChart extends ChartWidget
{
    public ?string $scope = 'mine';

    protected ?string $maxHeight = '280px';

    public function getHeading(): string
    {
        return __('crm::filament/clusters/sales/resources/activity/pages/graph-activities.widgets.by-status');
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = collect(ActivityDueStatus::cases())->map(fn (ActivityDueStatus $status): int => ActivityResource::getScopedQuery($this->scope)->dueStatus($status)->count());

        return [
            'datasets' => [
                [
                    'data'            => $counts->all(),
                    'backgroundColor' => ['#ef4444', '#f59e0b', '#22c55e'],
                ],
            ],
            'labels' => collect(ActivityDueStatus::cases())->map->getLabel()->all(),
        ];
    }
}
