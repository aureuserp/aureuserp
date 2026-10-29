<?php

namespace Webkul\TimeOff\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Webkul\Support\Filament\Widgets\DashboardStatsWidget;
use Webkul\TimeOff\Enums\RequiresAllocation;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Models\Leave;
use Webkul\TimeOff\Models\LeaveAllocation;
use Webkul\TimeOff\Models\LeaveType;

class MyTimeOffWidget extends DashboardStatsWidget
{
    use HasWidgetShield;

    protected const ACCENTS = ['info', 'success', 'warning', 'primary'];

    protected static function getPagePermission(): ?string
    {
        return 'widget_time_off_my_time_off_widget';
    }

    protected function getHeading(): ?string
    {
        return __('time-off::filament/widgets/my-time-off-widget.heading.title');
    }

    protected function getStats(): array
    {
        $employeeId = Auth::user()?->employee?->id;
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        $leaveTypes = LeaveType::where('show_on_dashboard', '!=', 0)->get();

        $stats = [];

        $index = 0;

        foreach ($leaveTypes as $leaveType) {
            if ($leaveType->requires_allocation === RequiresAllocation::NO->value) {
                continue;
            }

            $availableDays = $this->calculateAvailableDays($employeeId, $leaveType->id, $endOfYear);

            $accent = self::ACCENTS[$index % count(self::ACCENTS)];

            $index++;

            $stats[] = Stat::make(__($leaveType->name), $availableDays['days'])
                ->description(__('time-off::filament/widgets/my-time-off-widget.stats.valid-until', ['date' => $endOfYear->format('Y-m-d')]))
                ->icon('heroicon-o-calendar-days')
                ->chart($this->leaveTakenSeries($employeeId, $leaveType->id, $startOfYear, $endOfYear))
                ->chartColor($accent)
                ->extraAttributes(['class' => 'dashboard-stat dashboard-stat--'.$accent]);
        }

        $pendingRequests = $this->calculatePendingRequests($employeeId);

        $stats[] = Stat::make(__('time-off::filament/widgets/my-time-off-widget.stats.pending-requests'), $pendingRequests)
            ->description(__('time-off::filament/widgets/my-time-off-widget.stats.time-off-requests'))
            ->icon('heroicon-o-clock')
            ->chart($this->pendingRequestsSeries($employeeId, $startOfYear, $endOfYear))
            ->chartColor('danger')
            ->extraAttributes(['class' => 'dashboard-stat dashboard-stat--danger']);

        return $stats;
    }

    /**
     * @return list<float>
     */
    protected function leaveTakenSeries($employeeId, $leaveTypeId, Carbon $start, Carbon $end): array
    {
        $query = Leave::where('employee_id', $employeeId)
            ->where('holiday_status_id', $leaveTypeId)
            ->where('state', '!=', 'refuse');

        return Trend::query($query)
            ->dateColumn('request_date_from')
            ->between(start: $start, end: $end)
            ->perMonth()
            ->sum('number_of_days')
            ->map(fn (TrendValue $value): float => round((float) $value->aggregate, 1))
            ->all();
    }

    /**
     * @return list<int>
     */
    protected function pendingRequestsSeries($employeeId, Carbon $start, Carbon $end): array
    {
        $query = Leave::where('employee_id', $employeeId)
            ->where('state', 'confirm');

        return Trend::query($query)
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count()
            ->map(fn (TrendValue $value): int => (int) $value->aggregate)
            ->all();
    }

    protected function calculateAvailableDays($employeeId, $leaveTypeId, $endDate)
    {
        $totalAllocated = LeaveAllocation::where('employee_id', $employeeId)
            ->where('holiday_status_id', $leaveTypeId)
            ->where('state', State::VALIDATE_TWO->value)
            ->where(function ($query) use ($endDate) {
                $query->where('date_to', '<=', $endDate)
                    ->orWhereNull('date_to');
            })
            ->sum('number_of_days');

        $totalTaken = Leave::where('employee_id', $employeeId)
            ->where('holiday_status_id', $leaveTypeId)
            ->where(function ($query) use ($endDate) {
                $query->where('request_date_to', '<=', $endDate)
                    ->orWhereNull('request_date_to');
            })
            ->where('state', '!=', 'refuse')
            ->sum('number_of_days');

        $availableDays = $totalAllocated - $totalTaken;

        return [
            'days' => number_format($availableDays, 1),
        ];
    }

    protected function calculatePendingRequests($employeeId)
    {
        return Leave::where('employee_id', $employeeId)
            ->where('state', 'confirm')
            ->count();
    }
}
