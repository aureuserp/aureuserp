<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Webkul\Employee\Models\Employee;
use Webkul\TimeOff\Enums\RequestDateFromPeriod;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Filament\Clusters\Management;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages;
use Webkul\TimeOff\Models\Leave;
use Webkul\TimeOff\Models\LeaveType;

class TimeOffResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Management::class;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/management/resources/time-off.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/management/resources/time-off.navigation.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Select::make('employee_id')
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
                                Forms\Components\Select::make('department_id')
                                    ->relationship('department', 'name')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.department-name'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('holiday_status_id')
                                    ->relationship('holidayStatus', 'name')
                                    ->searchable()
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.time-off-type'))
                                    ->preload()
                                    ->live()
                                    ->required(),
                                Forms\Components\Fieldset::make()
                                    ->label(function (Get $get) {
                                        if ($get('request_unit_half')) {
                                            return __('time-off::filament/clusters/management/resources/time-off.form.fields.date');
                                        } else {
                                            return __('time-off::filament/clusters/management/resources/time-off.form.fields.dates');
                                        }
                                    })
                                    ->live()
                                    ->schema([
                                        Forms\Components\DatePicker::make('request_date_from')
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
                                        Forms\Components\DatePicker::make('request_date_to')
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
                                        Forms\Components\Select::make('request_date_from_period')
                                            ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.period'))
                                            ->options(RequestDateFromPeriod::class)
                                            ->default(RequestDateFromPeriod::MORNING->value)
                                            ->native(false)
                                            ->visible(fn (Get $get) => $get('request_unit_half'))
                                            ->required(),
                                    ]),
                                Forms\Components\Toggle::make('request_unit_half')
                                    ->live()
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.half-day')),
                                Forms\Components\Placeholder::make('requested_days')
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
                                Forms\Components\Textarea::make('private_name')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.form.fields.description'))
                                    ->live(),
                                Forms\Components\FileUpload::make('attachment')
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
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label(__('time-off::filament/clusters/management/resources/time-off.table.columns.employee-name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('holidayStatus.name')
                    ->label(__('time-off::filament/clusters/management/resources/time-off.table.columns.time-off-type'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('private_name')
                    ->label(__('time-off::filament/clusters/management/resources/time-off.table.columns.description'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('request_date_from')
                    ->label(__('time-off::filament/clusters/management/resources/time-off.table.columns.date-from'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('request_date_to')
                    ->label(__('time-off::filament/clusters/management/resources/time-off.table.columns.date-to'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('duration_display')
                    ->label(__('Duration'))
                    ->label(__('time-off::filament/clusters/management/resources/time-off.table.columns.duration'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('time-off::filament/clusters/management/resources/time-off.table.columns.status'))
                    ->formatStateUsing(fn (State $state) => $state->getLabel())
                    ->sortable()
                    ->badge()
                    ->searchable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('employee.name')
                    ->label(__('time-off::filament/clusters/management/resources/time-off.table.groups.employee-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('holidayStatus.name')
                    ->label(__('time-off::filament/clusters/management/resources/time-off.table.groups.time-off-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state')
                    ->label(__('time-off::filament/clusters/management/resources/time-off.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_from')
                    ->label(__('time-off::filament/clusters/management/resources/time-off.table.groups.start-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_to')
                    ->label(__('time-off::filament/clusters/management/resources/time-off.table.groups.start-to'))
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->hidden(fn ($record) => $record->state === State::VALIDATE_TWO->value)
                        ->action(function ($record) {
                            if ($record->state === State::VALIDATE_ONE->value) {
                                $record->update(['state' => State::VALIDATE_TWO->value]);
                            } else {
                                $record->update(['state' => State::VALIDATE_TWO->value]);
                            }

                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/management/resources/time-off.table.actions.approve.notification.title'))
                                ->body(__('time-off::filament/clusters/management/resources/time-off.table.actions.approve.notification.body'))
                                ->send();
                        })
                        ->label(function ($record) {
                            if ($record->state === State::VALIDATE_ONE->value) {
                                return __('time-off::filament/clusters/management/resources/time-off.table.actions.approve.title.validate');
                            } else {
                                return __('time-off::filament/clusters/management/resources/time-off.table.actions.approve.title.approve');
                            }
                        }),
                    Tables\Actions\Action::make('refuse')
                        ->icon('heroicon-o-x-circle')
                        ->hidden(fn ($record) => $record->state === State::REFUSE->value)
                        ->color('danger')
                        ->action(function ($record) {
                            $record->update(['state' => State::REFUSE->value]);

                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/management/resources/time-off.table.actions.refused.notification.title'))
                                ->body(__('time-off::filament/clusters/management/resources/time-off.table.actions.refused.notification.body'))
                                ->send();
                        })
                        ->label(__('time-off::filament/clusters/management/resources/time-off.table.actions.refused.title')),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/management/resources/time-off.table.actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/management/resources/time-off.table.actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/management/resources/time-off.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/management/resources/time-off.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('employee.name')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.employee-name')),

                                Infolists\Components\TextEntry::make('department.name')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.department-name')),

                                Infolists\Components\TextEntry::make('date_from')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.date-from')),

                                Infolists\Components\TextEntry::make('date_to')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.date-to')),

                                Infolists\Components\TextEntry::make('state')
                                    ->label(__('time-off::filament/clusters/management/resources/time-off.infolist.entries.status'))
                                    ->badge(),

                                Infolists\Components\ImageEntry::make('attachment')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.attachment'))
                                    ->visible(fn ($record) => $record->holidayStatus?->support_document),

                                Infolists\Components\TextEntry::make('holidayStatus.name')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.time-off-type'))
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('request_unit_half')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.half-day'))
                                    ->formatStateUsing(fn ($record) => $record->request_unit_half ? 'Yes' : 'No')
                                    ->icon('heroicon-o-clock'),

                                Infolists\Components\TextEntry::make('request_date_from')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.request-date-from'))
                                    ->date()
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('request_date_to')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.request-date-to'))
                                    ->date()
                                    ->hidden(fn ($record) => $record->request_unit_half)
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('request_date_from_period')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.period'))
                                    ->visible(fn ($record) => $record->request_unit_half)
                                    ->icon('heroicon-o-sun'),

                                Infolists\Components\TextEntry::make('private_name')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.description'))
                                    ->icon('heroicon-o-document-text'),

                                Infolists\Components\TextEntry::make('duration_display')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.requested-days'))
                                    ->formatStateUsing(function ($record) {
                                        if ($record->request_unit_half) {
                                            return __('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.day', ['day' => '0.5']);
                                        }

                                        $startDate = Carbon::parse($record->request_date_from);
                                        $endDate = $record->request_date_to ? Carbon::parse($record->request_date_to) : $startDate;

                                        // Calculate business days (excluding weekends)
                                        $businessDays = 0;
                                        $currentDate = $startDate->copy();

                                        while ($currentDate->lte($endDate)) {
                                            // Check if the current date is not Saturday (6) or Sunday (0)
                                            if (! in_array($currentDate->dayOfWeek, [0, 6])) {
                                                $businessDays++;
                                            }
                                            $currentDate->addDay();
                                        }

                                        return __('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.days', ['days' => $businessDays]);
                                    })
                                    ->icon('heroicon-o-calendar-days'),
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTimeOff::route('/'),
            'create' => Pages\CreateTimeOff::route('/create'),
            'edit'   => Pages\EditTimeOff::route('/{record}/edit'),
            'view'   => Pages\ViewTimeOff::route('/{record}'),
        ];
    }
}
