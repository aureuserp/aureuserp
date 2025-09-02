<?php

namespace Webkul\TimeOff\Filament\Widgets;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Actions;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Webkul\TimeOff\Enums\RequestDateFromPeriod;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Filament\Actions\HolidayAction;
use Webkul\TimeOff\Models\Leave;

class CalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = Leave::class;

    public function config(): array
    {
        return [
            'initialView'      => 'dayGridMonth',
            'headerToolbar'    => [
                'left'   => 'prev,next today',
                'center' => 'title',
                'right'  => 'dayGridMonth,timeGridWeek,listWeek',
            ],
            'height'           => 'auto',
            'aspectRatio'      => 1.8,
            'firstDay'         => 1,
            'moreLinkClick'    => 'popover',
            'eventDisplay'     => 'block',
            'displayEventTime' => false,
            'selectable'       => true,
            'selectMirror'     => true,
            'unselectAuto'     => false,
            'weekends'         => true,
            'dayHeaderFormat'  => [
                'weekday' => 'short',
            ],
            'businessHours'    => [
                'daysOfWeek' => [1, 2, 3, 4, 5],
                'startTime'  => '09:00',
                'endTime'    => '17:00',
            ],
            'dayCellClassNames' => 'function(info) {
                var isWeekend = info.date.getDay() === 0 || info.date.getDay() === 6;
                var isToday = info.date.toDateString() === new Date().toDateString();
                var classes = [];

                if (isToday) {
                    classes.push("today-highlight");
                }

                if (isWeekend) {
                    classes.push("weekend-day");
                } else {
                    classes.push("business-day");
                }

                return classes;
            }',
            'eventClassNames'   => 'function(info) {
                var classes = ["leave-event", "enhanced-event"];

                if (info.event.extendedProps.state) {
                    classes.push("state-" + info.event.extendedProps.state);
                }

                if (info.event.extendedProps.isHalfDay) {
                    classes.push("half-day-event");
                }

                if (info.event.extendedProps.priority) {
                    classes.push("priority-" + info.event.extendedProps.priority);
                }

                return classes;
            }',
        ];
    }

    private function calculateBusinessDays(Carbon $startDate, Carbon $endDate): int
    {
        $businessDays = 0;
        $current = $startDate->copy();

        while ($current <= $endDate) {
            if (! $current->isWeekend()) {
                $businessDays++;
            }

            $current->addDay();
        }

        return $businessDays;
    }

    private function calculateTotalDays(Carbon $startDate, Carbon $endDate): int
    {
        return $startDate->diffInDays($endDate) + 1;
    }

    private function getDurationInfo($data): array
    {
        if ($data['request_unit_half']) {
            return [
                'duration_display' => '0.5 day',
                'number_of_days'   => 0.5,
                'business_days'    => 0.5,
                'total_days'       => 0.5,
                'weekend_days'     => 0,
            ];
        }

        $startDate = Carbon::parse($data['request_date_from']);
        $endDate = $data['request_date_to'] ? Carbon::parse($data['request_date_to']) : $startDate;

        $businessDays = $this->calculateBusinessDays($startDate, $endDate);
        $totalDays = $this->calculateTotalDays($startDate, $endDate);
        $weekendDays = $totalDays - $businessDays;

        if ($weekendDays > 0) {
            $duration = trans_choice('time-off::filament/widgets/calendar-widget.modal-actions.edit.duration-display-with-weekend', $businessDays, [
                'count'   => $businessDays,
                'weekend' => $weekendDays,
            ]);
        } else {
            $duration = trans_choice('time-off::filament/widgets/calendar-widget.modal-actions.edit.duration-display', $businessDays, [
                'count' => $businessDays,
            ]);
        }

        return [
            'duration_display' => $duration,
            'number_of_days'   => $businessDays,
            'business_days'    => $businessDays,
            'total_days'       => $totalDays,
            'weekend_days'     => $weekendDays,
        ];
    }

    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('time-off::filament/widgets/calendar-widget.modal-actions.edit.title'))
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->action(function ($data, $record) {
                    $user = Auth::user();
                    $employee = $user->employee;

                    if ($employee) {
                        $data['employee_id'] = $employee->id;
                    }

                    if ($employee->department) {
                        $data['department_id'] = $employee->department?->id;
                    } else {
                        $data['department_id'] = null;
                    }

                    if ($employee->calendar) {
                        $data['calendar_id'] = $employee->calendar->id;
                        $data['number_of_hours'] = $employee->calendar->hours_per_day;
                    }

                    if ($user) {
                        $data['user_id'] = $user->id;
                        $data['company_id'] = $user->default_company_id;
                        $data['employee_company_id'] = $user->default_company_id;
                    }

                    $durationInfo = $this->getDurationInfo($data);
                    $data = array_merge($data, $durationInfo);

                    $data['creator_id'] = Auth::user()->id;
                    $data['state'] = State::CONFIRM->value;
                    $data['request_date_from'] = $data['request_date_from'] ?? null;
                    $data['request_date_to'] = $data['request_date_to'] ?? null;

                    $record->update($data);

                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/widgets/calendar-widget.modal-actions.edit.notification.title'))
                        ->body(__('time-off::filament/widgets/calendar-widget.modal-actions.edit.notification.body'))
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->send();
                })
                ->mountUsing(
                    function (Forms\Form $form, array $arguments, $livewire) {
                        $leave = $livewire->record;

                        $form->fill([
                            ...$leave->toArray() ?? [],
                            'request_date_from' => $arguments['event']['start'] ?? $leave->request_date_from,
                            'request_date_to'   => $arguments['event']['end'] ?? $leave->request_date_to,
                        ]);
                    }
                ),

            Actions\DeleteAction::make()
                ->label(__('time-off::filament/widgets/calendar-widget.modal-actions.delete.title'))
                ->modalIcon('heroicon-o-trash')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    protected function viewAction(): Action
    {
        return Actions\ViewAction::make()
            ->modalIcon('heroicon-o-eye')
            ->icon('heroicon-o-eye')
            ->color('info')
            ->label(__('time-off::filament/widgets/calendar-widget.view-action.title'))
            ->modalDescription(__('time-off::filament/widgets/calendar-widget.view-action.description'))
            ->infolist($this->infolist());
    }

    protected function headerActions(): array
    {
        return [
            HolidayAction::make(),
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->modalIcon('heroicon-o-calendar-days')
                ->label(__('time-off::filament/widgets/calendar-widget.header-actions.create.title'))
                ->modalDescription(__('time-off::filament/widgets/calendar-widget.header-actions.create.description'))
                ->color('success')
                ->action(function ($data) {
                    $user = Auth::user();
                    $employee = $user->employee;

                    if ($employee) {
                        $data['employee_id'] = $employee->id;
                    } else {
                        Notification::make()
                            ->danger()
                            ->title(__('time-off::filament/widgets/calendar-widget.header-actions.create.employee-not-found.notification.title'))
                            ->body(__('time-off::filament/widgets/calendar-widget.header-actions.create.employee-not-found.notification.body'))
                            ->icon('heroicon-o-exclamation-triangle')
                            ->send();

                        return;
                    }

                    if ($employee?->department) {
                        $data['department_id'] = $employee->department?->id;
                    } else {
                        $data['department_id'] = null;
                    }

                    if ($employee->calendar) {
                        $data['calendar_id'] = $employee->calendar->id;
                        $data['number_of_hours'] = $employee->calendar->hours_per_day;
                    }

                    if ($user) {
                        $data['user_id'] = $user->id;
                        $data['company_id'] = $user->default_company_id;
                        $data['employee_company_id'] = $user->default_company_id;
                    }

                    $durationInfo = $this->getDurationInfo($data);
                    $data = array_merge($data, $durationInfo);

                    $data['creator_id'] = Auth::user()->id;
                    $data['state'] = State::CONFIRM->value;
                    $data['date_from'] = $data['request_date_from'];
                    $data['date_to'] = isset($data['request_date_to']) ? $data['request_date_to'] : null;

                    Leave::create($data);

                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/widgets/calendar-widget.header-actions.create.success.notification.title'))
                        ->body(__('time-off::filament/widgets/calendar-widget.header-actions.create.success.notification.body'))
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->send();
                })
                ->mountUsing(
                    function (Forms\Form $form, array $arguments) {
                        $form->fill($arguments);
                    }
                ),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make(__('time-off::filament/widgets/calendar-widget.form.title'))
                ->label(__('time-off::filament/widgets/calendar-widget.form.title'))
                ->description(__('time-off::filament/widgets/calendar-widget.form.description'))
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Forms\Components\Select::make('holiday_status_id')
                        ->label(__('time-off::filament/widgets/calendar-widget.form.fields.time-off-type'))
                        ->relationship('holidayStatus', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpanFull()
                        ->placeholder(__('time-off::filament/widgets/calendar-widget.form.fields.time-off-type-placeholder'))
                        ->helperText(__('time-off::filament/widgets/calendar-widget.form.fields.time-off-type-helper')),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('request_date_from')
                                ->native(false)
                                ->label(__('time-off::filament/widgets/calendar-widget.form.fields.request-date-from'))
                                ->default(now())
                                ->required()
                                ->live()
                                ->prefixIcon('heroicon-o-calendar')
                                ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                                    if (
                                        $state
                                        && ! $get('request_unit_half')
                                    ) {
                                        $this->updateDurationCalculation($set, $get);
                                    }
                                }),

                            Forms\Components\DatePicker::make('request_date_to')
                                ->native(false)
                                ->label('To Date')
                                ->default(now())
                                ->hidden(fn (Get $get) => $get('request_unit_half'))
                                ->required(fn (Get $get) => ! $get('request_unit_half'))
                                ->live()
                                ->prefixIcon('heroicon-o-calendar')
                                ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                                    if (
                                        $state
                                        && $get('request_date_from')
                                    ) {
                                        $this->updateDurationCalculation($set, $get);
                                    }
                                }),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('request_unit_half')
                                ->live()
                                ->label(__('time-off::filament/widgets/calendar-widget.form.fields.half-day'))
                                ->helperText(__('time-off::filament/widgets/calendar-widget.form.fields.half-day-helper'))
                                ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                                    if ($state) {
                                        $set('duration_info', '0.5 day');
                                    } else {
                                        $this->updateDurationCalculation($set, $get);
                                    }
                                }),

                            Forms\Components\Select::make('request_date_from_period')
                                ->label(__('time-off::filament/widgets/calendar-widget.form.fields.period'))
                                ->options(RequestDateFromPeriod::class)
                                ->default(RequestDateFromPeriod::MORNING->value)
                                ->native(false)
                                ->visible(fn (Get $get) => $get('request_unit_half'))
                                ->required(fn (Get $get) => $get('request_unit_half'))
                                ->prefixIcon('heroicon-o-sun'),
                        ]),

                    Forms\Components\Placeholder::make('duration_info')
                        ->label(__('time-off::filament/widgets/calendar-widget.form.fields.duration'))
                        ->live()
                        ->content(function (Get $get): string {
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

                    Forms\Components\Textarea::make('private_name')
                        ->label(__('time-off::filament/widgets/calendar-widget.form.fields.description'))
                        ->placeholder(__('time-off::filament/widgets/calendar-widget.form.fields.description-placeholder'))
                        ->rows(3)
                        ->columnSpanFull()
                        ->helperText(__('time-off::filament/widgets/calendar-widget.form.fields.description-helper')),
                ]),
        ];
    }

    private function updateDurationCalculation(Forms\Set $set, Get $get): void
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

    public function infolist(): array
    {
        return [
            Infolists\Components\Section::make(__('time-off::filament/widgets/calendar-widget.infolist.title'))
                ->label(__('time-off::filament/widgets/calendar-widget.infolist.title'))
                ->description(__('time-off::filament/widgets/calendar-widget.infolist.description'))
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Infolists\Components\Grid::make(2)
                        ->schema([
                            Infolists\Components\TextEntry::make('holidayStatus.name')
                                ->label(__('time-off::filament/widgets/calendar-widget.infolist.entries.time-off-type'))
                                ->icon('heroicon-o-clock')
                                ->badge()
                                ->color('primary')
                                ->size('lg'),

                            Infolists\Components\TextEntry::make('state')
                                ->label(__('time-off::filament/widgets/calendar-widget.infolist.entries.status'))
                                ->badge()
                                ->size('lg')
                                ->formatStateUsing(fn ($state) => $this->getStateLabel($state))
                                ->color(fn ($state) => $this->getStateColor($state, true))
                                ->icon(fn ($state) => $this->getStateIcon($state)),
                        ]),

                    Infolists\Components\Grid::make(2)
                        ->schema([
                            Infolists\Components\TextEntry::make('request_date_from')
                                ->label(__('time-off::filament/widgets/calendar-widget.infolist.entries.request-date-from'))
                                ->date('F j, Y')
                                ->icon('heroicon-o-calendar-days')
                                ->badge()
                                ->color('info'),

                            Infolists\Components\TextEntry::make('request_date_to')
                                ->label(__('time-off::filament/widgets/calendar-widget.infolist.entries.request-date-to'))
                                ->date('F j, Y')
                                ->icon('heroicon-o-calendar-days')
                                ->badge()
                                ->color('info'),
                        ]),

                    Infolists\Components\TextEntry::make('number_of_days')
                        ->label(__('time-off::filament/widgets/calendar-widget.infolist.entries.duration'))
                        ->formatStateUsing(function ($state, $record) {
                            if ($record->request_unit_half) {
                                return '0.5 day';
                            }

                            $startDate = Carbon::parse($record->request_date_from);
                            $endDate = $record->request_date_to ? Carbon::parse($record->request_date_to) : $startDate;

                            $businessDays = $this->calculateBusinessDays($startDate, $endDate);
                            $totalDays = $this->calculateTotalDays($startDate, $endDate);
                            $weekendDays = $totalDays - $businessDays;

                            $duration = $businessDays.' working day'.($businessDays !== 1 ? 's' : '');

                            if ($weekendDays > 0) {
                                $duration .= ' (+ '.$weekendDays.' weekend day'.($weekendDays !== 1 ? 's' : '').')';
                            }

                            return $duration;
                        })
                        ->icon('heroicon-o-clock')
                        ->badge()
                        ->color('success')
                        ->size('lg'),

                    Infolists\Components\TextEntry::make('private_name')
                        ->label(__('time-off::filament/widgets/calendar-widget.infolist.entries.description'))
                        ->icon('heroicon-o-document-text')
                        ->placeholder(__('time-off::filament/widgets/calendar-widget.infolist.entries.description-placeholder'))
                        ->columnSpanFull()
                        ->badge()
                        ->color('gray'),
                ]),
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $user = Auth::user();

        return Leave::query()
            ->where('user_id', $user->id)
            ->orWhere('employee_id', $user?->employee?->id)
            ->where('request_date_from', '>=', $fetchInfo['start'])
            ->where('request_date_to', '<=', $fetchInfo['end'])
            ->with('holidayStatus', 'user')
            ->get()
            ->map(function (Leave $leave) {
                $startDate = Carbon::parse($leave->request_date_from);
                $endDate = $leave->request_date_to ? Carbon::parse($leave->request_date_to) : $startDate;

                $businessDays = $this->calculateBusinessDays($startDate, $endDate);
                $totalDays = $this->calculateTotalDays($startDate, $endDate);
                $weekendDays = $totalDays - $businessDays;

                $title = "{$leave->holidayStatus->name} {$leave->user->name}";

                if ($leave->request_unit_half) {
                    $title .= ' (0.5 day)';
                } else {
                    $title .= ' ('.$businessDays.'d)';
                    if ($weekendDays > 0) {
                        $title .= ' +'.$weekendDays;
                    }
                }

                return [
                    'id'              => $leave->id,
                    'title'           => $title,
                    'start'           => $leave->request_date_from,
                    'end'             => $leave->request_date_to ? Carbon::parse($leave->request_date_to)->addDay()->toDateString() : null,
                    'allDay'          => true,
                    'backgroundColor' => $leave->holidayStatus?->color,
                    'borderColor'     => $leave->holidayStatus?->color,
                    'textColor'       => '#ffffff',
                    'extendedProps'   => [
                        'state'         => $leave->state,
                        'business_days' => $businessDays,
                        'weekend_days'  => $weekendDays,
                        'total_days'    => $totalDays,
                        'description'   => $leave->private_name,
                        'type'          => $leave->holidayStatus->name,
                        'isHalfDay'     => $leave->request_unit_half,
                        'priority'      => $this->getEventPriority($leave->state),
                    ],
                ];
            })
            ->all();
    }

    private function getEventPriority(string $state): string
    {
        return match ($state) {
            State::REFUSE->value       => 'low',
            State::VALIDATE_ONE->value => 'medium',
            State::CONFIRM->value      => 'high',
            State::VALIDATE_TWO->value => 'highest',
            default                    => 'normal'
        };
    }

    private function getStateLabel(string $state): string
    {
        return match ($state) {
            State::VALIDATE_ONE->value => State::VALIDATE_ONE->getLabel(),
            State::VALIDATE_TWO->value => State::VALIDATE_TWO->getLabel(),
            State::CONFIRM->value      => State::CONFIRM->getLabel(),
            State::REFUSE->value       => State::REFUSE->getLabel(),
        };
    }

    private function getStateIcon(string $state): string
    {
        return match ($state) {
            State::VALIDATE_ONE->value => 'heroicon-o-magnifying-glass',
            State::VALIDATE_TWO->value => 'heroicon-o-check-circle',
            State::CONFIRM->value      => 'heroicon-o-clock',
            State::REFUSE->value       => 'heroicon-o-x-circle',
            default                    => 'heroicon-o-document',
        };
    }

    private function getStateColor($state, $isFilament = false): string
    {
        if ($isFilament) {
            return match ($state) {
                State::VALIDATE_ONE->value => 'info',
                State::VALIDATE_TWO->value => 'success',
                State::CONFIRM->value      => 'warning',
                State::REFUSE->value       => 'danger',
                default                    => 'gray',
            };
        }

        return match ($state) {
            State::VALIDATE_ONE->value => '#3B82F6',
            State::VALIDATE_TWO->value => '#10B981',
            State::CONFIRM->value      => '#F59E0B',
            State::REFUSE->value       => '#EF4444',
            default                    => '#6B7280',
        };
    }

    public function onDateSelect(string $start, ?string $end, bool $allDay, ?array $view, ?array $resource): void
    {
        $startDate = Carbon::parse($start);
        $endDate = $end ? Carbon::parse($end)->subDay() : $startDate;

        $this->mountAction('create', [
            'request_date_from' => $startDate->toDateString(),
            'request_date_to'   => $endDate->toDateString(),
        ]);
    }
}
