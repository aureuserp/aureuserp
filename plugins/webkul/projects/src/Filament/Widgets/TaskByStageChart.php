<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class TaskByStageChart extends DashboardChartWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_task_by_stage_chart';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/task-by-stage.heading.title');
    }

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        foreach (TaskStage::all() as $stage) {
            if (in_array($stage->name, $labels)) {
                $labels[] = $stage->name.' '.$stage->id;
            } else {
                $labels[] = $stage->name;
            }

            $query = Task::query();

            if (! empty($this->pageFilters['selectedProjects'])) {
                $query->whereIn('project_id', $this->pageFilters['selectedProjects']);
            }

            if (! empty($this->pageFilters['selectedAssignees'])) {
                $query->whereHas('users', function ($q) {
                    $q->whereIn('users.id', $this->pageFilters['selectedAssignees']);
                });
            }

            if (! empty($this->pageFilters['selectedTags'])) {
                $query->whereHas('tags', function ($q) {
                    $q->whereIn('projects_task_tag.tag_id', $this->pageFilters['selectedTags']);
                });
            }

            if (! empty($this->pageFilters['selectedPartners'])) {
                $query->whereIn('parent_id', $this->pageFilters['selectedPartners']);
            }

            $startDate = ! is_null($this->pageFilters['startDate'] ?? null) ?
                Carbon::parse($this->pageFilters['startDate']) :
                null;

            $endDate = ! is_null($this->pageFilters['endDate'] ?? null) ?
                Carbon::parse($this->pageFilters['endDate']) :
                now();

            $data[] = $query
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('stage_id', $stage->id)
                ->count();
        }

        $ramp = $this->chartRamp();

        $colors = array_map(fn (int $index): string => $ramp[$index % count($ramp)], array_keys($data));

        return [
            'datasets' => [
                [
                    'label'           => __('projects::filament/widgets/task-by-stage.datasets.label'),
                    'data'            => $data,
                    'backgroundColor' => $colors,
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 34,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return $this->barChartOptions(8);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
