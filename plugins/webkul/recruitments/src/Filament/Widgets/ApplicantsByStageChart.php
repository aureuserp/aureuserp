<?php

namespace Webkul\Recruitment\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Recruitment\Models\Applicant;
use Webkul\Recruitment\Models\Stage;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class ApplicantsByStageChart extends DashboardChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    protected ?array $resolvedData = null;

    protected static function getPagePermission(): ?string
    {
        return 'widget_recruitment_applicant_chart_widget';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('recruitments::filament/widgets/applicants-by-stage.heading.title');
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        return $this->resolvedData ??= $this->resolveData();
    }

    protected function resolveData(): array
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

        if ($this->pageFilters['startDate'] ?? null) {
            $query->where('created_at', '>=', Carbon::parse($this->pageFilters['startDate'])->startOfDay());
        }

        if ($this->pageFilters['endDate'] ?? null) {
            $query->where('created_at', '<=', Carbon::parse($this->pageFilters['endDate'])->endOfDay());
        }

        $counts = $query
            ->selectRaw('stage_id, COUNT(*) as total')
            ->groupBy('stage_id')
            ->pluck('total', 'stage_id');

        $stages = Stage::query()
            ->orderBy('sort')
            ->get()
            ->filter(fn (Stage $stage): bool => (int) ($counts[$stage->id] ?? 0) > 0)
            ->values();

        $palette = collect(['info', 'success', 'warning', 'danger', 'primary', 'gray'])
            ->flatMap(fn (string $name): array => [$this->brandColor($name), $this->brandColor($name, 300)])
            ->unique()
            ->values()
            ->all();

        return [
            'datasets' => [
                [
                    'label'           => __('recruitments::filament/widgets/applicants-by-stage.dataset'),
                    'data'            => $stages->map(fn (Stage $stage): int => (int) $counts[$stage->id])->all(),
                    'backgroundColor' => $stages->map(fn (Stage $stage, int $index): string => $palette[$index % count($palette)])->all(),
                    'borderWidth'     => 0,
                    'hoverOffset'     => 0,
                ],
            ],
            'labels' => $stages->map(fn (Stage $stage): string => $stage->name)->all(),
        ];
    }

    protected function getOptions(): array
    {
        $total = (int) array_sum($this->getData()['datasets'][0]['data']);

        return $this->doughnutChartOptions(
            number_format($total),
            __('recruitments::filament/widgets/applicants-by-stage.dataset'),
        );
    }
}
