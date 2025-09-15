<?php

namespace Webkul\Project\Filament\Resources\TaskResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Settings\TimeSettings;

class ManageTimesheets extends ManageRelatedRecords
{
    protected static string $resource = TaskResource::class;

    protected static string $relationship = 'timesheets';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/resources/task/pages/manage-timesheets.title');
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function canAccess(array $parameters = []): bool
    {
        $canAccess = parent::canAccess($parameters);

        if (! $canAccess) {
            return false;
        }

        if (! app(TimeSettings::class)->enable_timesheets) {
            return false;
        }

        if (! $parameters['record']->project) {
            return true;
        }

        return $parameters['record']->project->allow_timesheets;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('type')
                    ->default('projects'),
                DatePicker::make('date')
                    ->label(__('projects::filament/resources/task/pages/manage-timesheets.form.date'))
                    ->required()
                    ->native(false),
                Select::make('user_id')
                    ->label(__('projects::filament/resources/task/pages/manage-timesheets.form.employee'))
                    ->required()
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->label(__('projects::filament/resources/task/pages/manage-timesheets.form.description')),
                TextInput::make('unit_amount')
                    ->label(__('projects::filament/resources/task/pages/manage-timesheets.form.time-spent'))
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->helperText(__('projects::filament/resources/task/pages/manage-timesheets.form.time-spent-helper-text')),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('date')
                    ->label(__('projects::filament/resources/task/pages/manage-timesheets.table.columns.date'))
                    ->date('Y-m-d'),
                TextColumn::make('user.name')
                    ->label(__('projects::filament/resources/task/pages/manage-timesheets.table.columns.employee')),
                TextColumn::make('name')
                    ->label(__('projects::filament/resources/task/pages/manage-timesheets.table.columns.description')),
                TextColumn::make('unit_amount')
                    ->label(__('projects::filament/resources/task/pages/manage-timesheets.table.columns.time-spent'))
                    ->formatStateUsing(function ($state) {
                        $hours = floor($state);
                        $minutes = ($hours - $hours) * 60;

                        return $hours.':'.$minutes;
                    })
                    ->summarize([
                        Sum::make()
                            ->label(__('projects::filament/resources/task/pages/manage-timesheets.table.columns.time-spent'))
                            ->formatStateUsing(function ($state) {
                                $hours = floor($state);
                                $minutes = ($state - $hours) * 60;

                                return $hours.':'.$minutes;
                            }),
                        Sum::make()
                            ->label(__('projects::filament/resources/task/pages/manage-timesheets.table.columns.time-spent-on-subtasks'))
                            ->formatStateUsing(function ($state) {
                                $subtaskHours = $this->getOwnerRecord()->subtask_effective_hours;
                                $hours = floor($subtaskHours);
                                $minutes = ($subtaskHours - $hours) * 60;

                                return $hours.':'.$minutes;
                            }),
                        Sum::make()
                            ->label(__('projects::filament/resources/task/pages/manage-timesheets.table.columns.total-time-spent'))
                            ->formatStateUsing(function ($state) {
                                $subtaskHours = $this->getOwnerRecord()->total_hours_spent;
                                $hours = floor($subtaskHours);
                                $minutes = ($subtaskHours - $hours) * 60;

                                return $hours.':'.$minutes;
                            }),
                        Sum::make()
                            ->label(__('projects::filament/resources/task/pages/manage-timesheets.table.columns.remaining-time'))
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
                    ->label(__('projects::filament/resources/task/pages/manage-timesheets.table.header-actions.create.label'))
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
                            ->title(__('projects::filament/resources/task/pages/manage-timesheets.table.header-actions.create.notification.title'))
                            ->body(__('projects::filament/resources/task/pages/manage-timesheets.table.header-actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/resources/task/pages/manage-timesheets.table.actions.edit.notification.title'))
                            ->body(__('projects::filament/resources/task/pages/manage-timesheets.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/resources/task/pages/manage-timesheets.table.actions.delete.notification.title'))
                            ->body(__('projects::filament/resources/task/pages/manage-timesheets.table.actions.delete.notification.body')),
                    ),
            ])
            ->paginated(false);
    }
}
