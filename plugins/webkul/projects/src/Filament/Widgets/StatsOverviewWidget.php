<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Webkul\Project\Models\Task;

class StatsOverviewWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected ?string $pollingInterval = '15s';

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

    protected function calculatePercentageChange($current, $previous): array
    {
        if ($previous == 0) {
            return [
                'percentage' => 100,
                'trend'      => 'success',
            ];
        }

        $change = (($current - $previous) / $previous) * 100;

        return [
            'percentage' => abs(round($change, 1)),
            'trend'      => $change >= 0 ? 'success' : 'danger',
        ];
    }

    protected function getStats(): array
    {
        $data = $this->getData();

        $current = $data['current'];
        $previous = $data['previous'];

        $tasksChange = $this->calculatePercentageChange(
            $current['total_tasks'],
            $previous['total_tasks']
        );

        $hoursSpentChange = $this->calculatePercentageChange(
            $current['total_hours_spent'],
            $previous['total_hours_spent']
        );

        $remainingHoursChange = $this->calculatePercentageChange(
            $current['total_remaining_hours'],
            $previous['total_remaining_hours']
        );

        $formatHours = function ($state): string {
            $hours = floor($state);
            $minutes = ($state - $hours) * 60;

            return $hours.':'.$minutes;
        };

        return [
            Stat::make(__('projects::filament/widgets/stats-overview.total-tasks'), $current['total_tasks'])
                ->description($tasksChange['percentage'].'% '.($tasksChange['trend'] === 'success' ? 'increase' : 'decrease'))
                ->descriptionIcon($tasksChange['trend'] === 'success' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tasksChange['trend'])
                ->chart($data['charts']['tasks']),

            Stat::make(__('projects::filament/widgets/stats-overview.total-hours-spent'), $formatHours($current['total_hours_spent']))
                ->description($hoursSpentChange['percentage'].'% '.($hoursSpentChange['trend'] === 'success' ? 'increase' : 'decrease'))
                ->descriptionIcon($hoursSpentChange['trend'] === 'success' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($hoursSpentChange['trend'])
                ->chart($data['charts']['hoursSpent']),

            Stat::make(__('projects::filament/widgets/stats-overview.total-time-remaining'), $formatHours($current['total_remaining_hours']))
                ->description($remainingHoursChange['percentage'].'% '.($remainingHoursChange['trend'] === 'success' ? 'increase' : 'decrease'))
                ->descriptionIcon($remainingHoursChange['trend'] === 'success' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($remainingHoursChange['trend'])
                ->chart($data['charts']['remainingHours']),
        ];
    }
}
