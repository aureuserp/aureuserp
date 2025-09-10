<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CalendarInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.general.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.general.entries.name')),
                                        TextEntry::make('timezone')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-clock')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.general.entries.timezone')),
                                        TextEntry::make('company.name')
                                            ->icon('heroicon-o-building-office-2')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.general.entries.company')),
                                    ])->columns(2),
                                Section::make(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.configuration.title'))
                                    ->schema([
                                        TextEntry::make('hours_per_day')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.configuration.entries.hours-per-day'))
                                            ->icon('heroicon-o-clock')
                                            ->date(),
                                        TextEntry::make('full_time_required_hours')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.configuration.entries.full-time-required-hours'))
                                            ->icon('heroicon-o-clock')
                                            ->date(),
                                    ])->columns(2),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.flexibility.title'))
                                ->schema([
                                    IconEntry::make('is_active')
                                        ->boolean()
                                        ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.flexibility.entries.status')),
                                    IconEntry::make('two_weeks_calendar')
                                        ->boolean()
                                        ->placeholder('—')
                                        ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.flexibility.entries.two-weeks-calendar')),
                                    IconEntry::make('flexible_hours')
                                        ->placeholder('—')
                                        ->boolean()
                                        ->label(__('employees::filament/clusters/configurations/resources/calendar.infolist.sections.flexibility.entries.flexible-hours')),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }
}
