<?php

namespace Webkul\TimeOff\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\FullCalendar\Filament\Actions\CreateAction;
use Webkul\FullCalendar\Filament\Actions\DeleteAction;
use Webkul\FullCalendar\Filament\Actions\EditAction;
use Webkul\FullCalendar\Filament\Actions\ViewAction;
use Webkul\FullCalendar\Filament\Widgets\FullCalendarWidget;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Filament\Actions\HolidayAction;
use Webkul\TimeOff\Models\Leave;
use Webkul\TimeOff\Traits\TimeOffHelper;

class CalendarWidget extends FullCalendarWidget
{
    use TimeOffHelper;

     use HasWidgetShield;
    public Model|string|null $model = Leave::class;

    public function getHeading(): string|Htmlable|null
    {
        return __('time-off::filament/widgets/calendar-widget.heading.title');
    }

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

    public function modalActions(): array
    {
        return [
            EditAction::make()
                ->label(__('time-off::filament/widgets/calendar-widget.modal-actions.edit.title'))
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->action(function ($data, $record, EditAction $action) {
                    $data = $this->mutateTimeOffData($data, $this->record?->id, $action);

                    $record->update($data);

                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/widgets/calendar-widget.modal-actions.edit.notification.title'))
                        ->body(__('time-off::filament/widgets/calendar-widget.modal-actions.edit.notification.body'))
                        ->send();
                    $action->cancel();
                })
                ->mountUsing(
                    function (Schema $schema, array $arguments, $livewire) {
                        $leave = $livewire->record;

                        $schema->fill([
                            ...$leave->toArray() ?? [],
                            'request_date_from' => $arguments['event']['start'] ?? $leave->request_date_from,
                            'request_date_to'   => $arguments['event']['end'] ?? $leave->request_date_to,
                        ]);
                    }
                ),

            DeleteAction::make()
                ->label(__('time-off::filament/widgets/calendar-widget.modal-actions.delete.title'))
                ->modalIcon('heroicon-o-trash')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    protected function viewAction(): Action
    {
        return ViewAction::make()
            ->modalIcon('heroicon-o-eye')
            ->icon('heroicon-o-eye')
            ->color('info')
            ->label(__('time-off::filament/widgets/calendar-widget.view-action.title'))
            ->modalDescription(__('time-off::filament/widgets/calendar-widget.view-action.description'))
            ->schema($this->infolist());
    }

    protected function headerActions(): array
    {
        return [
            HolidayAction::make(),
            CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->modalIcon('heroicon-o-calendar-days')
                ->label(__('time-off::filament/widgets/calendar-widget.header-actions.create.title'))
                ->modalDescription(__('time-off::filament/widgets/calendar-widget.header-actions.create.description'))
                ->color('success')
                ->action(function ($data, CreateAction $action) {
                    $data = $this->mutateTimeOffData($data, $this->record?->id, $action);

                    Leave::create($data);

                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/widgets/calendar-widget.header-actions.create.notification.title'))
                        ->body(__('time-off::filament/widgets/calendar-widget.header-actions.create.notification.body'))
                        ->send();

                    $action->cancel();
                })
                ->mountUsing(fn (Schema $schema, array $arguments) => $schema->fill($arguments)),
        ];
    }

    public function infolist(): array
    {
        return [
            Section::make(__('time-off::filament/widgets/calendar-widget.infolist.title'))
                ->label(__('time-off::filament/widgets/calendar-widget.infolist.title'))
                ->description(__('time-off::filament/widgets/calendar-widget.infolist.description'))
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('holidayStatus.name')
                                ->label(__('time-off::filament/widgets/calendar-widget.infolist.entries.time-off-type'))
                                ->icon('heroicon-o-clock')
                                ->badge()
                                ->color('primary')
                                ->size('lg'),

                            TextEntry::make('state')
                                ->label(__('time-off::filament/widgets/calendar-widget.infolist.entries.status'))
                                ->badge()
                                ->size('lg')
                                ->formatStateUsing(fn ($state) => $this->getStateLabel($state))
                                ->color(fn ($state) => $this->getStateColor($state, true))
                                ->icon(fn ($state) => $this->getStateIcon($state)),
                        ]),

                    Grid::make(2)
                        ->schema([
                            TextEntry::make('request_date_from')
                                ->label(__('time-off::filament/widgets/calendar-widget.infolist.entries.request-date-from'))
                                ->date('F j, Y')
                                ->icon('heroicon-o-calendar-days')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('request_date_to')
                                ->label(__('time-off::filament/widgets/calendar-widget.infolist.entries.request-date-to'))
                                ->date('F j, Y')
                                ->icon('heroicon-o-calendar-days')
                                ->badge()
                                ->color('info'),
                        ]),

                    TextEntry::make('number_of_days')
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

                    TextEntry::make('private_name')
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

    private function getEventPriority(State $state): string
    {
        return match ($state) {
            State::REFUSE              => 'low',
            State::VALIDATE_ONE        => 'medium',
            State::CONFIRM             => 'high',
            State::VALIDATE_TWO        => 'highest',
            default                    => 'normal'
        };
    }

    private function getStateLabel(State $state): string
    {
        return match ($state) {
            State::VALIDATE_ONE => State::VALIDATE_ONE->getLabel(),
            State::VALIDATE_TWO => State::VALIDATE_TWO->getLabel(),
            State::CONFIRM      => State::CONFIRM->getLabel(),
            State::REFUSE       => State::REFUSE->getLabel(),
        };
    }

    private function getStateIcon(State $state): string
    {
        return match ($state) {
            State::VALIDATE_ONE        => 'heroicon-o-magnifying-glass',
            State::VALIDATE_TWO        => 'heroicon-o-check-circle',
            State::CONFIRM             => 'heroicon-o-clock',
            State::REFUSE              => 'heroicon-o-x-circle',
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
