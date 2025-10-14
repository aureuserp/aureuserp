<?php

namespace Webkul\Timesheet\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Project\Models\Timesheet;
use Webkul\Timesheet\Filament\Resources\TimesheetResource\Pages\ManageTimesheets;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    public static function getNavigationLabel(): string
    {
        return __('timesheets::filament/resources/timesheet.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('timesheets::filament/resources/timesheet.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('type')
                    ->default('projects'),
                DatePicker::make('date')
                    ->label(__('timesheets::filament/resources/timesheet.form.date'))
                    ->required()
                    ->native(false),
                Select::make('user_id')
                    ->label(__('timesheets::filament/resources/timesheet.form.employee'))
                    ->required()
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('project_id')
                    ->label(__('timesheets::filament/resources/timesheet.form.project'))
                    ->required()
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('task_id', null);
                    }),
                Select::make('task_id')
                    ->label(__('timesheets::filament/resources/timesheet.form.task'))
                    ->required()
                    ->relationship(
                        name: 'task',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('project_id', $get('project_id')),
                    )
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->label(__('timesheets::filament/resources/timesheet.form.description')),
                TextInput::make('unit_amount')
                    ->label(__('timesheets::filament/resources/timesheet.form.time-spent'))
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->helperText(__('timesheets::filament/resources/timesheet.form.time-spent-helper-text')),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('date')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.date'))
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.employee'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('project.name')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.project'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('task.title')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.task'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.description'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('unit_amount')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.time-spent'))
                    ->formatStateUsing(function ($state) {
                        $hours = floor($state);
                        $minutes = ($state - $hours) * 60;

                        return $hours.':'.$minutes;
                    })
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->label(__('timesheets::filament/resources/timesheet.table.columns.time-spent'))
                            ->formatStateUsing(function ($state) {
                                $hours = floor($state);
                                $minutes = ($state - $hours) * 60;

                                return $hours.':'.$minutes;
                            }),
                    ]),
                TextColumn::make('created_at')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('date')
                    ->label(__('timesheets::filament/resources/timesheet.table.groups.date'))
                    ->date(),
                Group::make('user.name')
                    ->label(__('timesheets::filament/resources/timesheet.table.groups.employee')),
                Group::make('project.name')
                    ->label(__('timesheets::filament/resources/timesheet.table.groups.project')),
                Group::make('task.title')
                    ->label(__('timesheets::filament/resources/timesheet.table.groups.task')),
                Group::make('creator.name')
                    ->label(__('timesheets::filament/resources/timesheet.table.groups.creator')),
            ])
            ->filters([
                Filter::make('date')
                    ->schema([
                        DatePicker::make('date_from')
                            ->label(__('timesheets::filament/resources/timesheet.table.filters.date-from'))
                            ->native(false)
                            ->placeholder(fn ($state): string => 'Dec 18, '.now()->subYear()->format('Y')),
                        DatePicker::make('date_until')
                            ->label(__('timesheets::filament/resources/timesheet.table.filters.date-until'))
                            ->native(false)
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators['date_from'] = 'Order from '.Carbon::parse($data['date_from'])->toFormattedDateString();
                        }
                        if ($data['date_until'] ?? null) {
                            $indicators['date_until'] = 'Order until '.Carbon::parse($data['date_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                SelectFilter::make('user_id')
                    ->label(__('timesheets::filament/resources/timesheet.table.filters.employee'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('project_id')
                    ->label(__('timesheets::filament/resources/timesheet.table.filters.project'))
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('task_id')
                    ->label(__('timesheets::filament/resources/timesheet.table.filters.task'))
                    ->relationship('task', 'title')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('creator_id')
                    ->label(__('timesheets::filament/resources/timesheet.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('timesheets::filament/resources/timesheet.table.actions.edit.notification.title'))
                            ->body(__('timesheets::filament/resources/timesheet.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('timesheets::filament/resources/timesheet.table.actions.delete.notification.title'))
                            ->body(__('timesheets::filament/resources/timesheet.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('timesheets::filament/resources/timesheet.table.bulk-actions.delete.notification.title'))
                                ->body(__('timesheets::filament/resources/timesheet.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTimesheets::route('/'),
        ];
    }
}
