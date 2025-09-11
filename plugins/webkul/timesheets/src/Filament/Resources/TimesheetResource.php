<?php

namespace Webkul\Timesheet\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Project\Models\Timesheet;
use Webkul\Timesheet\Filament\Resources\TimesheetResource\Pages;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function getNavigationLabel(): string
    {
        return __('timesheets::filament/resources/timesheet.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('timesheets::filament/resources/timesheet.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('type')
                    ->default('projects'),
                Forms\Components\DatePicker::make('date')
                    ->label(__('timesheets::filament/resources/timesheet.form.date'))
                    ->required()
                    ->native(false),
                Forms\Components\Select::make('user_id')
                    ->label(__('timesheets::filament/resources/timesheet.form.employee'))
                    ->required()
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('project_id')
                    ->label(__('timesheets::filament/resources/timesheet.form.project'))
                    ->required()
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set) {
                        $set('task_id', null);
                    }),
                Forms\Components\Select::make('task_id')
                    ->label(__('timesheets::filament/resources/timesheet.form.task'))
                    ->required()
                    ->relationship(
                        name: 'task',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Forms\Get $get, Builder $query) => $query->where('project_id', $get('project_id')),
                    )
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->label(__('timesheets::filament/resources/timesheet.form.description')),
                Forms\Components\TextInput::make('unit_amount')
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
                Tables\Columns\TextColumn::make('date')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.date'))
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.employee'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.project'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('task.title')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.task'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.description'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_amount')
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('timesheets::filament/resources/timesheet.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('date')
                    ->label(__('timesheets::filament/resources/timesheet.table.groups.date'))
                    ->date(),
                Tables\Grouping\Group::make('user.name')
                    ->label(__('timesheets::filament/resources/timesheet.table.groups.employee')),
                Tables\Grouping\Group::make('project.name')
                    ->label(__('timesheets::filament/resources/timesheet.table.groups.project')),
                Tables\Grouping\Group::make('task.title')
                    ->label(__('timesheets::filament/resources/timesheet.table.groups.task')),
                Tables\Grouping\Group::make('creator.name')
                    ->label(__('timesheets::filament/resources/timesheet.table.groups.creator')),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(__('timesheets::filament/resources/timesheet.table.filters.date-from'))
                            ->native(false)
                            ->placeholder(fn ($state): string => 'Dec 18, '.now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('date_until')
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
                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('timesheets::filament/resources/timesheet.table.filters.employee'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('project_id')
                    ->label(__('timesheets::filament/resources/timesheet.table.filters.project'))
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('task_id')
                    ->label(__('timesheets::filament/resources/timesheet.table.filters.task'))
                    ->relationship('task', 'title')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('creator_id')
                    ->label(__('timesheets::filament/resources/timesheet.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('timesheets::filament/resources/timesheet.table.actions.edit.notification.title'))
                            ->body(__('timesheets::filament/resources/timesheet.table.actions.edit.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('timesheets::filament/resources/timesheet.table.actions.delete.notification.title'))
                            ->body(__('timesheets::filament/resources/timesheet.table.actions.delete.notification.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
            'index' => Pages\ManageTimesheets::route('/'),
        ];
    }
}
