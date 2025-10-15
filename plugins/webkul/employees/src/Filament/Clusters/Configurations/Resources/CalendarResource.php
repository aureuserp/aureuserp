<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\Pages\CreateCalendar;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\Pages\EditCalendar;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\Pages\ListCalendars;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\Pages\ViewCalendar;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\RelationManagers\CalendarAttendance;
use Webkul\Employee\Models\Calendar;

class CalendarResource extends Resource
{
    protected static ?string $model = Calendar::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/calendar.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/clusters/configurations/resources/calendar.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/calendar.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/clusters/configurations/resources/calendar.form.sections.general.title'))
                                    ->schema([
                                        Hidden::make('creator_id')
                                            ->default(Auth::user()->id),
                                        TextInput::make('name')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.general.fields.schedule-name'))
                                            ->maxLength(255)
                                            ->required()
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('employees::filament/clusters/configurations/resources/calendar.form.sections.general.fields.schedule-name-tooltip')),
                                        Select::make('timezone')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.general.fields.timezone'))
                                            ->options(function () {
                                                return collect(timezone_identifiers_list())->mapWithKeys(function ($timezone) {
                                                    return [$timezone => $timezone];
                                                });
                                            })
                                            ->default(date_default_timezone_get())
                                            ->preload()
                                            ->searchable()
                                            ->required()
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('employees::filament/clusters/configurations/resources/calendar.form.sections.general.fields.timezone-tooltip')),
                                        Select::make('company_id')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.general.fields.company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ])->columns(2),
                                Section::make(__('employees::filament/clusters/configurations/resources/calendar.form.sections.configuration.title'))
                                    ->schema([
                                        TextInput::make('hours_per_day')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.configuration.fields.hours-per-day'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(24)
                                            ->default(8)
                                            ->suffix(__('employees::filament/clusters/configurations/resources/calendar.form.sections.configuration.fields.hours-per-day-suffix')),
                                        TextInput::make('full_time_required_hours')
                                            ->label('Full-Time Required Hours')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.configuration.fields.full-time-required-hours'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(168)
                                            ->default(40)
                                            ->suffix(__('employees::filament/clusters/configurations/resources/calendar.form.sections.configuration.fields.full-time-required-hours-suffix')),
                                    ])->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/clusters/configurations/resources/calendar.form.sections.flexibility.title'))
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.flexibility.fields.status'))
                                            ->default(true)
                                            ->inline(false),
                                        Toggle::make('two_weeks_calendar')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.flexibility.fields.two-weeks-calendar'))
                                            ->inline(false)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: 'Enable alternating two-week work schedule'),
                                        Toggle::make('flexible_hours')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.flexibility.fields.flexible-hours'))
                                            ->inline(false)
                                            ->live()
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('employees::filament/clusters/configurations/resources/calendar.form.sections.flexibility.fields.flexible-hours-tooltip')),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('id')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label('Schedule Name')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('timezone')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.timezone'))
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.company'))
                    ->searchable()
                    ->sortable(),
                IconColumn::make('flexible_hours')
                    ->sortable()
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.flexible-hours'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.status'))
                    ->boolean(),
                TextColumn::make('hours_per_day')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.daily-hours'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('timezone')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.timezone'))
                    ->collapsible(),
                Tables\Grouping\Group::make('flexible_hours')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.flexible-hours'))
                    ->collapsible(),
                Tables\Grouping\Group::make('is_active')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('hours_per_day')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.daily-hours'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.company')),
                TernaryFilter::make('is_active')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.is-active')),
                TernaryFilter::make('two_weeks_calendar')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.two-week-calendar')),
                TernaryFilter::make('flexible_hours')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.flexible-hours')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.name'))
                            ->icon('heroicon-o-user'),
                        NumberConstraint::make('hours_per_day')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.daily-hours'))
                            ->icon('heroicon-o-clock'),
                        NumberConstraint::make('full_time_required_hours')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.full-time-required-hours'))
                            ->icon('heroicon-o-clock'),
                        TextConstraint::make('timezone')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.timezone'))
                            ->icon('heroicon-o-clock'),
                        RelationshipConstraint::make('attendance')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.attendance'))
                            ->icon('heroicon-o-building-office')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.attendance'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.name'))
                            ->icon('heroicon-o-building-office')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('createdBy')
                            ->label('Created By')
                            ->icon('heroicon-o-user')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.created-by'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar.table.actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar.table.actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar.table.actions.force-delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar.table.bulk-actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.general.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.general.entries.name')),
                                        TextEntry::make('timezone')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-clock')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.general.entries.timezone')),
                                        TextEntry::make('company.name')
                                            ->icon('heroicon-o-building-office-2')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.general.entries.company')),
                                    ])->columns(2),
                                Section::make(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.configuration.title'))
                                    ->schema([
                                        TextEntry::make('hours_per_day')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.configuration.entries.hours-per-day'))
                                            ->icon('heroicon-o-clock')
                                            ->date(),
                                        TextEntry::make('full_time_required_hours')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.configuration.entries.full-time-required-hours'))
                                            ->icon('heroicon-o-clock')
                                            ->date(),
                                    ])->columns(2),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.flexibility.title'))
                                ->schema([
                                    IconEntry::make('is_active')
                                        ->boolean()
                                        ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.flexibility.entries.status')),
                                    IconEntry::make('two_weeks_calendar')
                                        ->boolean()
                                        ->placeholder('—')
                                        ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.flexibility.entries.two-weeks-calendar')),
                                    IconEntry::make('flexible_hours')
                                        ->placeholder('—')
                                        ->boolean()
                                        ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.flexibility.entries.flexible-hours')),
                                ]),
                        ])->columnSpan(1),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CalendarAttendance::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCalendars::route('/'),
            'create' => CreateCalendar::route('/create'),
            'view'   => ViewCalendar::route('/{record}'),
            'edit'   => EditCalendar::route('/{record}/edit'),
        ];
    }
}
