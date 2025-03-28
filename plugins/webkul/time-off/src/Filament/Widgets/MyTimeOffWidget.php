<?php

namespace Webkul\TimeOff\Filament\Widgets;

use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Webkul\TimeOff\Models\Leave;
use Webkul\TimeOff\Models\LeaveAllocation;
use Webkul\TimeOff\Models\LeaveType;

class MyTimeOffWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $employeeId = Auth::user()?->employee?->id;
        $endOfYear = Carbon::now()->endOfYear();

        $leaveTypes = LeaveType::all();

        $stats = [];

        foreach ($leaveTypes as $leaveType) {
            $availableDays = $this->calculateAvailableDays($employeeId, $leaveType->id, $endOfYear);

            $stats[] = Stat::make(__($leaveType->name), $availableDays['days'])
                ->description(__('time-off::filament/widgets/my-time-off-widget.stats.valid-until', ['date' => $endOfYear->format('Y-m-d')]))
                ->color(Color::hex($leaveType->color));
        }

        $pendingRequests = $this->calculatePendingRequests($employeeId);

        $stats[] = Stat::make(__('Pending Requests'), $pendingRequests)
            ->description(__('time-off::filament/widgets/my-time-off-widget.stats.time-off-requests'))
            ->color('danger');

        return $stats;
    }

    protected function calculateAvailableDays($employeeId, $leaveTypeId, $endDate)
    {
        $allocation = LeaveAllocation::where('employee_id', $employeeId)
            ->where('holiday_status_id', $leaveTypeId)
            ->where('date_to', '<=', $endDate)
            ->latest('created_at')
            ->first();

        return [
            'days' => $allocation ? $allocation->number_of_days : 0,
        ];
    }

    protected function calculatePendingRequests($employeeId)
    {
        return Leave::where('employee_id', $employeeId)
            ->where('state', 'confirm')
            ->count();
    }
}
