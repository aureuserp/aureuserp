<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOff\Schemas;

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
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Webkul\TimeOff\Enums\RequestDateFromPeriod;
use Webkul\TimeOff\Models\LeaveType;

class MyTimeOffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Select::make('holiday_status_id')
                                    ->relationship('holidayStatus', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.time-off-type'))
                                    ->required(),
                                Fieldset::make()
                                    ->label(function (Get $get) {
                                        if ($get('request_unit_half')) {
                                            return __('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.date');
                                        } else {
                                            return __('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.dates');
                                        }
                                    })
                                    ->live()
                                    ->schema([
                                        DatePicker::make('request_date_from')
                                            ->native(false)
                                            ->default(now())
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.request-date-from'))
                                            ->required(),
                                        DatePicker::make('request_date_to')
                                            ->native(false)
                                            ->default(now())
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.request-date-to'))
                                            ->hidden(fn (Get $get) => $get('request_unit_half'))
                                            ->required(),
                                        Select::make('request_date_from_period')
                                            ->options(RequestDateFromPeriod::class)
                                            ->default(RequestDateFromPeriod::MORNING->value)
                                            ->native(false)
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.period'))
                                            ->visible(fn (Get $get) => $get('request_unit_half'))
                                            ->required(),
                                    ]),
                                Toggle::make('request_unit_half')
                                    ->live()
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.half-day')),
                                Placeholder::make('requested_days')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.requested-days'))
                                    ->live()
                                    ->inlineLabel()
                                    ->reactive()
                                    ->content(function ($state, Get $get): string {
                                        if ($get('request_unit_half')) {
                                            return __('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.day', ['day' => '0.5']);
                                        }

                                        $startDate = Carbon::parse($get('request_date_from'));
                                        $endDate = $get('request_date_to') ? Carbon::parse($get('request_date_to')) : $startDate;

                                        return __('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.days', ['days' => $startDate->diffInDays($endDate) + 1]);
                                    }),
                                Textarea::make('private_name')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.description'))
                                    ->live(),
                                FileUpload::make('attachment')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.form.fields.attachment'))
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
                    ]),
            ]);
    }
}
