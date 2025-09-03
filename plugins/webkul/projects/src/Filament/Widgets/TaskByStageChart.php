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

    protected static ?string $heading = 'Tasks By Stage';

    protected static ?string $maxHeight = '250px';

    protected static ?int $sort = 1;

    public function getHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/task-by-stage.heading.title');
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

            if (! empty($this->filters['selectedProjects'])) {
                $query->whereIn('project_id', $this->filters['selectedProjects']);
            }

            if (! empty($this->filters['selectedAssignees'])) {
                $query->whereHas('users', function ($q) {
                    $q->whereIn('users.id', $this->filters['selectedAssignees']);
                });
            }

            if (! empty($this->filters['selectedTags'])) {
                $query->whereHas('tags', function ($q) {
                    $q->whereIn('projects_task_tag.tag_id', $this->filters['selectedTags']);
                });
            }

            if (! empty($this->filters['selectedPartners'])) {
                $query->whereIn('parent_id', $this->filters['selectedPartners']);
            }

            $startDate = ! is_null($this->filters['startDate'] ?? null) ?
                Carbon::parse($this->filters['startDate']) :
                null;

            $endDate = ! is_null($this->filters['endDate'] ?? null) ?
                Carbon::parse($this->filters['endDate']) :
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
