<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;

class TaskByStageChart extends ChartWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected ?string $heading = 'Tasks By Stage';

    protected ?string $maxHeight = '250px';

    protected static ?int $sort = 1;

    public function getHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/task-by-stage.heading');
    }

    protected function getData(): array
    {
        $datasets = [
            'datasets' => [],
            'labels'   => [],
        ];

        foreach (TaskStage::all() as $stage) {
            if (in_array($stage->name, $datasets['labels'])) {
                $datasets['labels'][] = $stage->name.' '.$stage->id;
            } else {
                $datasets['labels'][] = $stage->name;
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

            $datasets['datasets'][] = $query
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('stage_id', $stage->id)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => __('projects::filament/widgets/task-by-stage.datasets.label'),
                    'data'  => $datasets['datasets'],
                ],
            ],
            'labels' => $datasets['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
