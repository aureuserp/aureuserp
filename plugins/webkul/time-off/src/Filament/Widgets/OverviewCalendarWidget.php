<?php

namespace Webkul\TimeOff\Filament\Widgets;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Actions;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Webkul\TimeOff\Enums\RequestDateFromPeriod;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Models\Leave;

class OverviewCalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = Leave::class;

    public function getHeading(): string|Htmlable|null
    {
        return __('time-off::filament/widgets/overview-calendar-widget.heading.title');
    }

    public function config(): array
    {
        return [
            'initialView' => 'multiMonthYear',
        ];
    }

    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('time-off::filament/widgets/overview-calendar-widget.modal-actions.edit.title'))
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

                    if ($data['request_unit_half']) {
                        $data['duration_display'] = '0.5 day';

                        $data['number_of_days'] = 0.5;
                    } else {
                        $startDate = Carbon::parse($data['request_date_from']);
                        $endDate = $data['request_date_to'] ? Carbon::parse($data['request_date_to']) : $startDate;

                        $data['duration_display'] = $startDate->diffInDays($endDate) + 1 .' day(s)';

                        $data['number_of_days'] = $startDate->diffInDays($endDate) + 1;
                    }

                    $data['creator_id'] = Auth::user()->id;

                    $data['state'] = State::CONFIRM->value;

                    $data['date_from'] = $data['request_date_from'] ?? null;
                    $data['date_to'] = $data['request_date_to'] ?? null;

                    $record->update($data);
                })
                ->mountUsing(
                    function (Forms\Form $form, array $arguments, $livewire) {
                        $leave = $livewire->record;

                        $newData = [
                            ...$leave->toArray(),
                            'request_date_from' => $arguments['event']['start'] ?? $leave->request_date_from,
                            'request_date_to'   => $arguments['event']['end'] ?? $leave->request_date_to,
                        ];

                        $form->fill($newData);
                    }
                ),
            Actions\DeleteAction::make()
                ->label(__('time-off::filament/widgets/overview-calendar-widget.modal-actions.delete.title')),
        ];
    }

    protected function viewAction(): Action
    {
        return Actions\ViewAction::make()
            ->modalIcon('heroicon-o-lifebuoy')
            ->label(__('time-off::filament/widgets/overview-calendar-widget.view-action.title'))
            ->modalDescription(__('time-off::filament/widgets/overview-calendar-widget.view-action.description'))
            ->infolist($this->infolist());
    }

    protected function headerActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->modalIcon('heroicon-o-lifebuoy')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.header-actions.create.title'))
                ->modalDescription(__('time-off::filament/widgets/overview-calendar-widget.header-actions.create.description'))
                ->action(function ($data) {
                    $user = Auth::user();
                    $employee = $user->employee;

                    if ($employee) {
                        $data['employee_id'] = $employee->id;
                    } else {
                        Notification::make()
                            ->danger()
                            ->title(__('time-off::filament/widgets/overview-calendar-widget.header-actions.create.employee-not-found.notification.title'))
                            ->body(__('time-off::filament/widgets/overview-calendar-widget.header-actions.create.employee-not-found.notification.body'))
                            ->send();

                        return;
                    }

                    if ($employee?->department) {
                        $data['department_id'] = $employee->department?->id;
                    } else {
                        $data['department_id'] = null;
                    }

                    if ($employee?->calendar) {
                        $data['calendar_id'] = $employee->calendar->id;
                        $data['number_of_hours'] = $employee->calendar->hours_per_day;
                    }

                    if ($user) {
                        $data['user_id'] = $user->id;

                        $data['company_id'] = $user->default_company_id;

                        $data['employee_company_id'] = $user->default_company_id;
                    }

                    if ($data['request_unit_half']) {
                        $data['duration_display'] = '0.5 day';

                        $data['number_of_days'] = 0.5;
                    } else {
                        $startDate = Carbon::parse($data['request_date_from']);
                        $endDate = $data['request_date_to'] ? Carbon::parse($data['request_date_to']) : $startDate;

                        $data['duration_display'] = $startDate->diffInDays($endDate) + 1 .' day(s)';

                        $data['number_of_days'] = $startDate->diffInDays($endDate) + 1;
                    }

                    $data['creator_id'] = Auth::user()->id;

                    $data['state'] = State::CONFIRM->value;

                    $data['date_from'] = $data['request_date_from'];
                    $data['date_to'] = $data['request_date_to'];

                    Leave::create($data);
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
            Forms\Components\Select::make('holiday_status_id')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.form.fields.time-off-type'))
                ->relationship('holidayStatus', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Fieldset::make()
                ->label(function (Get $get) {
                    if ($get('request_unit_half')) {
                        return 'Date';
                    } else {
                        return 'Dates';
                    }
                })
                ->live()
                ->schema([
                    Forms\Components\DatePicker::make('request_date_from')
                        ->native(false)
                        ->label(__('time-off::filament/widgets/overview-calendar-widget.form.fields.request-date-from'))
                        ->default(now())
                        ->required(),
                    Forms\Components\DatePicker::make('request_date_to')
                        ->native(false)
                        ->label(__('time-off::filament/widgets/overview-calendar-widget.form.fields.request-date-to'))
                        ->default(now())
                        ->hidden(fn (Get $get) => $get('request_unit_half'))
                        ->required(),
                    Forms\Components\Select::make('request_date_from_period')
                        ->label(__('time-off::filament/widgets/overview-calendar-widget.form.fields.period'))
                        ->options(RequestDateFromPeriod::class)
                        ->default(RequestDateFromPeriod::MORNING->value)
                        ->native(false)
                        ->visible(fn (Get $get) => $get('request_unit_half'))
                        ->required(),
                ]),
            Forms\Components\Toggle::make('request_unit_half')
                ->live()
                ->label(__('time-off::filament/widgets/overview-calendar-widget.form.fields.half-day')),
            Forms\Components\Placeholder::make('requested_days')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.form.fields.requested-days'))
                ->live()
                ->inlineLabel()
                ->reactive()
                ->content(function ($state, Get $get): string {
                    if ($get('request_unit_half')) {
                        return '0.5 day';
                    }

                    $startDate = Carbon::parse($get('request_date_from'));
                    $endDate = $get('request_date_to') ? Carbon::parse($get('request_date_to')) : $startDate;

                    return $startDate->diffInDays($endDate).' day(s)';
                }),
            Forms\Components\Textarea::make('private_name')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.form.fields.description')),
        ];
    }

    public function infolist(): array
    {
        return [
            Infolists\Components\TextEntry::make('holidayStatus.name')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.time-off-type'))
                ->icon('heroicon-o-clock'),
            Infolists\Components\TextEntry::make('request_date_from')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.request-date-from'))
                ->date()
                ->icon('heroicon-o-calendar-days'),
            Infolists\Components\TextEntry::make('request_date_to')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.request-date-to'))
                ->date()
                ->icon('heroicon-o-calendar-days'),
            Infolists\Components\TextEntry::make('number_of_days')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.duration'))
                ->formatStateUsing(fn ($state) => $state.' day(s)')
                ->icon('heroicon-o-clock'),
            Infolists\Components\TextEntry::make('private_name')
                ->label(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.description'))
                ->icon('heroicon-o-document-text')
                ->placeholder(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.description-placeholder')),
            Infolists\Components\TextEntry::make('state')
                ->placeholder(__('time-off::filament/widgets/overview-calendar-widget.infolist.entries.status'))
                ->badge()
                ->formatStateUsing(fn ($state) => State::options()[$state])
                ->icon('heroicon-o-check-circle'),
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $user = Auth::user();

        return Leave::query()
            ->where('request_date_from', '>=', $fetchInfo['start'])
            ->where('request_date_to', '<=', $fetchInfo['end'])
            ->with('holidayStatus')
            ->get()
            ->map(function (Leave $leave) {
                return [
                    'id'              => $leave->id,
                    'title'           => $leave->holidayStatus?->name,
                    'start'           => $leave->request_date_from,
                    'end'             => $leave->request_date_to,
                    'allDay'          => true,
                    'backgroundColor' => $leave->holidayStatus?->color,
                    'borderColor'     => $leave->holidayStatus?->color,
                    'textColor'       => '#ffffff',
                ];
            })
            ->all();
    }

    public function onDateSelect(string $start, ?string $end, bool $allDay, ?array $view, ?array $resource): void
    {
        $startDate = Carbon::parse($start);
        $endDate = $end ? Carbon::parse($end) : $startDate;

        $this->mountAction('create', [
            'request_date_from' => $startDate->toDateString(),
            'request_date_to'   => $endDate->toDateString(),
        ]);
    }
}
