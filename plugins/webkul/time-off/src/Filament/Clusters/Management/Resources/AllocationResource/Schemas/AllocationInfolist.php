<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\TimeOff\Enums\State;

class AllocationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                InfolistProgressStepper::make('state')
                                    ->hiddenLabel()
                                    ->inline()
                                    ->options(function ($record) {
                                        $onlyStates = [
                                            State::CONFIRM->value,
                                            State::VALIDATE_TWO->value,
                                        ];

                                        if ($record->state === State::REFUSE->value) {
                                            $onlyStates[] = State::REFUSE->value;
                                        }

                                        return collect(State::options())->only($onlyStates)->toArray();
                                    })
                                    ->default(State::CONFIRM->value)
                                    ->columnSpan('full'),

                                Section::make(__('time-off::filament/clusters/management/resources/allocation.infolist.sections.allocation-details.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/management/resources/allocation.infolist.sections.allocation-details.entries.name')),
                                        TextEntry::make('holidayStatus.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-clock')
                                            ->label(__('time-off::filament/clusters/management/resources/allocation.infolist.sections.allocation-details.entries.time-off-type')),
                                        TextEntry::make('allocation_type')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-queue-list')
                                            ->formatStateUsing(fn ($state) => $state->getLabel())
                                            ->label(__('time-off::filament/clusters/management/resources/allocation.infolist.sections.allocation-details.entries.allocation-type')),
                                    ])->columns(2),
                                Section::make(__('time-off::filament/clusters/management/resources/allocation.infolist.sections.validity-period.title'))
                                    ->schema([
                                        TextEntry::make('date_from')
                                            ->label(__('time-off::filament/clusters/management/resources/allocation.infolist.sections.validity-period.entries.date-from'))
                                            ->placeholder('—'),
                                        TextEntry::make('date_to')
                                            ->label(__('time-off::filament/clusters/management/resources/allocation.infolist.sections.validity-period.entries.date-to'))
                                            ->placeholder('—'),
                                        TextEntry::make('notes')
                                            ->label(__('time-off::filament/clusters/management/resources/allocation.infolist.sections.validity-period.entries.reason'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make(__('time-off::filament/clusters/management/resources/allocation.infolist.sections.allocation-status.title'))
                                ->schema([
                                    TextEntry::make('number_of_days')
                                        ->label(__('time-off::filament/clusters/management/resources/allocation.infolist.sections.allocation-status.entries.allocation'))
                                        ->placeholder('—')
                                        ->icon('heroicon-o-calculator')
                                        ->numeric(),
                                    TextEntry::make('state')
                                        ->placeholder('—')
                                        ->icon('heroicon-o-flag')
                                        ->formatStateUsing(fn ($state) => State::options()[$state])
                                        ->label(__('time-off::filament/clusters/management/resources/allocation.infolist.sections.allocation-status.entries.state')),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ])
            ->columns(1);
    }
}
