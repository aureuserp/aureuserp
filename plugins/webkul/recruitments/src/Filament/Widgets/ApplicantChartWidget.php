<?php

namespace Webkul\Recruitment\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Recruitment\Models\Applicant;

class ApplicantChartWidget extends ChartWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '400px';

    public function getHeading(): string|Htmlable|null
    {
        return __('recruitments::filament/widgets/applicant.heading.title');
    }

    protected function getData(): array
    {
        $query = Applicant::query();

        if ($this->filters['selectedJobs'] ?? null) {
            $query->whereIn('job_id', $this->filters['selectedJobs']);
        }

        if ($this->filters['selectedDepartments'] ?? null) {
            $query->whereIn('department_id', $this->filters['selectedDepartments']);
        }

        if ($this->filters['selectedCompanies'] ?? null) {
            $query->whereIn('company_id', $this->filters['selectedCompanies']);
        }

        if ($this->filters['selectedStages'] ?? null) {
            $query->whereIn('stage_id', $this->filters['selectedStages']);
        }

        if ($this->filters['selectedRecruiters'] ?? null) {
            $query->whereIn('recruiter_id', $this->filters['selectedRecruiters']);
        }

        if ($this->filters['startDate'] ?? null) {
            $query->where('created_at', '>=', Carbon::parse($this->filters['startDate'])->startOfDay());
        }

        if ($this->filters['endDate'] ?? null) {
            $query->where('created_at', '<=', Carbon::parse($this->filters['endDate'])->endOfDay());
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

        $data = match ($this->filters['status'] ?? 'all') {
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
                    'label'           => __('recruitments::filament/widgets/applicant.overview.label'),
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
