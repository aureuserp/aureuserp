<?php

namespace Webkul\Recruitment\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Webkul\Employee\Models\EmployeeJobPosition;
use Webkul\Recruitment\Models\Applicant;

class JobPositionStatsWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected ?string $pollingInterval = '15s';

    protected function getData(): array
    {
        $query = EmployeeJobPosition::query();
        $applicantQuery = Applicant::query();

        if (! empty($this->pageFilters['selectedDepartments'])) {
            $query->whereIn('department_id', $this->pageFilters['selectedDepartments']);
            $applicantQuery->whereIn('department_id', $this->pageFilters['selectedDepartments']);
        }

        if (! empty($this->pageFilters['selectedCompanies'])) {
            $query->whereIn('company_id', $this->pageFilters['selectedCompanies']);
            $applicantQuery->whereIn('company_id', $this->pageFilters['selectedCompanies']);
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

        $currentStats = $this->calculatePeriodStats($query->clone(), $applicantQuery->clone(), $currentPeriodStart, $currentPeriodEnd);
        $previousStats = $this->calculatePeriodStats($query->clone(), $applicantQuery->clone(), $previousPeriodStart, $previousPeriodEnd);

        $jobsChart = $this->generateChartData($query->clone(), $currentPeriodStart, $currentPeriodEnd);
        $applicationsChart = $this->generateChartData($applicantQuery->clone(), $currentPeriodStart, $currentPeriodEnd);
        $hiredChart = $this->generateChartData(
            $applicantQuery->clone()->whereNotNull('date_closed'),
            $currentPeriodStart,
            $currentPeriodEnd
        );

        return [
            'current'  => $currentStats,
            'previous' => $previousStats,
            'charts'   => [
                'jobs'         => $jobsChart,
                'applications' => $applicationsChart,
                'hired'        => $hiredChart,
            ],
        ];
    }

    protected function calculatePeriodStats($query, $applicantQuery, $startDate, $endDate): array
    {
        $jobStats = $query->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_jobs,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_jobs
            ')
            ->first();

        $applicantStats = $applicantQuery->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_applications,
                SUM(CASE WHEN date_closed IS NOT NULL THEN 1 ELSE 0 END) as hired_count
            ')
            ->first();

        return [
            'total_jobs'         => $jobStats->total_jobs ?? 0,
            'active_jobs'        => $jobStats->active_jobs ?? 0,
            'total_applications' => $applicantStats->total_applications ?? 0,
            'hired_count'        => $applicantStats->hired_count ?? 0,
        ];
    }

    protected function generateChartData($query, $startDate, $endDate): array
    {
        $data = [];
        $current = clone $startDate;

        while ($current <= $endDate) {
            $count = $query->whereDate('created_at', $current)->count();
            $data[] = $count;
            $current->addDay();
        }

        return $data;
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

        $jobsChange = $this->calculatePercentageChange(
            $current['active_jobs'],
            $previous['active_jobs']
        );

        $applicationsChange = $this->calculatePercentageChange(
            $current['total_applications'],
            $previous['total_applications']
        );

        $hiredChange = $this->calculatePercentageChange(
            $current['hired_count'],
            $previous['hired_count']
        );

        return [
            Stat::make(__('recruitments::filament/widgets/job-position.active-job-applications'), $current['active_jobs'])
                ->description($jobsChange['percentage'].'% '.($jobsChange['trend'] === 'success' ? 'increase' : 'decrease'))
                ->descriptionIcon($jobsChange['trend'] === 'success' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($jobsChange['trend'])
                ->chart($data['charts']['jobs']),

            Stat::make(__('recruitments::filament/widgets/job-position.total-applications'), $current['total_applications'])
                ->description($applicationsChange['percentage'].'% '.($applicationsChange['trend'] === 'success' ? 'increase' : 'decrease'))
                ->descriptionIcon($applicationsChange['trend'] === 'success' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($applicationsChange['trend'])
                ->chart($data['charts']['applications']),

            Stat::make(__('recruitments::filament/widgets/job-position.hired-candidate'), $current['hired_count'])
                ->description($hiredChange['percentage'].'% '.($hiredChange['trend'] === 'success' ? 'increase' : 'decrease'))
                ->descriptionIcon($hiredChange['trend'] === 'success' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($hiredChange['trend'])
                ->chart($data['charts']['hired']),
        ];
    }
}
