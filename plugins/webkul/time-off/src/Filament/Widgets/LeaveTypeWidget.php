<?php

namespace Webkul\TimeOff\Filament\Widgets;

use Carbon\Carbon;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\TimeOff\Models\Leave;

class LeaveTypeWidget extends ChartWidget
{
     use HasWidgetShield;
    public function getHeading(): string|Htmlable|null
    {
        return __('time-off::filament/widgets/leave-type-widget.heading.title');
    }

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

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

        $stats = $query->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN state = "draft" THEN 1 ELSE 0 END) as draft,
            SUM(CASE WHEN state = "confirm" THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN state = "validate" THEN 1 ELSE 0 END) as validated,
            SUM(CASE WHEN state = "refuse" THEN 1 ELSE 0 END) as refused,
            SUM(CASE WHEN state = "cancel" THEN 1 ELSE 0 END) as cancelled
        ')->first();

        $data = match ($this->pageFilters['status'] ?? 'all') {
            'draft'     => ['Draft' => $stats->draft ?? 0],
            'confirmed' => ['Confirmed' => $stats->confirmed ?? 0],
            'validated' => ['Validated' => $stats->validated ?? 0],
            'refused'   => ['Refused' => $stats->refused ?? 0],
            'cancelled' => ['Cancelled' => $stats->cancelled ?? 0],
            default     => [
                __('time-off::filament/widgets/leave-type-widget.types.draft')     => $stats->draft ?? 0,
                __('time-off::filament/widgets/leave-type-widget.types.confirmed') => $stats->confirmed ?? 0,
                __('time-off::filament/widgets/leave-type-widget.types.validated') => $stats->validated ?? 0,
                __('time-off::filament/widgets/leave-type-widget.types.refused')   => $stats->refused ?? 0,
                __('time-off::filament/widgets/leave-type-widget.types.cancelled') => $stats->cancelled ?? 0,
            ],
        };

        return [
            'datasets' => [
                [
                    'label'           => __('time-off::filament/widgets/leave-type-widget.label'),
                    'data'            => array_values($data),
                    'backgroundColor' => array_map(fn ($key) => match ($key) {
                        __('time-off::filament/widgets/leave-type-widget.types.draft')     => '#94a3b8',
                        __('time-off::filament/widgets/leave-type-widget.types.confirmed') => '#3b82f6',
                        __('time-off::filament/widgets/leave-type-widget.types.validated') => '#22c55e',
                        __('time-off::filament/widgets/leave-type-widget.types.refused')   => '#ef4444',
                        __('time-off::filament/widgets/leave-type-widget.types.cancelled') => '#f97316',
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
