<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets;

use Filament\Widgets\ChartWidget;
use Webkul\Crm\Enums\ActivityDueStatus;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Models\Activity;

class ActivitiesByTypeChart extends ChartWidget
{
    public ?string $scope = 'mine';

    protected ?string $maxHeight = '280px';

    public function getHeading(): string
    {
        return __('crm::filament/clusters/sales/resources/activity/pages/graph-activities.widgets.by-type');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['stacked' => true],
                'y' => ['stacked' => true, 'ticks' => ['precision' => 0]],
            ],
        ];
    }

    protected function getData(): array
    {
        $activities = ActivityResource::getScopedQuery($this->scope)->get();

        $types = $activities->map(fn (Activity $activity): string => $activity->activityType?->name ?? '—')->unique()->sort()->values();

        $colors = [
            ActivityDueStatus::Overdue->value => '#ef4444',
            ActivityDueStatus::Today->value   => '#f59e0b',
            ActivityDueStatus::Planned->value => '#22c55e',
        ];

        return [
            'datasets' => collect(ActivityDueStatus::cases())->map(fn (ActivityDueStatus $status): array => [
                'label'           => $status->getLabel(),
                'maxBarThickness' => 48,
                'backgroundColor' => $colors[$status->value],
                'data'            => $types->map(fn (string $type): int => $activities
                    ->filter(fn (Activity $activity): bool => ($activity->activityType?->name ?? '—') === $type && $activity->getDueStatus() === $status)
                    ->count())->all(),
            ])->all(),
            'labels' => $types->all(),
        ];
    }
}
