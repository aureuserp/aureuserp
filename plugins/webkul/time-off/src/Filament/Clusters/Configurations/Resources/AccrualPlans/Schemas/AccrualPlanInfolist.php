<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlans\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AccrualPlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 2])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('Basic Information'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.name')),
                                        IconEntry::make('is_based_on_worked_time')
                                            ->boolean()
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.is-based-on-worked-time')),
                                        TextEntry::make('accrued_gain_time')
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.accrued-gain-time')),
                                        TextEntry::make('carryover_date')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.carry-over-time')),
                                        TextEntry::make('carryover_day')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('Carryover Day'))
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.carry-over-day')),
                                        TextEntry::make('carryover_month')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('Carryover Month'))
                                            ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.infolist.entries.carry-over-month')),
                                    ]),
                            ])
                            ->columnSpan(2),
                    ])->columnSpanFull(),
            ]);
    }
}
