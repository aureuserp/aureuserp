<?php

namespace Webkul\TimeOff\Traits;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Models\Employee;
use Webkul\TimeOff\Enums\RequestDateFromPeriod;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Models\Leave;
use Webkul\TimeOff\Models\LeaveAllocation;
use Webkul\TimeOff\Models\LeaveType;

trait TimeOffHelper
{
    public function mutateTimeOffData(array $data, ?int $excludeRecordId = null, ?Action $action = null): array
    {
        $this->updateEmployeeAndCompanyData($data);
        $this->calculateBusinessDaysAndNumbers($data);

        $this->handleLeaveOverlap($data, $excludeRecordId, $action);

        $this->handleLeaveAllocation($data, $action);

        $data['creator_id'] = Auth::user()->id;
        $data['state'] = State::CONFIRM->value;
        $data['date_from'] = $data['request_date_from'] ?? null;
        $data['date_to'] = $data['request_date_to'] ?? null;

        return $data;
    }

    public function getFormSchema($isVisible = null): array
    {
        return [
            Section::make()
                ->schema([
                    Select::make('employee_id')
                        ->relationship('employee', 'name')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->visible(fn (Get $get) => $isVisible ?? false)
                        ->afterStateUpdated(function (Set $set, $state) {
                            if ($state) {
                                $employee = Employee::find($state);

                                if ($employee->department) {
                                    $set('department_id', $employee->department->id);
                                } else {
                                    $set('department_id', null);
                                }
                            }
                        })
                        ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.employee-name'))
                        ->required(),
                    Select::make('department_id')
                        ->relationship('department', 'name')
                        ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.department-name'))
                        ->searchable()
                        ->visible(fn (Get $get) => $isVisible ?? false)
                        ->preload()
                        ->required(),

                    Select::make('holiday_status_id')
                        ->label(__('time-off::filament/widgets/calendar-widget.form.fields.time-off-type'))
                        ->relationship('holidayStatus', 'name')
                        ->required()
                        ->columnSpanFull()
                        ->placeholder(__('time-off::filament/widgets/calendar-widget.form.fields.time-off-type-placeholder'))
                        ->helperText(__('time-off::filament/widgets/calendar-widget.form.fields.time-off-type-helper')),

                    Grid::make(2)
                        ->schema([
                            DatePicker::make('request_date_from')
                                ->native(false)
                                ->label(__('time-off::filament/widgets/calendar-widget.form.fields.request-date-from'))
                                ->required()
                                ->live()
                                ->prefixIcon('heroicon-o-calendar')
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    if (blank($state)) {
                                        $set('request_date_to', null);
                                    }

                                    if ($state && ! $get('request_unit_half')) {
                                        $this->updateDurationCalculation($set, $get);
                                    }
                                }),

                            DatePicker::make('request_date_to')
                                ->native(false)
                                ->label('To Date')
                                ->hidden(fn (Get $get) => $get('request_unit_half'))
                                ->required(fn (Get $get) => ! $get('request_unit_half'))
                                ->live()
                                ->prefixIcon('heroicon-o-calendar')
                                ->disabled(fn (Get $get) => blank($get('request_date_from')))
                                ->minDate(fn (Get $get) => $get('request_date_from'))
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    if ($state && $get('request_date_from')) {
                                        $this->updateDurationCalculation($set, $get);
                                    }
                                }),
                        ]),

                    Grid::make(2)
                        ->schema([
                            Toggle::make('request_unit_half')
                                ->live()
                                ->label(__('time-off::filament/widgets/calendar-widget.form.fields.half-day'))
                                ->helperText(__('time-off::filament/widgets/calendar-widget.form.fields.half-day-helper'))
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    if ($state) {
                                        $set('duration_info', '0.5 day');
                                    } else {
                                        $this->updateDurationCalculation($set, $get);
                                    }
                                }),

