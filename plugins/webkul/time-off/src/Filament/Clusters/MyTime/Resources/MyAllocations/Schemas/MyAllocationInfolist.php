<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\TimeOff\Enums\AllocationType;
use Webkul\TimeOff\Enums\State;

class MyAllocationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-details.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-details.entries.name')),
                                        TextEntry::make('holidayStatus.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-clock')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-details.entries.time-off-type')),
                                        TextEntry::make('allocation_type')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-queue-list')
                                            ->formatStateUsing(fn ($state) => AllocationType::options()[$state])
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-details.entries.allocation-type')),
                                    ])->columns(2),
                                Section::make(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.validity-period.title'))
                                    ->schema([
                                        TextEntry::make('date_from')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.validity-period.entries.date-from'))
                                            ->placeholder('—'),
                                        TextEntry::make('date_to')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.validity-period.entries.date-to'))
                                            ->placeholder('—'),
                                        TextEntry::make('notes')
                                            ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.validity-period.entries.reason'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-status.title'))
                                ->schema([
                                    TextEntry::make('number_of_days')
                                        ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-status.entries.allocation'))
                                        ->placeholder('—')
                                        ->icon('heroicon-o-calculator')
                                        ->numeric(),
                                    TextEntry::make('state')
                                        ->placeholder('—')
                                        ->icon('heroicon-o-flag')
                                        ->formatStateUsing(fn ($state) => State::options()[$state])
                                        ->label(__('time-off::filament/clusters/my-time/resources/my-allocation.infolist.sections.allocation-status.entries.state')),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }
}
