<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\Schemas;

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
                                Section::make(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-briefcase')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.job-position-title')),
                                        TextEntry::make('department.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.department')),
                                        TextEntry::make('manager.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.manager')),
                                        TextEntry::make('company.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.company')),
                                        TextEntry::make('recruiter.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.recruiter')),
                                        TextEntry::make('interviewers.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-user')
                                            ->listWithLineBreaks()
                                            ->badge()
                                            ->listWithLineBreaks()
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.interviewers')),
                                        TextEntry::make('address.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office-2')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.job-location')),
                                        TextEntry::make('industry.name')
                                            ->placeholder('—')
                                            ->icon('heroicon-o-building-office-2')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.industry')),
                                    ])->columns(2),
                                Section::make(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.job-description.title'))
                                    ->schema([
                                        TextEntry::make('description')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.job-description.entries.job-description'))
                                            ->placeholder('—')
                                            ->html()
                                            ->columnSpanFull(),
                                        TextEntry::make('requirements')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.job-description.entries.job-requirements'))
                                            ->placeholder('—')
                                            ->html()
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make()
                                ->schema([
                                    TextEntry::make('date_from')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.date-from'))
                                        ->placeholder('—')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d') : null)
                                        ->icon('heroicon-o-calendar'),
                                    TextEntry::make('date_to')
                                        ->icon('heroicon-o-calendar')
                                        ->placeholder('—')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d') : null)
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.date-to')),
                                ]),
                            Section::make(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.title'))
                                ->schema([
                                    TextEntry::make('expected_employees')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.expected-employees'))
                                        ->placeholder('—')
                                        ->icon('heroicon-o-user-group')
                                        ->numeric(),
                                    TextEntry::make('no_of_employee')
                                        ->icon('heroicon-o-user-group')
                                        ->placeholder('—')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.current-employees'))
                                        ->numeric(),
                                    TextEntry::make('no_of_recruitment')
                                        ->icon('heroicon-o-user-group')
                                        ->placeholder('—')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.work-planning.entries.recruitment-target'))
                                        ->numeric(),
                                    TextEntry::make('employmentType.name')
                                        ->placeholder('—')
                                        ->icon('heroicon-o-briefcase')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.employment-information.entries.employment-type')),
                                    IconEntry::make('is_active')
                                        ->label(__('recruitments::filament/clusters/configurations/resources/job-position.infolist.sections.position-status.entries.status')),
                                    TextEntry::make('skills.name')
                                        ->listWithLineBreaks()
                                        ->badge()
                                        ->listWithLineBreaks()
                                        ->label(__('Expected Skills')),
                                ]),
                        ])->columnSpan(1),
                    ])->columnSpanFull(),
            ]);
    }
}
