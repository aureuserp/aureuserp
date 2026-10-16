<?php

namespace Webkul\Attendance\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Webkul\Attendance\Models\Attendance;

class AttendanceStatsWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static function getPagePermission(): ?string
    {
        return 'widget_attendance_attendance_stats_widget';
    }

    /**
     * Month-scoped attendance aggregates.
     *
     * @return array{records: int, employees: int, hours: float, open: int}
     */
    public static function monthlyStats(?Carbon $month = null): array
    {
        $month ??= Carbon::now();

        $from = $month->copy()->startOfMonth()->toDateString();
        $to = $month->copy()->endOfMonth()->toDateString();

        $rows = Attendance::query()
            ->whereDate('work_date', '>=', $from)
            ->whereDate('work_date', '<=', $to)
            ->get(['employee_id', 'check_in', 'check_out']);

        $minutes = $rows
            ->filter(fn (Attendance $row) => $row->check_out !== null)
            ->sum(fn (Attendance $row) => $row->worked_minutes);

        return [
            'records'   => $rows->count(),
            'employees' => $rows->pluck('employee_id')->unique()->count(),
            'hours'     => round($minutes / 60, 1),
            'open'      => $rows->whereNull('check_out')->count(),
        ];
    }

    protected function getHeading(): ?string
    {
        return __('attendance::filament/widgets/attendance-stats.heading');
    }

    protected function getStats(): array
    {
        $stats = static::monthlyStats();

        return [
            Stat::make(__('attendance::filament/widgets/attendance-stats.stats.records'), $stats['records'])
                ->description(__('attendance::filament/widgets/attendance-stats.stats.records-description'))
                ->color('info'),
            Stat::make(__('attendance::filament/widgets/attendance-stats.stats.employees'), $stats['employees'])
                ->description(__('attendance::filament/widgets/attendance-stats.stats.employees-description'))
                ->color('success'),
            Stat::make(__('attendance::filament/widgets/attendance-stats.stats.hours'), $stats['hours'])
                ->description(__('attendance::filament/widgets/attendance-stats.stats.hours-description'))
                ->color('warning'),
            Stat::make(__('attendance::filament/widgets/attendance-stats.stats.open'), $stats['open'])
                ->description(__('attendance::filament/widgets/attendance-stats.stats.open-description'))
                ->color('danger'),
        ];
    }
}
