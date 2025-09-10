<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\TimeOff\Enums\AllocationType;
use Webkul\TimeOff\Enums\State;

class MyAllocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        ProgressStepper::make('state')
                            ->hiddenLabel()
                            ->inline()
                            ->options(function ($record) {
                                $onlyStates = [
                                    State::CONFIRM->value,
                                    State::VALIDATE_TWO->value,
                                ];

                                if ($record) {
                                    if ($record->state === State::REFUSE->value) {
                                        $onlyStates[] = State::REFUSE->value;
                                    }
                                }

                                return collect(State::options())->only($onlyStates)->toArray();
                            })
                            ->default(State::CONFIRM->value)
                            ->columnSpan('full')
                            ->disabled()
                            ->reactive()
                            ->live(),
                    ])->columns(1),
                Section::make()
                    ->columns(1)
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.name'))
                                    ->placeholder(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.name-placeholder'))
                                    ->required(),
                                Grid::make(1)
                                    ->schema([
                                        Select::make('holiday_status_id')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.time-off-type'))
                                            ->relationship('holidayStatus', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                    ]),
                                Radio::make('allocation_type')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.allocation-type'))
                                    ->options(AllocationType::class)
                                    ->default(AllocationType::REGULAR->value)
                                    ->required(),
                                Fieldset::make(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.validity-period'))
                                    ->schema([
                                        DatePicker::make('date_from')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.date-from'))
                                            ->native(false)
                                            ->required()
                                            ->default(now()),
                                        DatePicker::make('date_to')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.date-to'))
                                            ->native(false)
                                            ->placeholder(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.date-to-placeholder')),
                                    ]),
                                TextInput::make('number_of_days')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.allocation'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required()
                                    ->suffix(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.allocation-suffix')),
                                RichEditor::make('notes')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.form.fields.reason')),
                            ]),
                    ])->columns(1),
            ]);
    }
}
