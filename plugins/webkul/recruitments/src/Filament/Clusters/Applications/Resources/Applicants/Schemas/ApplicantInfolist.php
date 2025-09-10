<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Webkul\Recruitment\Enums\ApplicationStatus;
use Webkul\Recruitment\Models\Applicant;

class ApplicantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.title'))
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('priority')
                                                    ->hiddenLabel()
                                                    ->formatStateUsing(function ($state) {
                                                        $html = '<div class="flex gap-1" style="color: rgb(217 119 6);">';
                                                        for ($i = 1; $i <= 3; $i++) {
                                                            $iconType = $i <= $state ? 'heroicon-s-star' : 'heroicon-o-star';
                                                            $html .= view('filament::components.icon', [
                                                                'icon'  => $iconType,
                                                                'class' => 'w-5 h-5',
                                                            ])->render();
                                                        }

                                                        $html .= '</div>';

                                                        return new HtmlString($html);
                                                    })
                                                    ->placeholder('—'),
                                                TextEntry::make('stage.name')
                                                    ->hiddenLabel()
                                                    ->badge(),
                                                TextEntry::make('application_status')
                                                    ->hiddenLabel()
                                                    ->icon(null)
                                                    ->state(function (Applicant $record) {
                                                        return [
                                                            'label' => $record->application_status->getLabel(),
                                                            'color' => $record->application_status->getColor(),
                                                        ];
                                                    })
                                                    ->hidden(fn ($record) => $record->application_status->value === ApplicationStatus::ONGOING->value)
                                                    ->formatStateUsing(function ($record, $state) {
                                                        $html = '<span style="display: inline-flex; align-items: center; background-color: '.$record->application_status->getColor().'; color: white; padding: 4px 8px; border-radius: 12px; font-size: 18px; font-weight: 500;">';

                                                        $html .= view('filament::components.icon', [
                                                            'icon'  => $record->application_status->getIcon(),
                                                            'class' => 'w-6 h-6',
                                                        ])->render();

                                                        $html .= $record->application_status->getLabel();
                                                        $html .= '</span>';

                                                        return new HtmlString($html);
                                                    }),
                                            ])
                                            ->extraAttributes([
                                                'class' => 'flex',
                                            ])
                                            ->columns(2),
                                        TextEntry::make('candidate.name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.candidate-name')),
                                        TextEntry::make('candidate.email_from')
                                            ->icon('heroicon-o-envelope')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.email')),
                                        TextEntry::make('candidate.phone')
                                            ->icon('heroicon-o-phone')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.phone')),
                                        TextEntry::make('candidate.linkedin_profile')
                                            ->icon('heroicon-o-link')
                                            ->placeholder('—')
                                            ->url(fn ($record) => $record->candidate->linkedin_profile)
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.linkedin-profile')),
                                        TextEntry::make('job.name')
                                            ->icon('heroicon-o-briefcase')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.job-position')),
                                        TextEntry::make('recruiter.name')
                                            ->icon('heroicon-o-user-circle')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.recruiter')),
                                        TextEntry::make('recruiter.name')
                                            ->icon('heroicon-o-user-circle')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.recruiter')),
                                        TextEntry::make('categories.name')
                                            ->icon('heroicon-o-tag')
                                            ->placeholder('—')
                                            ->state(function (Applicant $record): array {
                                                $tags = $record->categories ?? $record->candidate->categories;

                                                return $tags->map(fn ($category) => [
                                                    'label' => $category->name,
                                                    'color' => $category->color ?? '#808080',
                                                ])->toArray();
                                            })
                                            ->badge()
                                            ->formatStateUsing(fn ($state) => $state['label'])
                                            ->color(fn ($state) => Color::generateV3Palette($state['color']))
                                            ->listWithLineBreaks()
                                            ->label('Tags'),
                                        TextEntry::make('interviewer.name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->badge()
                                            ->label('Interviewers'),
                                    ])
                                    ->columns(2),
                                Section::make()
                                    ->schema([
                                        TextEntry::make('applicant_notes')
                                            ->formatStateUsing(fn ($state) => new HtmlString($state))
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.notes')),
                                    ]),
                            ])->columnSpan(2),
                        Group::make()
                            ->schema([
                                Section::make(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.education-and-availability.title'))
                                    ->relationship('candidate', 'name')
                                    ->schema([
                                        TextEntry::make('degree.name')
                                            ->icon('heroicon-o-academic-cap')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.education-and-availability.entries.degree')),
                                        TextEntry::make('availability_date')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.education-and-availability.entries.availability-date')),
                                    ]),
                                Section::make(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.salary.title'))
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('salary_expected')
                                                    ->icon('heroicon-o-currency-dollar')
                                                    ->placeholder('—')
                                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.salary.entries.expected-salary')),
                                                TextEntry::make('salary_expected_extra')
                                                    ->icon('heroicon-o-currency-dollar')
                                                    ->placeholder('—')
                                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.salary.entries.salary-expected-extra')),
                                            ])->columns(2),
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('salary_proposed')
                                                    ->icon('heroicon-o-currency-dollar')
                                                    ->placeholder('—')
                                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.salary.entries.proposed-salary')),
                                                TextEntry::make('salary_proposed_extra')
                                                    ->icon('heroicon-o-currency-dollar')
                                                    ->placeholder('—')
                                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.salary.entries.salary-proposed-extra')),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.source-and-medium.title'))
                                    ->schema([
                                        TextEntry::make('source.name')
                                            ->icon('heroicon-o-globe-alt')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.source-and-medium.entries.source')),
                                        TextEntry::make('medium.name')
                                            ->icon('heroicon-o-globe-alt')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.source-and-medium.entries.medium')),
                                    ]),
                            ])->columnSpan(1),
                    ])->columnSpanFull(),
            ]);
    }
}
