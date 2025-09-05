<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobPositionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-briefcase')
                                            ->placeholder('—')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.job-position-title')),
                                        TextEntry::make('department.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.department')),
                                        TextEntry::make('company.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.company')),
                                    ])->columns(2),
                                Section::make(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.job-description.title'))
                                    ->schema([
                                        TextEntry::make('description')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.job-description.entries.job-description'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                        TextEntry::make('requirements')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.job-description.entries.job-requirements'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.title'))
                                ->schema([
                                    TextEntry::make('expected_employees')
                                        ->label(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.expected-employees'))
                                        ->placeholder('—')
                                        ->icon('heroicon-o-user-group')
                                        ->numeric(),
                                    TextEntry::make('no_of_employee')
                                        ->icon('heroicon-o-user-group')
                                        ->placeholder('—')
                                        ->label(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.current-employees'))
                                        ->numeric(),
                                    TextEntry::make('no_of_recruitment')
                                        ->icon('heroicon-o-user-group')
                                        ->placeholder('—')
                                        ->label(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.recruitment-target'))
                                        ->numeric(),
                                    TextEntry::make('employmentType.name')
                                        ->placeholder('—')
                                        ->icon('heroicon-o-briefcase')
                                        ->label(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.employment-type')),
                                    IconEntry::make('is_active')
                                        ->label(__('employees::filament/clusters/configurations/resources/job-position.infolist.sections.position-status.entries.status')),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }
}
