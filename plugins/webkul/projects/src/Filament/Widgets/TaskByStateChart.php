<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Exception;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Task;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class TaskByStateChart extends DashboardChartWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_task_by_state_chart';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/task-by-state.heading.title');
    }

    protected function getData(): array
    {
        $labels = [];
        $data = [];

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

            $this->applyDateFilters($query);

            $labels[] = TaskState::options()[$state->value];
            $data[] = $query->where('state', $state->value)->count();
        }

        $colors = TaskState::colors();

        return [
            'datasets' => [
                [
                    'data'            => $data,
                    'backgroundColor' => array_map(
                        fn ($state): string => $this->brandColor($colors[$state] ?? 'gray'),
                        array_keys(TaskState::options())
                    ),
                    'borderWidth'     => 0,
                    'hoverOffset'     => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return $this->doughnutChartOptions();
    }

    private function applyDateFilters($query): void
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        if (! empty($startDate)) {
            try {
                $startDateCarbon = Carbon::parse($startDate)->startOfDay();
                $query->where('created_at', '>=', $startDateCarbon);
            } catch (Exception) {
            }
        }

        if (! empty($endDate)) {
            try {
                $endDateCarbon = Carbon::parse($endDate)->endOfDay();
                $query->where('created_at', '<=', $endDateCarbon);
            } catch (Exception) {
            }
        }

        if (empty($startDate) && empty($endDate)) {
            $query->whereBetween('created_at', [
                now()->subMonth()->startOfDay(),
                now()->endOfDay(),
            ]);
        }
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
