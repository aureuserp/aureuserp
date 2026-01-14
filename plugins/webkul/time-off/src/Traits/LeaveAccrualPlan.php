<?php

namespace Webkul\TimeOff\Traits;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Support\Enums\Week;
use Webkul\TimeOff\Enums\AccrualValidityType;
use Webkul\TimeOff\Enums\AddedValueType;
use Webkul\TimeOff\Enums\CarryoverDay;
use Webkul\TimeOff\Enums\CarryoverMonth;
use Webkul\TimeOff\Enums\CarryOverUnusedAccruals;
use Webkul\TimeOff\Enums\Frequency;
use Webkul\TimeOff\Enums\StartType;

trait LeaveAccrualPlan
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('added_value')
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-amount'))
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.5),
                                Select::make('added_value_type')
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-value-type'))
                                    ->options(AddedValueType::class)
                                    ->default(AddedValueType::DAYS->value)
                                    ->required(),
                            ]),
                        Fieldset::make()
                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-frequency'))
                            ->schema([
                                Select::make('frequency')
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-frequency'))
                                    ->options(Frequency::class)
                                    ->live()
                                    ->default(Frequency::WEEKLY->value)
                                    ->required()
                                    ->afterStateUpdated(fn (Set $set) => $set('week_day', null)),
                                Grid::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Select::make('week_day')
                                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-day'))
                                                    ->options(Week::class)
                                                    ->default(Week::MONDAY->value)
                                                    ->required(),
                                            ])
                                            ->visible(fn (Get $get) => $get('frequency') === Frequency::WEEKLY->value),
                                        Group::make()
                                            ->schema([
                                                Select::make('monthly_day')
                                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.day-of-month'))
                                                    ->options(CarryoverDay::class)
                                                    ->default(CarryoverDay::DAY_1->value)
                                                    ->required(),
                                            ])
                                            ->visible(fn (Get $get) => $get('frequency') === Frequency::MONTHLY->value),
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('first_day')
                                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.first-day-of-month'))
                                                    ->options(CarryoverDay::class)
                                                    ->default(CarryoverDay::DAY_1->value)
                                                    ->required(),
                                                Select::make('second_day')
                                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.second-day-of-month'))
                                                    ->options(CarryoverDay::class)
                                                    ->default(CarryoverDay::DAY_15->value)
                                                    ->required(),
                                            ])
                                            ->visible(fn (Get $get) => $get('frequency') === Frequency::BIMONTHLY->value),
                                        Grid::make(2)
                                            ->schema([
                                                Group::make()
                                                    ->schema([
                                                        Select::make('first_month')
                                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.first-period-month'))
                                                            ->options(CarryoverMonth::class)
                                                            ->default(CarryoverMonth::JAN->value)
                                                            ->required(),
                                                        Select::make('first_day_biyearly')
                                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.first-period-day'))
                                                            ->options(CarryoverDay::class)
                                                            ->default(CarryoverDay::DAY_1->value)
                                                            ->required(),
                                                    ]),
                                                Group::make()
                                                    ->schema([
                                                        Select::make('second_month')
                                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.second-period-month'))
                                                            ->options(CarryoverMonth::class)
                                                            ->default(CarryoverMonth::JUL->value)
                                                            ->required(),
                                                        Select::make('second_day_biyearly')
                                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.second-period-day'))
                                                            ->options(CarryoverDay::class)
                                                            ->default(CarryoverDay::DAY_1->value)
                                                            ->required(),
                                                    ]),
                                            ])
                                            ->visible(fn (Get $get) => $get('frequency') === Frequency::BIYEARLY->value),
                                        Grid::make(2)
                                            ->schema([
                                                Group::make()
                                                    ->schema([
                                                        Select::make('first_day_biyearly')
                                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.first-period-day'))
                                                            ->options(CarryoverDay::class)
                                                            ->default(CarryoverDay::DAY_1->value)
                                                            ->required(),
                                                        Select::make('first_month')
                                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.first-period-year'))
                                                            ->options(CarryoverMonth::class)
                                                            ->default(CarryoverMonth::JAN->value)
                                                            ->required(),
                                                    ]),
                                            ])
                                            ->visible(fn (Get $get) => $get('frequency') === Frequency::YEARLY->value),
                                    ]),
                            ]),
                        Fieldset::make(__('time-off::traits/leave-accrual-plan.form.fields.cap-accrued-time'))
                            ->schema([
                                Toggle::make('cap_accrued_time')
                                    ->inline(false)
                                    ->live()
                                    ->default(false)
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.cap-accrued-time')),
                                TextInput::make('maximum_leave')
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.days'))
                                    ->visible(fn (Get $get) => $get('cap_accrued_time') === true)
                                    ->numeric(),
                            ])->columns(4),
                        Fieldset::make(__('time-off::traits/leave-accrual-plan.form.fields.start-count'))
                            ->schema([
                                TextInput::make('start_count')
                                    ->live()
                                    ->default(1)
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.start-count')),
                                Select::make('start_type')
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.start-type'))
                                    ->options(StartType::class)
                                    ->default(StartType::YEARS->value)
                                    ->required()
                                    ->helperText(__('time-off::traits/leave-accrual-plan.form.fields.after-allocation-start')),
                            ])->columns(2),
                        Fieldset::make(__('time-off::traits/leave-accrual-plan.form.fields.advanced-accrual-settings'))
                            ->schema([
                                Radio::make('action_with_unused_accruals')
                                    ->options(CarryOverUnusedAccruals::class)
                                    ->live()
                                    ->required()
                                    ->default(CarryOverUnusedAccruals::ALL_ACCRUED_TIME_CARRIED_OVER->value)
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.action-with-unused-accruals')),
                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('cap_accrued_time_yearly')
                                            ->inline(false)
                                            ->live()
                                            ->visible(fn (Get $get) => $get('action_with_unused_accruals') == CarryOverUnusedAccruals::ALL_ACCRUED_TIME_CARRIED_OVER->value)
                                            ->default(false)
                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.milestone-cap')),
                                        TextInput::make('maximum_leave_yearly')
                                            ->numeric()
                                            ->visible(fn (Get $get) => $get('cap_accrued_time_yearly'))
                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.maximum-leave-yearly')),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('accrual_validity')
                                            ->inline(false)
                                            ->live()
                                            ->visible(fn (Get $get) => $get('action_with_unused_accruals') == CarryOverUnusedAccruals::ALL_ACCRUED_TIME_CARRIED_OVER->value)
                                            ->default(false)
                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-validity')),
                                        TextInput::make('accrual_validity_count')
                                            ->numeric()
                                            ->visible(fn (Get $get) => $get('accrual_validity'))
                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-validity-count')),
                                        Select::make('accrual_validity_type')
                                            ->required()
                                            ->visible(fn (Get $get) => $get('accrual_validity'))
                                            ->options(AccrualValidityType::class)
                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-validity-type')),
                                    ]),
                            ])
                            ->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('added_value')
                    ->label(__('time-off::traits/leave-accrual-plan.table.columns.accrual-amount'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('added_value_type')
                    ->label(__('time-off::traits/leave-accrual-plan.table.columns.accrual-value-type'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('frequency')
                    ->label(__('time-off::traits/leave-accrual-plan.table.columns.frequency'))
                    ->sortable(),
                TextColumn::make('maximum_leave')
                    ->label(__('time-off::traits/leave-accrual-plan.table.columns.maximum-leave-days'))
                    ->sortable()
                    ->searchable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('added_value')
                    ->label(__('time-off::traits/leave-accrual-plan.table.groups.accrual-amount'))
                    ->collapsible(),
                Tables\Grouping\Group::make('added_value_type')
                    ->label(__('time-off::traits/leave-accrual-plan.table.groups.accrual-value-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('frequency')
                    ->label(__('time-off::traits/leave-accrual-plan.table.groups.frequency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('maximum_leave')
                    ->label(__('time-off::traits/leave-accrual-plan.table.groups.maximum-leave-days'))
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('frequency')
                    ->options(Frequency::class)
                    ->label(__('time-off::traits/leave-accrual-plan.table.filters.accrual-frequency')),
                SelectFilter::make('start_type')
                    ->options(StartType::class)
                    ->label(__('time-off::traits/leave-accrual-plan.table.filters.start-type')),
                Filter::make('cap_accrued_time')
                    ->schema([
                        Toggle::make('cap_accrued_time')
                            ->label(__('time-off::traits/leave-accrual-plan.table.filters.cap-accrued-time')),
                    ])
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['cap_accrued_time'] ?? null,
                        fn (Builder $query, $value): Builder => $query->where('cap_accrued_time', '>=', $value),
                    ))
                    ->label(__('time-off::traits/leave-accrual-plan.table.filters.cap-accrued-time')),
                SelectFilter::make('action_with_unused_accruals')
                    ->options(CarryOverUnusedAccruals::class)
                    ->label(__('time-off::traits/leave-accrual-plan.table.filters.action-with-unused-accruals')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('added_value')
                            ->label(__('time-off::traits/leave-accrual-plan.table.filters.accrual-amount'))
                            ->icon('heroicon-o-calculator'),
                        TextConstraint::make('frequency')
                            ->label(__('time-off::traits/leave-accrual-plan.table.filters.accrual-frequency'))
                            ->icon('heroicon-o-arrow-path-rounded-square'),
                        TextConstraint::make('start_type')
                            ->label(__('time-off::traits/leave-accrual-plan.table.filters.start-type'))
                            ->icon('heroicon-o-clock'),
                        DateConstraint::make('created_at')
                            ->label(__('time-off::traits/leave-accrual-plan.table.filters.created-at'))
                            ->icon('heroicon-o-calendar'),
                        DateConstraint::make('updated_at')
                            ->label(__('time-off::traits/leave-accrual-plan.table.filters.updated-at'))
                            ->icon('heroicon-o-calendar'),
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->label(__('time-off::traits/leave-accrual-plan.table.header-actions.created.title'))
                    ->mutateDataUsing(function ($data) {
                        $data['creator_id'] = Auth::user()?->id;

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::traits/leave-accrual-plan.table.header-actions.created.notification.title'))
                            ->body(__('time-off::traits/leave-accrual-plan.table.header-actions.created.notification.body'))
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::traits/leave-accrual-plan.table.actions.edit.notification.title'))
                            ->body(__('time-off::traits/leave-accrual-plan.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::traits/leave-accrual-plan.table.actions.delete.notification.title'))
                            ->body(__('time-off::traits/leave-accrual-plan.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::traits/leave-accrual-plan.table.bulk-actions.delete.notification.title'))
                            ->body(__('time-off::traits/leave-accrual-plan.table.bulk-actions.delete.notification.body'))
                    ),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('added_value')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-amount'))
                                    ->icon('heroicon-o-currency-dollar'),
                                TextEntry::make('added_value_type')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-value-type'))
                                    ->formatStateUsing(fn ($state) => AddedValueType::options()[$state] ?? $state)
                                    ->icon('heroicon-o-adjustments-horizontal'),
                            ]),
                        TextEntry::make('frequency')
                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-frequency'))
                            ->formatStateUsing(fn ($state) => Frequency::options()[$state] ?? $state)
                            ->icon('heroicon-o-calendar'),
                        Group::make()
                            ->schema([
                                TextEntry::make('week_day')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-day'))
                                    ->visible(fn ($record) => $record->frequency === Frequency::WEEKLY->value)
                                    ->icon('heroicon-o-clock'),
                                TextEntry::make('monthly_day')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.day-of-month'))
                                    ->visible(fn ($record) => $record->frequency === Frequency::MONTHLY->value)
                                    ->icon('heroicon-o-calendar-days'),
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('first_day')
                                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.first-day-of-month'))
                                            ->icon('heroicon-o-arrow-up-circle'),
                                        TextEntry::make('second_day')
                                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.second-day-of-month'))
                                            ->icon('heroicon-o-arrow-down-circle'),
                                    ])
                                    ->visible(fn ($record) => $record->frequency === Frequency::BIMONTHLY->value),
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('first_month')
                                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.first-period-month'))
                                            ->icon('heroicon-o-arrow-up-on-square'),
                                        TextEntry::make('second_month')
                                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.second-period-month'))
                                            ->icon('heroicon-o-arrow-down-on-square'),
                                    ])
                                    ->visible(fn ($record) => $record->frequency === Frequency::BIYEARLY->value),
                            ]),
                        IconEntry::make('cap_accrued_time')
                            ->boolean()
                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.cap-accrued-time'))
                            ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                        TextEntry::make('maximum_leave')
                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.days'))
                            ->visible(fn ($record) => $record->cap_accrued_time)
                            ->icon('heroicon-o-scale'),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('start_count')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.start-count'))
                                    ->icon('heroicon-o-play-circle'),
                                TextEntry::make('start_type')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.start-type'))
                                    ->formatStateUsing(fn ($state) => StartType::options()[$state] ?? $state)
                                    ->icon('heroicon-o-adjustments-vertical'),
                            ]),
                        Group::make()
                            ->schema([
                                TextEntry::make('action_with_unused_accruals')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.action-with-unused-accruals'))
                                    ->formatStateUsing(fn ($state) => CarryOverUnusedAccruals::options()[$state] ?? $state)
                                    ->icon('heroicon-o-receipt-refund'),
                                TextEntry::make('maximum_leave_yearly')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.maximum-leave-yearly'))
                                    ->visible(fn ($record) => $record->cap_accrued_time_yearly)
                                    ->icon('heroicon-o-chart-pie'),
                                TextEntry::make('accrual_validity_count')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-validity-count'))
                                    ->visible(fn ($record) => $record->accrual_validity)
                                    ->icon('heroicon-o-clock'),
                                TextEntry::make('accrual_validity_type')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-validity-type'))
                                    ->formatStateUsing(fn ($state) => AccrualValidityType::options()[$state] ?? $state)
                                    ->visible(fn ($record) => $record->accrual_validity)
                                    ->icon('heroicon-o-calendar-days'),
                            ]),
                    ]),
            ]);
    }
}
