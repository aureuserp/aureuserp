<?php

namespace Webkul\TimeOff\Traits;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Support\Enums\Week;
use Webkul\TimeOff\Enums;

trait LeaveAccrualPlan
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('added_value')
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-amount'))
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.5),
                                Forms\Components\Select::make('added_value_type')
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-value-type'))
                                    ->options(Enums\AddedValueType::class)
                                    ->default(Enums\AddedValueType::DAYS->value)
                                    ->required(),
                            ]),
                        Forms\Components\Fieldset::make()
                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-frequency'))
                            ->schema([
                                Forms\Components\Select::make('frequency')
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-frequency'))
                                    ->options(Enums\Frequency::class)
                                    ->live()
                                    ->default(Enums\Frequency::WEEKLY->value)
                                    ->required()
                                    ->afterStateUpdated(fn (Forms\Set $set) => $set('week_day', null)),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Select::make('week_day')
                                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-day'))
                                                    ->options(Week::class)
                                                    ->default(Week::MONDAY->value)
                                                    ->required(),
                                            ])
                                            ->visible(fn (Get $get) => $get('frequency') === Enums\Frequency::WEEKLY->value),
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Select::make('monthly_day')
                                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.day-of-month'))
                                                    ->options(Enums\CarryoverDay::class)
                                                    ->default(Enums\CarryoverDay::DAY_1->value)
                                                    ->required(),
                                            ])
                                            ->visible(fn (Get $get) => $get('frequency') === Enums\Frequency::MONTHLY->value),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('first_day')
                                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.first-day-of-month'))
                                                    ->options(Enums\CarryoverDay::class)
                                                    ->default(Enums\CarryoverDay::DAY_1->value)
                                                    ->required(),
                                                Forms\Components\Select::make('second_day')
                                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.second-day-of-month'))
                                                    ->options(Enums\CarryoverDay::class)
                                                    ->default(Enums\CarryoverDay::DAY_15->value)
                                                    ->required(),
                                            ])
                                            ->visible(fn (Get $get) => $get('frequency') === Enums\Frequency::BIMONTHLY->value),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Group::make()
                                                    ->schema([
                                                        Forms\Components\Select::make('first_month')
                                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.first-period-month'))
                                                            ->options(Enums\CarryoverMonth::class)
                                                            ->default(Enums\CarryoverMonth::JAN->value)
                                                            ->required(),
                                                        Forms\Components\Select::make('first_day_biyearly')
                                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.first-period-day'))
                                                            ->options(Enums\CarryoverDay::class)
                                                            ->default(Enums\CarryoverDay::DAY_1->value)
                                                            ->required(),
                                                    ]),
                                                Forms\Components\Group::make()
                                                    ->schema([
                                                        Forms\Components\Select::make('second_month')
                                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.second-period-month'))
                                                            ->options(Enums\CarryoverMonth::class)
                                                            ->default(Enums\CarryoverMonth::JUL->value)
                                                            ->required(),
                                                        Forms\Components\Select::make('second_day_biyearly')
                                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.second-period-day'))
                                                            ->options(Enums\CarryoverDay::class)
                                                            ->default(Enums\CarryoverDay::DAY_1->value)
                                                            ->required(),
                                                    ]),
                                            ])
                                            ->visible(fn (Get $get) => $get('frequency') === Enums\Frequency::BIYEARLY->value),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Group::make()
                                                    ->schema([
                                                        Forms\Components\Select::make('first_day_biyearly')
                                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.first-period-day'))
                                                            ->options(Enums\CarryoverDay::class)
                                                            ->default(Enums\CarryoverDay::DAY_1->value)
                                                            ->required(),
                                                        Forms\Components\Select::make('first_month')
                                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.first-period-year'))
                                                            ->options(Enums\CarryoverMonth::class)
                                                            ->default(Enums\CarryoverMonth::JAN->value)
                                                            ->required(),
                                                    ]),
                                            ])
                                            ->visible(fn (Get $get) => $get('frequency') === Enums\Frequency::YEARLY->value),
                                    ]),
                            ]),
                        Forms\Components\Fieldset::make(__('time-off::traits/leave-accrual-plan.form.fields.cap-accrued-time'))
                            ->schema([
                                Forms\Components\Toggle::make('cap_accrued_time')
                                    ->inline(false)
                                    ->live()
                                    ->default(false)
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.cap-accrued-time')),
                                Forms\Components\TextInput::make('maximum_leave')
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.days'))
                                    ->visible(fn (Get $get) => $get('cap_accrued_time') === true)
                                    ->numeric(),
                            ])->columns(4),
                        Forms\Components\Fieldset::make(__('time-off::traits/leave-accrual-plan.form.fields.start-count'))
                            ->schema([
                                Forms\Components\TextInput::make('start_count')
                                    ->live()
                                    ->default(1)
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.start-count')),
                                Forms\Components\Select::make('start_type')
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.start-type'))
                                    ->options(Enums\StartType::class)
                                    ->default(Enums\StartType::YEARS->value)
                                    ->required()
                                    ->helperText(__('time-off::traits/leave-accrual-plan.form.fields.after-allocation-start')),
                            ])->columns(2),
                        Forms\Components\Fieldset::make(__('time-off::traits/leave-accrual-plan.form.fields.advanced-accrual-settings'))
                            ->schema([
                                Forms\Components\Radio::make('action_with_unused_accruals')
                                    ->options(Enums\CarryOverUnusedAccruals::class)
                                    ->live()
                                    ->required()
                                    ->default(Enums\CarryOverUnusedAccruals::ALL_ACCRUED_TIME_CARRIED_OVER->value)
                                    ->label(__('time-off::traits/leave-accrual-plan.form.fields.action-with-unused-accruals')),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('cap_accrued_time_yearly')
                                            ->inline(false)
                                            ->live()
                                            ->visible(fn (Get $get) => $get('action_with_unused_accruals') == Enums\CarryOverUnusedAccruals::ALL_ACCRUED_TIME_CARRIED_OVER->value)
                                            ->default(false)
                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.milestone-cap')),
                                        Forms\Components\TextInput::make('maximum_leave_yearly')
                                            ->numeric()
                                            ->visible(fn (Get $get) => $get('cap_accrued_time_yearly'))
                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.maximum-leave-yearly')),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('accrual_validity')
                                            ->inline(false)
                                            ->live()
                                            ->visible(fn (Get $get) => $get('action_with_unused_accruals') == Enums\CarryOverUnusedAccruals::ALL_ACCRUED_TIME_CARRIED_OVER->value)
                                            ->default(false)
                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-validity')),
                                        Forms\Components\TextInput::make('accrual_validity_count')
                                            ->numeric()
                                            ->visible(fn (Get $get) => $get('accrual_validity'))
                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-validity-count')),
                                        Forms\Components\Select::make('accrual_validity_type')
                                            ->required()
                                            ->visible(fn (Get $get) => $get('accrual_validity'))
                                            ->options(Enums\AccrualValidityType::class)
                                            ->label(__('time-off::traits/leave-accrual-plan.form.fields.accrual-validity-type')),
                                    ]),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('added_value')
                    ->label(__('time-off::traits/leave-accrual-plan.table.columns.accrual-amount'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('added_value_type')
                    ->label(__('time-off::traits/leave-accrual-plan.table.columns.accrual-value-type'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('frequency')
                    ->label(__('time-off::traits/leave-accrual-plan.table.columns.frequency'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('maximum_leave')
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
                    ->options(\Webkul\TimeOff\Enums\Frequency::class)
                    ->label(__('time-off::traits/leave-accrual-plan.table.filters.accrual-frequency')),
                SelectFilter::make('start_type')
                    ->options(\Webkul\TimeOff\Enums\StartType::class)
                    ->label(__('time-off::traits/leave-accrual-plan.table.filters.start-type')),
                Tables\Filters\Filter::make('cap_accrued_time')
                    ->form([
                        Forms\Components\Toggle::make('cap_accrued_time')
                            ->label(__('time-off::traits/leave-accrual-plan.table.filters.cap-accrued-time')),
                    ])
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['cap_accrued_time'] ?? null,
                        fn (Builder $query, $value): Builder => $query->where('cap_accrued_time', '>=', $value),
                    ))
                    ->label(__('time-off::traits/leave-accrual-plan.table.filters.cap-accrued-time')),
                SelectFilter::make('action_with_unused_accruals')
                    ->options(\Webkul\TimeOff\Enums\CarryOverUnusedAccruals::class)
                    ->label(__('time-off::traits/leave-accrual-plan.table.filters.action-with-unused-accruals')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('added_value')
                            ->label(__('time-off::traits/leave-accrual-plan.table.filters.accrual-amount'))
                            ->icon('heroicon-o-calculator'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('frequency')
                            ->label(__('time-off::traits/leave-accrual-plan.table.filters.accrual-frequency'))
                            ->icon('heroicon-o-arrow-path-rounded-square'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('start_type')
                            ->label(__('time-off::traits/leave-accrual-plan.table.filters.start-type'))
                            ->icon('heroicon-o-clock'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('time-off::traits/leave-accrual-plan.table.filters.created-at'))
                            ->icon('heroicon-o-calendar'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('time-off::traits/leave-accrual-plan.table.filters.updated-at'))
                            ->icon('heroicon-o-calendar'),
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->label(__('time-off::traits/leave-accrual-plan.table.header-actions.created.title'))
                    ->mutateFormDataUsing(function ($data) {
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::traits/leave-accrual-plan.table.actions.edit.notification.title'))
                            ->body(__('time-off::traits/leave-accrual-plan.table.actions.edit.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::traits/leave-accrual-plan.table.actions.delete.notification.title'))
                            ->body(__('time-off::traits/leave-accrual-plan.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::traits/leave-accrual-plan.table.bulk-actions.delete.notification.title'))
                            ->body(__('time-off::traits/leave-accrual-plan.table.bulk-actions.delete.notification.body'))
                    ),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(1)
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('added_value')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-amount'))
                                    ->icon('heroicon-o-currency-dollar'),
                                Infolists\Components\TextEntry::make('added_value_type')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-value-type'))
                                    ->formatStateUsing(fn ($state) => Enums\AddedValueType::options()[$state] ?? $state)
                                    ->icon('heroicon-o-adjustments-horizontal'),
                            ]),
                        Infolists\Components\TextEntry::make('frequency')
                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-frequency'))
                            ->formatStateUsing(fn ($state) => Enums\Frequency::options()[$state] ?? $state)
                            ->icon('heroicon-o-calendar'),
                        Infolists\Components\Group::make()
                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-frequency'))
                            ->schema([
                                Infolists\Components\TextEntry::make('week_day')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-day'))
                                    ->visible(fn ($record) => $record->frequency === Enums\Frequency::WEEKLY->value)
                                    ->icon('heroicon-o-clock'),
                                Infolists\Components\TextEntry::make('monthly_day')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.day-of-month'))
                                    ->visible(fn ($record) => $record->frequency === Enums\Frequency::MONTHLY->value)
                                    ->icon('heroicon-o-calendar-days'),
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('first_day')
                                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.first-day-of-month'))
                                            ->icon('heroicon-o-arrow-up-circle'),
                                        Infolists\Components\TextEntry::make('second_day')
                                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.second-day-of-month'))
                                            ->icon('heroicon-o-arrow-down-circle'),
                                    ])
                                    ->visible(fn ($record) => $record->frequency === Enums\Frequency::BIMONTHLY->value),
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('first_month')
                                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.first-period-month'))
                                            ->icon('heroicon-o-arrow-up-on-square'),
                                        Infolists\Components\TextEntry::make('second_month')
                                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.second-period-month'))
                                            ->icon('heroicon-o-arrow-down-on-square'),
                                    ])
                                    ->visible(fn ($record) => $record->frequency === Enums\Frequency::BIYEARLY->value),
                            ]),
                        Infolists\Components\IconEntry::make('cap_accrued_time')
                            ->boolean()
                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.cap-accrued-time'))
                            ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                        Infolists\Components\TextEntry::make('maximum_leave')
                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.days'))
                            ->visible(fn ($record) => $record->cap_accrued_time)
                            ->icon('heroicon-o-scale'),
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('start_count')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.start-count'))
                                    ->icon('heroicon-o-play-circle'),
                                Infolists\Components\TextEntry::make('start_type')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.start-type'))
                                    ->formatStateUsing(fn ($state) => Enums\StartType::options()[$state] ?? $state)
                                    ->icon('heroicon-o-adjustments-vertical'),
                            ]),
                        Infolists\Components\Group::make()
                            ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.advanced-accrual-settings'))
                            ->schema([
                                Infolists\Components\TextEntry::make('action_with_unused_accruals')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.action-with-unused-accruals'))
                                    ->formatStateUsing(fn ($state) => Enums\CarryOverUnusedAccruals::options()[$state] ?? $state)
                                    ->icon('heroicon-o-receipt-refund'),
                                Infolists\Components\TextEntry::make('maximum_leave_yearly')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.maximum-leave-yearly'))
                                    ->visible(fn ($record) => $record->cap_accrued_time_yearly)
                                    ->icon('heroicon-o-chart-pie'),
                                Infolists\Components\TextEntry::make('accrual_validity_count')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-validity-count'))
                                    ->visible(fn ($record) => $record->accrual_validity)
                                    ->icon('heroicon-o-clock'),
                                Infolists\Components\TextEntry::make('accrual_validity_type')
                                    ->label(__('time-off::traits/leave-accrual-plan.infolist.entries.accrual-validity-type'))
                                    ->formatStateUsing(fn ($state) => Enums\AccrualValidityType::options()[$state] ?? $state)
                                    ->visible(fn ($record) => $record->accrual_validity)
                                    ->icon('heroicon-o-calendar-days'),
                            ]),
                    ]),
            ]);
    }
}
