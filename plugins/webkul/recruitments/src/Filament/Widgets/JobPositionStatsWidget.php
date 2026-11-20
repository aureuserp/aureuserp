<?php

namespace Webkul\Recruitment\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Webkul\Employee\Models\EmployeeJobPosition;
use Webkul\Recruitment\Models\Applicant;
use Webkul\Support\Filament\Widgets\DashboardStatsWidget;

class JobPositionStatsWidget extends DashboardStatsWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected ?string $pollingInterval = '15s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_recruitment_job_position_stats_widget';
    }

    protected function getHeading(): ?string
    {
        return __('recruitments::filament/widgets/job-position.heading.title');
    }

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

        if (! empty($this->pageFilters['selectedJobs'])) {
            $query->whereIn('id', $this->pageFilters['selectedJobs']);
            $applicantQuery->whereIn('job_id', $this->pageFilters['selectedJobs']);
        }

        if (! empty($this->pageFilters['selectedStages'])) {
            $applicantQuery->whereIn('stage_id', $this->pageFilters['selectedStages']);
        }

        $status = $this->pageFilters['status'] ?? 'all';

        if ($status !== 'all') {
            match ($status) {
                'hired'    => $applicantQuery->whereNotNull('date_closed'),
                'refused'  => $applicantQuery->whereNotNull('refuse_reason_id'),
                'ongoing'  => $applicantQuery->whereNull('refuse_reason_id')
                    ->whereNull('date_closed')
                    ->where('is_active', true),
                'archived' => $applicantQuery->where('is_active', false),
                default    => $applicantQuery,
            };
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
                SUM(CASE WHEN is_active = true THEN 1 ELSE 0 END) as active_jobs
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
        $trend = Trend::query($query)
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->count();

        return $trend->map(fn (TrendValue $value) => (int) $value->aggregate)->toArray();
    }

    protected function getStats(): array
    {
        $data = $this->getData();

        $current = $data['current'];
        $previous = $data['previous'];

        return [
            $this->themedStat(
                __('recruitments::filament/widgets/job-position.active-job-applications'),
                'heroicon-o-briefcase',
                'primary',
                number_format($current['active_jobs']),
                (float) $current['active_jobs'],
                (float) $previous['active_jobs'],
                $data['charts']['jobs'],
            ),
            $this->themedStat(
                __('recruitments::filament/widgets/job-position.total-applications'),
                'heroicon-o-user-group',
                'info',
                number_format($current['total_applications']),
                (float) $current['total_applications'],
                (float) $previous['total_applications'],
                $data['charts']['applications'],
            ),
            $this->themedStat(
                __('recruitments::filament/widgets/job-position.hired-candidate'),
                'heroicon-o-check-badge',
                'success',
                number_format($current['hired_count']),
                (float) $current['hired_count'],
                (float) $previous['hired_count'],
                $data['charts']['hired'],
            ),
        ];
    }
}
