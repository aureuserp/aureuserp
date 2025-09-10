<?php

namespace Webkul\Timesheet\Filament\Resources\Timesheets;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TimesheetForm
{
    public static function configure(Schema $schema): Schema
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
}
