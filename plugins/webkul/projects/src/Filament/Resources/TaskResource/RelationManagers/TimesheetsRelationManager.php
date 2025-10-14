<?php

namespace Webkul\Project\Filament\Resources\TaskResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Project\Settings\TimeSettings;

class TimesheetsRelationManager extends RelationManager
{
    protected static string $relationship = 'timesheets';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! app(TimeSettings::class)->enable_timesheets) {
            return false;
        }

        if (! $ownerRecord->project) {
            return true;
        }

        return $ownerRecord->project->allow_timesheets;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('type')
                    ->default('projects'),
                DatePicker::make('date')
                    ->label(__('projects::filament/resources/task/relation-managers/timesheets.form.date'))
                    ->required()
                    ->native(false),
                Select::make('user_id')
                    ->label(__('projects::filament/resources/task/relation-managers/timesheets.form.employee'))
                    ->required()
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->label(__('projects::filament/resources/task/relation-managers/timesheets.form.description')),
                TextInput::make('unit_amount')
                    ->label(__('projects::filament/resources/task/relation-managers/timesheets.form.time-spent'))
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->helperText(__('projects::filament/resources/task/relation-managers/timesheets.form.time-spent-helper-text')),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('date')
                    ->label(__('projects::filament/resources/task/relation-managers/timesheets.table.columns.date'))
                    ->date('Y-m-d'),
                TextColumn::make('user.name')
                    ->label(__('projects::filament/resources/task/relation-managers/timesheets.table.columns.employee')),
                TextColumn::make('name')
                    ->label(__('projects::filament/resources/task/relation-managers/timesheets.table.columns.description')),
                TextColumn::make('unit_amount')
                    ->label(__('projects::filament/resources/task/relation-managers/timesheets.table.columns.time-spent'))
                    ->formatStateUsing(function ($state) {
                        $hours = floor($state);
                        $minutes = ($state - $hours) * 60;

                        return $hours.':'.$minutes;
                    })
                    ->summarize([
                        Sum::make()
                            ->label(__('projects::filament/resources/task/relation-managers/timesheets.table.columns.time-spent'))
                            ->formatStateUsing(function ($state) {
                                $hours = floor($state);
                                $minutes = ($state - $hours) * 60;

                                return $hours.':'.$minutes;
                            }),
                        Sum::make()
                            ->label(__('projects::filament/resources/task/relation-managers/timesheets.table.columns.time-spent-on-subtasks'))
                            ->formatStateUsing(function ($state) {
                                $subtaskHours = $this->getOwnerRecord()->subtask_effective_hours;
                                $hours = floor($subtaskHours);
                                $minutes = ($subtaskHours - $hours) * 60;

                                return $hours.':'.$minutes;
                            }),
                        Sum::make()
                            ->label(__('projects::filament/resources/task/relation-managers/timesheets.table.columns.total-time-spent'))
                            ->formatStateUsing(function ($state) {
                                $subtaskHours = $this->getOwnerRecord()->total_hours_spent;
                                $hours = floor($subtaskHours);
                                $minutes = ($subtaskHours - $hours) * 60;

                                return $hours.':'.$minutes;
                            }),
                        Sum::make()
                            ->label(__('projects::filament/resources/task/relation-managers/timesheets.table.columns.remaining-time'))
                            ->formatStateUsing(function () {
                                $remainingHours = $this->getOwnerRecord()->remaining_hours;

                                $hours = floor($remainingHours);
                                $minutes = ($remainingHours - $hours) * 60;

                                return $hours.':'.$minutes;
                            })
                            ->visible((bool) $this->getOwnerRecord()->allocated_hours),
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('projects::filament/resources/task/relation-managers/timesheets.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['creator_id'] = Auth::id();

                        $ownerRecord = $this->getOwnerRecord();

                        $data['project_id'] = $ownerRecord->project_id;

                        $data['partner_id'] = $ownerRecord->partner_id ?? $ownerRecord->project?->partner_id;

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/resources/task/relation-managers/timesheets.table.header-actions.create.notification.title'))
                            ->body(__('projects::filament/resources/task/relation-managers/timesheets.table.header-actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/resources/task/relation-managers/timesheets.table.actions.edit.notification.title'))
                            ->body(__('projects::filament/resources/task/relation-managers/timesheets.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/resources/task/relation-managers/timesheets.table.actions.delete.notification.title'))
                            ->body(__('projects::filament/resources/task/relation-managers/timesheets.table.actions.delete.notification.body')),
                    ),
            ])
            ->paginated(false);
    }
}
