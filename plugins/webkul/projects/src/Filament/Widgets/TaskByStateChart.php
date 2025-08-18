<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Task;

class TaskByStateChart extends ChartWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected ?string $heading = 'Tasks By State';

    protected ?string $maxHeight = '250px';

    protected static ?int $sort = 1;

    public function getHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/task-by-state.heading');
    }

    protected function getData(): array
    {
        $datasets = [
            'datasets' => [],
            'labels'   => [],
        ];

        foreach (TaskState::cases() as $state) {
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

            $datasets['labels'][] = TaskState::options()[$state->value];

            $datasets['datasets'][] = $query
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('state', $state->value)
                ->count();
        }

        $colors = TaskState::colors();

        return [
            'datasets' => [
                [
                    'data'            => $datasets['datasets'],
                    'backgroundColor' => array_map(
                        fn ($state) => match ($colors[$state] ?? 'gray') {
                            'gray'    => '#a1a1aa',
                            'warning' => '#fbbf24',
                            'success' => '#22c55e',
                            'danger'  => '#ef4444',
                            default   => '#cccccc',
                        },
                        array_keys(TaskState::options())
                    ),
                ],
            ],
            'labels' => $datasets['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
