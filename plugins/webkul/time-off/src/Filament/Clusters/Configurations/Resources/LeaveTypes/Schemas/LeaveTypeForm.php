<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypes\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\TimeOff\Enums\AllocationValidationType;
use Webkul\TimeOff\Enums\EmployeeRequest;
use Webkul\TimeOff\Enums\LeaveValidationType;
use Webkul\TimeOff\Enums\RequestUnit;
use Webkul\TimeOff\Enums\RequiresAllocation;
use Webkul\TimeOff\Enums\TimeType;

class LeaveTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.general.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                        Group::make()
                                            ->schema([
                                                Group::make()
                                                    ->schema([
                                                        Radio::make('leave_validation_type')
                                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.approval'))
                                                            ->inline(false)
                                                            ->default(LeaveValidationType::HR->value)
                                                            ->live()
                                                            ->options(LeaveValidationType::class),
                                                        Radio::make('requires_allocation')
                                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.requires-allocation'))
                                                            ->inline(false)
                                                            ->live()
                                                            ->default(RequiresAllocation::NO->value)
                                                            ->options(RequiresAllocation::class),
                                                        Radio::make('employee_requests')
                                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.employee-requests'))
                                                            ->inline(false)
                                                            ->live()
                                                            ->visible(fn (Get $get) => $get('requires_allocation') === RequiresAllocation::YES->value)
                                                            ->default(EmployeeRequest::NO->value)
                                                            ->options(EmployeeRequest::class),
                                                        Radio::make('allocation_validation_type')
                                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.general.fields.approval'))
                                                            ->inline(false)
                                                            ->live()
                                                            ->visible(fn (Get $get) => $get('requires_allocation') === RequiresAllocation::YES->value)
                                                            ->default(AllocationValidationType::HR->value)
                                                            ->options(AllocationValidationType::class),
                                                    ]),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.display-option.title'))
                                    ->hiddenLabel()
                                    ->schema([
                                        ColorPicker::make('color')
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.display-option.fields.color'))
                                            ->hexColor(),
                                    ]),
                                Section::make(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.title'))
                                    ->hiddenLabel()
                                    ->schema([
                                        Select::make('time_off_user_leave_types')
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.notified-time-off-officers'))
                                            ->relationship('notifiedTimeOffOfficers', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->multiple(),
                                        Select::make('request_unit')
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.take-time-off-in'))
                                            ->options(RequestUnit::class)
                                            ->default(RequestUnit::DAY->value),
                                        Toggle::make('include_public_holidays_in_duration')
                                            ->inline(false)
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.public-holiday-included')),
                                        Toggle::make('support_document')
                                            ->inline(false)
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.allow-to-attach-supporting-document')),
                                        Toggle::make('show_on_dashboard')
                                            ->inline(false)
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.show-on-dashboard')),
                                        Select::make('time_type')
                                            ->options(TimeType::class)
                                            ->default(TimeType::LEAVE->value)
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.kind-of-time')),
                                        Toggle::make('allows_negative')
                                            ->visible(fn (Get $get) => $get('requires_allocation') === RequiresAllocation::YES->value)
                                            ->live()
                                            ->inline(false)
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.allow-negative-cap')),
                                        TextInput::make('max_allowed_negative')
                                            ->numeric()
                                            ->default(0)
                                            ->visible(fn (Get $get) => $get('requires_allocation') === RequiresAllocation::YES->value && $get('allows_negative') === true)
                                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.form.sections.configuration.fields.max-negative-cap'))
                                            ->step(1)
                                            ->live()
                                            ->required(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }
}
