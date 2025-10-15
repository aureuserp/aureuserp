<?php

namespace Webkul\Recruitment\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Recruitment\Models\Applicant;

class ApplicantChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    public function getHeading(): string|Htmlable|null
    {
        return __('recruitments::filament/widgets/applicant.heading.title');
    }

    protected function getData(): array
    {
        $query = Applicant::query();

        if ($this->pageFilters['selectedJobs'] ?? null) {
            $query->whereIn('job_id', $this->pageFilters['selectedJobs']);
        }

        if ($this->pageFilters['selectedDepartments'] ?? null) {
            $query->whereIn('department_id', $this->pageFilters['selectedDepartments']);
        }

        if ($this->pageFilters['selectedCompanies'] ?? null) {
            $query->whereIn('company_id', $this->pageFilters['selectedCompanies']);
        }

        if ($this->pageFilters['selectedStages'] ?? null) {
            $query->whereIn('stage_id', $this->pageFilters['selectedStages']);
        }

        if ($this->pageFilters['selectedRecruiters'] ?? null) {
            $query->whereIn('recruiter_id', $this->pageFilters['selectedRecruiters']);
        }

        if ($this->pageFilters['startDate'] ?? null) {
            $query->where('created_at', '>=', Carbon::parse($this->pageFilters['startDate'])->startOfDay());
        }

        if ($this->pageFilters['endDate'] ?? null) {
            $query->where('created_at', '<=', Carbon::parse($this->pageFilters['endDate'])->endOfDay());
        }

        $stats = $query->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN refuse_reason_id IS NOT NULL THEN 1 ELSE 0 END) as refused,
            SUM(CASE WHEN date_closed IS NOT NULL THEN 1 ELSE 0 END) as hired,
            SUM(CASE WHEN is_active = 0 OR deleted_at IS NOT NULL THEN 1 ELSE 0 END) as archived,
            SUM(CASE
                WHEN refuse_reason_id IS NULL
                AND date_closed IS NULL
                AND is_active = 1
                AND deleted_at IS NULL THEN 1
                ELSE 0
            END) as ongoing
        ')->first();

        $data = match ($this->pageFilters['status'] ?? 'all') {
            'ongoing'  => ['Ongoing' => $stats->ongoing ?? 0],
            'hired'    => ['Hired' => $stats->hired ?? 0],
            'refused'  => ['Refused' => $stats->refused ?? 0],
            'archived' => ['Archived' => $stats->archived ?? 0],
            default    => [
                'Ongoing'  => $stats->ongoing ?? 0,
                'Hired'    => $stats->hired ?? 0,
                'Refused'  => $stats->refused ?? 0,
                'Archived' => $stats->archived ?? 0,
            ],
        };

        return [
            'datasets' => [
                [
                    'label'           => __('recruitments::filament/widgets/applicant.heading.title'),
                    'data'            => array_values($data),
                    'backgroundColor' => array_map(fn ($key) => match ($key) {
                        __('recruitments::filament/widgets/applicant.ongoing')  => '#3b82f6',
                        __('recruitments::filament/widgets/applicant.hired')    => '#22c55e',
                        __('recruitments::filament/widgets/applicant.refused')  => '#ef4444',
                        __('recruitments::filament/widgets/applicant.archived') => '#94a3b8',
                    }, array_keys($data)),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
