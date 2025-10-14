<?php

namespace Webkul\TimeOff\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Models\Leave;
use Webkul\TimeOff\Models\LeaveAllocation;
use Webkul\TimeOff\Models\LeaveType;

class MyTimeOffWidget extends BaseWidget
{
     use HasWidgetShield;
    protected function getHeading(): ?string
    {
        return __('time-off::filament/widgets/my-time-off-widget.heading.title');
    }

    protected function getStats(): array
    {
        $employeeId = Auth::user()?->employee?->id;
        $endOfYear = Carbon::now()->endOfYear();

        $leaveTypes = LeaveType::where('show_on_dashboard', '!=', 0)->get();

        $stats = [];

        foreach ($leaveTypes as $leaveType) {
            $availableDays = $this->calculateAvailableDays($employeeId, $leaveType->id, $endOfYear);

            $stats[] = Stat::make(__($leaveType->name), $availableDays['days'])
                ->description(__('time-off::filament/widgets/my-time-off-widget.stats.valid-until', ['date' => $endOfYear->format('Y-m-d')]))
                ->color(Color::generateV3Palette($leaveType->color));
        }

        $pendingRequests = $this->calculatePendingRequests($employeeId);

        $stats[] = Stat::make(__('Pending Requests'), $pendingRequests)
            ->description(__('time-off::filament/widgets/my-time-off-widget.stats.time-off-requests'))
            ->color('danger');

        return $stats;
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
