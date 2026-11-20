<?php

namespace Webkul\TimeOff\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;
use Webkul\TimeOff\Models\Leave;

class LeaveTypeWidget extends DashboardChartWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    public function getHeading(): string|Htmlable|null
    {
        return __('time-off::filament/widgets/leave-type-widget.heading.title');
    }

    protected static function getPagePermission(): ?string
    {
        return 'widget_time_off_leave_type_widget';
    }

    protected function getData(): array
    {
        $query = Leave::query();

        if ($this->pageFilters['selectedCompanies'] ?? null) {
            $query->whereIn('company_id', $this->pageFilters['selectedCompanies']);
        }

        if ($this->pageFilters['selectedDepartments'] ?? null) {
            $query->whereIn('department_id', $this->pageFilters['selectedDepartments']);
        }

        if ($this->pageFilters['startDate'] ?? null) {
            $query->where('request_date_from', '>=', Carbon::parse($this->pageFilters['startDate'])->startOfDay());
        }

        if ($this->pageFilters['endDate'] ?? null) {
            $query->where('request_date_to', '<=', Carbon::parse($this->pageFilters['endDate'])->endOfDay());
        }

        $stats = $query->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN state = 'draft' THEN 1 ELSE 0 END) as draft,
            SUM(CASE WHEN state = 'confirm' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN state = 'validate' THEN 1 ELSE 0 END) as validated,
            SUM(CASE WHEN state = 'refuse' THEN 1 ELSE 0 END) as refused,
            SUM(CASE WHEN state = 'cancel' THEN 1 ELSE 0 END) as cancelled
        ")->first();

        $states = [
            'draft'     => ['label' => __('time-off::filament/widgets/leave-type-widget.types.draft'), 'color' => 'gray', 'count' => (int) ($stats->draft ?? 0)],
            'confirmed' => ['label' => __('time-off::filament/widgets/leave-type-widget.types.confirmed'), 'color' => 'info', 'count' => (int) ($stats->confirmed ?? 0)],
            'validated' => ['label' => __('time-off::filament/widgets/leave-type-widget.types.validated'), 'color' => 'success', 'count' => (int) ($stats->validated ?? 0)],
            'refused'   => ['label' => __('time-off::filament/widgets/leave-type-widget.types.refused'), 'color' => 'danger', 'count' => (int) ($stats->refused ?? 0)],
            'cancelled' => ['label' => __('time-off::filament/widgets/leave-type-widget.types.cancelled'), 'color' => 'warning', 'count' => (int) ($stats->cancelled ?? 0)],
        ];

        $status = $this->pageFilters['status'] ?? 'all';

        $selected = $status === 'all' ? $states : array_intersect_key($states, [$status => true]);

        if ($selected === []) {
            $selected = $states;
        }

        return [
            'datasets' => [
                [
                    'label'           => __('time-off::filament/widgets/leave-type-widget.label'),
                    'data'            => array_values(array_map(fn (array $state): int => $state['count'], $selected)),
                    'backgroundColor' => array_values(array_map(fn (array $state): string => $this->brandColor($state['color']), $selected)),
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 48,
                ],
            ],
            'labels' => array_values(array_map(fn (array $state): string => $state['label'], $selected)),
        ];
    }

    protected function getOptions(): array
    {
        return $this->barChartOptions(8);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
