<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Models\Task;
use Webkul\Support\Filament\Widgets\DashboardStatsWidget;

class StatsOverviewWidget extends DashboardStatsWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected ?string $pollingInterval = '15s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_stats_overview_widget';
    }

    protected function getHeading(): ?string
    {
        return __('projects::filament/widgets/stats-overview.heading.title');
    }

    protected function getData(): array
    {
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

        $currentPeriodStart = ! is_null($this->pageFilters['startDate'] ?? null) ?
            Carbon::parse($this->pageFilters['startDate']) :
            now()->subMonth();

        $currentPeriodEnd = ! is_null($this->pageFilters['endDate'] ?? null) ?
            Carbon::parse($this->pageFilters['endDate']) :
            now();

        $daysDifference = $currentPeriodEnd->diffInDays($currentPeriodStart);

        $previousPeriodStart = (clone $currentPeriodStart)->subDays($daysDifference);
        $previousPeriodEnd = (clone $currentPeriodEnd)->subDays($daysDifference);

        $currentStats = $this->calculatePeriodStats($query->clone(), $currentPeriodStart, $currentPeriodEnd);

        $previousStats = $this->calculatePeriodStats($query->clone(), $previousPeriodStart, $previousPeriodEnd);

        $tasksChart = $this->generateTrendData($query->clone(), 'COUNT', '*', $currentPeriodStart, $currentPeriodEnd);
        $hoursSpentChart = $this->generateTrendData($query->whereNull('parent_id')->clone(), 'SUM', 'total_hours_spent', $currentPeriodStart, $currentPeriodEnd);
        $remainingHoursChart = $this->generateTrendData($query->whereNull('parent_id')->clone(), 'SUM', 'remaining_hours', $currentPeriodStart, $currentPeriodEnd);

        return [
            'current'  => $currentStats,
            'previous' => $previousStats,
            'charts'   => [
                'tasks'          => $tasksChart,
                'hoursSpent'     => $hoursSpentChart,
                'remainingHours' => $remainingHoursChart,
            ],
        ];
    }

    protected function calculatePeriodStats($query, $startDate, $endDate): array
    {
        $taskStats = $query->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_tasks
            ')
            ->first();

        $stats = $query->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                SUM(total_hours_spent) as total_hours_spent,
                SUM(remaining_hours) as total_remaining_hours
            ')
            ->whereNull('parent_id')
            ->first();

        return [
            'total_tasks'           => $taskStats->total_tasks ?? 0,
            'total_hours_spent'     => $stats->total_hours_spent ?? 0,
            'total_remaining_hours' => $stats->total_remaining_hours ?? 0,
        ];
    }

    protected function generateTrendData($query, $aggregate, $column, $startDate, $endDate): array
    {
        $trend = Trend::query($query)
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->aggregate($column, $aggregate);

        return $trend->map(fn (TrendValue $value) => round((float) $value->aggregate, 2))->toArray();
    }

    protected function getStats(): array
    {
        $data = $this->getData();

        $current = $data['current'];
        $previous = $data['previous'];

        $formatHours = function ($state): string {
            $hours = floor($state);
            $minutes = round(($state - $hours) * 60);

            return $hours.':'.str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
        };

        return [
            $this->themedStat(
                __('projects::filament/widgets/stats-overview.total-tasks'),
                'heroicon-o-clipboard-document-list',
                'primary',
                number_format($current['total_tasks']),
                (float) $current['total_tasks'],
                (float) $previous['total_tasks'],
                $data['charts']['tasks'],
            )->url(TaskResource::getUrl('index')),
            $this->themedStat(
                __('projects::filament/widgets/stats-overview.total-hours-spent'),
                'heroicon-o-clock',
                'info',
                $formatHours($current['total_hours_spent']),
                (float) $current['total_hours_spent'],
                (float) $previous['total_hours_spent'],
                $data['charts']['hoursSpent'],
            )->url(TaskResource::getUrl('index')),
            $this->themedStat(
                __('projects::filament/widgets/stats-overview.total-time-remaining'),
                'heroicon-o-clock',
                'warning',
                $formatHours($current['total_remaining_hours']),
                (float) $current['total_remaining_hours'],
                (float) $previous['total_remaining_hours'],
                $data['charts']['remainingHours'],
            )->url(TaskResource::getUrl('index', ['activeTableView' => 'open_tasks'])),
        ];
    }
}
