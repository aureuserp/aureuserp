<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Webkul\Employee\Models\Employee;
use Webkul\TimeOff\Enums\RequestDateFromPeriod;
use Webkul\TimeOff\Models\LeaveType;

class TimeOffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Select::make('employee_id')
                                    ->relationship('employee', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
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
                                    ->preload()
                                    ->required(),
                                Select::make('holiday_status_id')
                                    ->relationship('holidayStatus', 'name')
                                    ->searchable()
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.time-off-type'))
                                    ->preload()
                                    ->live()
                                    ->required(),
                                Fieldset::make()
                                    ->label(function (Get $get) {
                                        if ($get('request_unit_half')) {
                                            return __('time-off::filament/clusters/management/resources/time-off.form.fields.date');
                                        } else {
                                            return __('time-off::filament/clusters/management/resources/time-off.form.fields.dates');
                                        }
                                    })
                                    ->live()
                                    ->schema([
                                        DatePicker::make('request_date_from')
                                            ->native(false)
                                            ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.request-date-from'))
                                            ->default(now())
                                            ->minDate(now()->toDateString())
                                            ->live()
                                            ->afterStateUpdated(fn (callable $set) => $set('request_date_to', null))
                                            ->rules([
                                                'required',
                                                'date',
                                                'after_or_equal:today',
                                            ])
                                            ->required(),
                                        DatePicker::make('request_date_to')
                                            ->native(false)
                                            ->default(now())
                                            ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.request-date-to'))
                                            ->hidden(fn (Get $get) => $get('request_unit_half'))
                                            ->minDate(fn (callable $get) => $get('request_date_from') ?: now()->toDateString())
                                            ->live()
                                            ->rules([
                                                'required',
                                                'date',
                                                'after_or_equal:request_date_from',
                                            ])
                                            ->required(),
                                        Select::make('request_date_from_period')
                                            ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.period'))
                                            ->options(RequestDateFromPeriod::class)
                                            ->default(RequestDateFromPeriod::MORNING->value)
                                            ->native(false)
                                            ->visible(fn (Get $get) => $get('request_unit_half'))
                                            ->required(),
                                    ]),
                                Toggle::make('request_unit_half')
                                    ->live()
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.half-day')),
                                Placeholder::make('requested_days')
                                    ->label('Requested (Days/Hours)')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.requested-days'))
                                    ->live()
                                    ->inlineLabel()
                                    ->reactive()
                                    ->content(function (Get $get): string {
                                        if ($get('request_unit_half')) {
                                            return __('time-off::filament/clusters/management/resources/time-off.form.fields.day', ['day' => '0.5']);
                                        }

                                        $startDate = Carbon::parse($get('request_date_from'));
                                        $endDate = $get('request_date_to') ? Carbon::parse($get('request_date_to')) : $startDate;

                                        $businessDays = 0;
                                        $currentDate = $startDate->copy();

                                        while ($currentDate->lte($endDate)) {
                                            if (! in_array($currentDate->dayOfWeek, [0, 6])) {
                                                $businessDays++;
                                            }

                                            $currentDate->addDay();
                                        }

                                        return __('time-off::filament/clusters/management/resources/time-off.form.fields.days', ['days' => $businessDays]);
                                    }),
                                Textarea::make('private_name')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.description'))
                                    ->live(),
                                FileUpload::make('attachment')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.attachment'))
                                    ->downloadable()
                                    ->deletable()
                                    ->previewable()
                                    ->openable()
                                    ->acceptedFileTypes([
                                        'image/*',
                                        'application/pdf',
                                    ])
                                    ->visible(function (Get $get) {
                                        $leaveType = LeaveType::find($get('holiday_status_id'));

                                        if ($leaveType) {
                                            return $leaveType->support_document;
                                        }

                                        return false;
                                    })
                                    ->live(),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