                            Select::make('request_date_from_period')
                                ->label(__('time-off::filament/widgets/calendar-widget.form.fields.period'))
                                ->options(RequestDateFromPeriod::class)
                                ->default(RequestDateFromPeriod::MORNING)
                                ->native(false)
                                ->visible(fn (Get $get) => $get('request_unit_half'))
                                ->required(fn (Get $get) => $get('request_unit_half'))
                                ->prefixIcon('heroicon-o-sun'),
                        ]),

                    TextEntry::make('duration_info')
                        ->label(__('time-off::filament/widgets/calendar-widget.form.fields.duration'))
                        ->live()
                        ->state(function (Get $get): string {
                            if ($get('request_unit_half')) {
                                return '0.5 day';
                            }

                            $startDate = $get('request_date_from');
                            $endDate = $get('request_date_to');

                            if (! $startDate) {
                                return __('time-off::filament/widgets/calendar-widget.form.fields.please-select-dates');
                            }

                            $start = Carbon::parse($startDate);
                            $end = $endDate ? Carbon::parse($endDate) : $start;

                            $businessDays = $this->calculateBusinessDays($start, $end);
                            $totalDays = $this->calculateTotalDays($start, $end);
                            $weekendDays = $totalDays - $businessDays;

                            $duration = $businessDays.' working day'.($businessDays !== 1 ? 's' : '');

                            if ($weekendDays > 0) {
                                $duration .= ' (+ '.$weekendDays.' weekend day'.($weekendDays !== 1 ? 's' : '').')';
                            }

                            return $duration;
                        })
                        ->extraAttributes(['class' => 'font-medium text-primary-600 bg-primary-50 p-3 rounded-lg border border-primary-200']),

                    Textarea::make('private_name')
                        ->label(__('time-off::filament/widgets/calendar-widget.form.fields.description'))
                        ->placeholder(__('time-off::filament/widgets/calendar-widget.form.fields.description-placeholder'))
                        ->rows(3)
                        ->columnSpanFull()
                        ->helperText(__('time-off::filament/widgets/calendar-widget.form.fields.description-helper')),
                ])->columnSpanFull(),
        ];
    }

    public function getDurationInfo(array $data): array
    {
        if (! empty($data['request_unit_half'])) {
            return [
                'duration_display' => '0.5 day',
                'number_of_days'   => 0.5,
                'business_days'    => 0.5,
                'total_days'       => 0.5,
                'weekend_days'     => 0,
            ];
        }

        $startDate = Carbon::parse($data['request_date_from']);
        $endDate = ! empty($data['request_date_to'])
            ? Carbon::parse($data['request_date_to'])
            : $startDate;

        $businessDays = $this->calculateBusinessDays($startDate, $endDate);
        $totalDays = $this->calculateTotalDays($startDate, $endDate);
        $weekendDays = $totalDays - $businessDays;

        $durationDisplay = $businessDays.' working day'.($businessDays !== 1 ? 's' : '');

        if ($weekendDays > 0) {
            $durationDisplay .= ' (+ '.$weekendDays.' weekend day'.($weekendDays !== 1 ? 's' : '').')';
        }

        return [
            'duration_display' => $durationDisplay,
            'number_of_days'   => $businessDays,
            'business_days'    => $businessDays,
            'total_days'       => $totalDays,
            'weekend_days'     => $weekendDays,
        ];
    }

    /**
     * Overlap check
     *
     * @throws \Exception
     */
    private function handleLeaveOverlap(array &$data, ?int $excludeRecordId = null, ?Action $action = null): void
    {
        $employee = Employee::find($data['employee_id']);
        if (! $employee) {
            Notification::make()
                ->danger()
                ->title(__('time-off::filament/widgets/overview-calendar-widget.header-actions.create.employee-not-found.notification.title'))
                ->body(__('time-off::filament/widgets/overview-calendar-widget.header-actions.create.employee-not-found.notification.body'))
                ->send();

            if ($action) {
                $action->halt();
            } else {
                $this->halt();
            }

            return;
        }

        $overlap = $this->checkForOverlappingLeave(
            $employee->id,
            $data['request_date_from'],
            $data['request_date_to'] ?? $data['request_date_from'],
            $excludeRecordId
        );

        if ($overlap) {
            Notification::make()
                ->danger()
                ->title(__('time-off::filament/clusters/my-time/resources/my-time-off/pages/create-time-off.notification.overlap.title'))
                ->body(__('time-off::filament/clusters/my-time/resources/my-time-off/pages/create-time-off.notification.overlap.body'))
                ->send();

            if ($action) {
                $action->halt();
            } else {
                $this->halt();
            }
        }
    }

    /**
     * Leave allocation check
     *
     * @throws \Exception
     */
    private function handleLeaveAllocation(array &$data, ?Action $action = null): void
    {
        $employee = Employee::find($data['employee_id']);
        if (! $employee) {
            return;
        }

        $leaveTypeId = $data['holiday_status_id'] ?? null;
        if (! $leaveTypeId) {
            return;
        }

        $leaveType = LeaveType::find($leaveTypeId);
        if (! $leaveType || ! $leaveType->requires_allocation) {
            return;
        }

        $requestedDays = $data['number_of_days'];
        $endOfYear = Carbon::now()->endOfYear();

        $totalAllocated = LeaveAllocation::where('employee_id', $employee->id)
            ->where('holiday_status_id', $leaveTypeId)
            ->where('state', State::VALIDATE_TWO->value)
            ->where(function ($q) use ($endOfYear) {
                $q->where('date_to', '<=', $endOfYear)
                    ->orWhereNull('date_to');
            })
            ->sum('number_of_days');

        $totalTaken = Leave::where('employee_id', $employee->id)
            ->where('holiday_status_id', $leaveTypeId)
            ->where('state', '!=', State::REFUSE->value)
            ->where(fn ($q) => true)  
            ->sum('number_of_days');

        $availableBalance = round($totalAllocated - $totalTaken, 1);

        if ($totalAllocated <= 0) {
            Notification::make()
                ->danger()
                ->title(__('time-off::filament/clusters/my-time/resources/my-time-off/pages/create-time-off.notification.leave_request_denied_no_allocation.title'))
                ->body(__('time-off::filament/clusters/my-time/resources/my-time-off/pages/create-time-off.notification.leave_request_denied_no_allocation.body', ['leaveType' => $leaveType->name]))
                ->send();

            if ($action) {
                $action->halt();
            } else {
                $this->halt();
            }
        }

        if ($requestedDays > $availableBalance) {
            Notification::make()
                ->danger()
                ->title(__('time-off::filament/clusters/my-time/resources/my-time-off/pages/create-time-off.notification.leave_request_denied_insufficient_balance.title'))
                ->body(__('time-off::filament/clusters/my-time/resources/my-time-off/pages/create-time-off.notification.leave_request_denied_insufficient_balance.body', [
                    'available_balance' => $availableBalance,
                    'requested_days'    => $requestedDays,
                ]))
                ->send();

            if ($action) {
                $action->halt();
            } else {
                $this->halt();
            }
        }
    }

    /**
     * Compute business days between two Carbon dates
     */
    private function calculateBusinessDays(Carbon $start, Carbon $end): int
    {
        $days = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            if (! $current->isWeekend()) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }

    /**
     * Compute total days inclusive
     */
    private function calculateTotalDays(Carbon $start, Carbon $end): int
    {
        return $start->diffInDays($end) + 1;
    }

    /**
     * Check overlapping leave
     */
    private function checkForOverlappingLeave(int $employeeId, string $startDate, ?string $endDate, ?int $excludeRecordId = null): bool
    {
        $start = Carbon::parse($startDate);
        $end = $endDate ? Carbon::parse($endDate) : $start;

        $query = Leave::where('employee_id', $employeeId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('date_from', [$start, $end])
                    ->orWhereBetween('date_to', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('date_from', '<=', $start)
                            ->where('date_to', '>=', $end);
                    });
            });

        if ($excludeRecordId) {
            $query->where('id', '!=', $excludeRecordId);
        }

        return $query->exists();
    }

    private function updateDurationCalculation(Set $set, Get $get): void
    {
        $startDate = $get('request_date_from');
        $endDate = $get('request_date_to');

        if (! $startDate) {
            $set('duration_info', 'Please select dates');

            return;
        }

        $start = Carbon::parse($startDate);
        $end = $endDate ? Carbon::parse($endDate) : $start;

        $businessDays = $this->calculateBusinessDays($start, $end);
        $totalDays = $this->calculateTotalDays($start, $end);
        $weekendDays = $totalDays - $businessDays;

        $duration = $businessDays.' working day'.($businessDays !== 1 ? 's' : '');

        if ($weekendDays > 0) {
            $duration .= ' (+ '.$weekendDays.' weekend day'.($weekendDays !== 1 ? 's' : '').')';
        }

        $set('duration_info', $duration);
    }

    /**
     * Calculate and set duration-related values in the given data array.
     */
    private function calculateBusinessDaysAndNumbers(array &$data): void
    {
        $info = $this->getDurationInfo($data);

        $data['duration_display'] = $info['duration_display'];
        $data['number_of_days'] = $info['number_of_days'];
        $data['business_days'] = $info['business_days'];
        $data['total_days'] = $info['total_days'];
        $data['weekend_days'] = $info['weekend_days'];
    }

    private function updateEmployeeAndCompanyData(array &$data): void
    {

        if (! empty($data['employee_id'])) {
            $employee = Employee::find($data['employee_id']);
            $user = $employee->user;
        } else {
            $user = Auth::user();
            $employee = $user->employee;
        }

        if ($employee) {
            $data['employee_id'] = $employee->id;

            if (! empty($data['department_id'])) {
                $data['department_id'] = $data['department_id'];
            } elseif ($employee->department) {
                $data['department_id'] = $employee->department->id;
            } else {
                $data['department_id'] = null;
            }

            if ($employee->calendar) {
                $data['calendar_id'] = $employee->calendar->id;
                $data['number_of_hours'] = $employee->calendar->hours_per_day;
            }
        }

        if ($user) {
            $data['user_id'] = $user->id;
            $data['company_id'] = $user->default_company_id;
            $data['employee_company_id'] = $user->default_company_id;
        }
    }
}
