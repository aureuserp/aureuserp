<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Enums\CalendarDisplayType;
use Webkul\Employee\Enums\DayOfWeek;
use Webkul\Employee\Enums\DayPeriod;
use Webkul\Employee\Enums\WeekType;

class CalendarAttendance extends RelationManager
{
    protected static string $relationship = 'attendance';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.modal.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.general.title'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.general.fields.attendance-name'))
                            ->required()
                            ->maxLength(255),
                        Select::make('day_of_week')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.general.fields.day-of-week'))
                            ->searchable()
                            ->preload()
                            ->options(DayOfWeek::options())
                            ->required(),
                    ]),
                Section::make(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.timing-information.title'))
                    ->columns(2)
                    ->schema([
                        Select::make('day_period')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.timing-information.fields.day-period'))
                            ->searchable()
                            ->preload()
                            ->options(DayPeriod::options())
                            ->required(),
                        Select::make('week_type')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.timing-information.fields.week-type'))
                            ->searchable()
                            ->preload()
                            ->options(WeekType::options()),
                        TimePicker::make('hour_from')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.timing-information.fields.work-from'))
                            ->native(false)
                            ->required(),
                        TimePicker::make('hour_to')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.timing-information.fields.work-to'))
                            ->native(false)
                            ->required(),
                    ]),
                Section::make(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.date-information.title'))
                    ->columns(2)
                    ->schema([
                        DatePicker::make('date_from')
                            ->native(false)
                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.date-information.fields.starting-date')),
                        DatePicker::make('date_to')
                            ->native(false)
                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.date-information.fields.ending-date')),
                    ]),
                Section::make(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.additional-information.title'))
                    ->columns(1)
                    ->schema([
                        Select::make('display_type')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.additional-information.fields.display-type'))
                            ->options(CalendarDisplayType::options()),
                        TextInput::make('duration_days')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.form.sections.additional-information.fields.durations-days'))
                            ->numeric()
                            ->default(1)
                            ->minValue(0)
                            ->maxValue(99999999999),
                        Hidden::make('creator_id')
                            ->default(Auth::user()->id),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->columns([
                TextColumn::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('day_of_week')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.columns.day-of-week'))
                    ->formatStateUsing(fn ($state) => DayOfWeek::options()[$state]),
                TextColumn::make('day_period')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.columns.day-period'))
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge()
                    ->color('secondary'),
                TextColumn::make('hour_from')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.columns.work-from')),
                TextColumn::make('hour_to')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.columns.work-to')),
                TextColumn::make('date_from')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.columns.starting-date'))
                    ->date(),
                TextColumn::make('date_to')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.columns.ending-date'))
                    ->date(),
                TextColumn::make('display_type')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.columns.display-type'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.columns.created-at'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.columns.updated-at'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('day_of_week')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.filters.day-of-week'))
                    ->options(DayOfWeek::options()),
                SelectFilter::make('display_type')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.filters.display-type'))
                    ->searchable()
                    ->preload()
                    ->options(CalendarDisplayType::options()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.actions.create.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.actions.create.notification.body')),
                    )
                    ->icon('heroicon-o-plus-circle')
                    ->hidden(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->flexible_hours ?? false)
                    ->mutateDataUsing(function (array $data) {
                        $data['sort'] = $this->getOwnerRecord()->attendance()->count() + 1;

                        return $data;
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.actions.edit.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.actions.edit.notification.body')),
                        )
                        ->mutateDataUsing(function (array $data) {
                            $data['sort'] = $this->getOwnerRecord()->attendance()->count() + 1;

                            return $data;
                        }),
                    ViewAction::make(),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.actions.delete.notification.body')),
                        ),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.bulk-actions.force-delete.notification.body')),
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.bulk-actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.table.bulk-actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->reorderable('sort');
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.general.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-clock')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.general.entries.name')),
                                        TextEntry::make('day_of_week')
                                            ->formatStateUsing(fn ($state) => DayOfWeek::options()[$state])
                                            ->placeholder('—')
                                            ->icon('heroicon-o-clock')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.general.entries.day-of-week')),
                                    ])->columns(2),
                                Section::make((__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.timing-information.title')))
                                    ->schema([
                                        TextEntry::make('day_period')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.timing-information.entries.day-period'))
                                            ->placeholder('—')
                                            ->formatStateUsing(fn ($state) => DayPeriod::options()[$state])
                                            ->icon('heroicon-o-clock'),
                                        TextEntry::make('week_type')
                                            ->formatStateUsing(fn ($state) => WeekType::options()[$state])
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.timing-information.entries.week-type'))
                                            ->placeholder('—')
                                            ->icon('heroicon-o-clock'),
                                        TextEntry::make('hour_from')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.timing-information.entries.work-from'))
                                            ->placeholder('—')
                                            ->icon('heroicon-o-clock'),
                                        TextEntry::make('hour_to')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.timing-information.entries.work-to'))
                                            ->placeholder('—')
                                            ->icon('heroicon-o-clock'),
                                    ])->columns(2),

                            ])->columnSpan(2),
                        Group::make([
                            Section::make((__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.date-information.title')))
                                ->schema([
                                    TextEntry::make('date_from')
                                        ->icon('heroicon-o-calendar')
                                        ->placeholder('—')
                                        ->date()
                                        ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.date-information.entries.starting-date')),
                                    TextEntry::make('date_to')
                                        ->icon('heroicon-o-calendar')
                                        ->placeholder('—')
                                        ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.date-information.entries.ending-date'))
                                        ->date(),
                                ]),
                            Section::make((__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.additional-information.title')))
                                ->schema([
                                    TextEntry::make('display_type')
                                        ->formatStateUsing(fn ($state) => CalendarDisplayType::options()[$state])
                                        ->placeholder('—')
                                        ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.additional-information.entries.display-type'))
                                        ->icon('heroicon-o-clock'),
                                    TextEntry::make('duration_days')
                                        ->placeholder('—')
                                        ->label(__('employees::filament/clusters/configurations/resources/calendar/relation-managers/working-hours.infolist.sections.additional-information.entries.durations-days'))
                                        ->icon('heroicon-o-clock'),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }
}
