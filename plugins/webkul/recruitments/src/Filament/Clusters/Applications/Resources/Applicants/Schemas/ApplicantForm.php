<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Recruitment\Enums\ApplicationStatus;
use Webkul\Recruitment\Enums\RecruitmentState as RecruitmentStateEnum;
use Webkul\Recruitment\Models\Applicant;
use Webkul\Recruitment\Models\Candidate;
use Webkul\Recruitment\Models\JobPosition;
use Webkul\Recruitment\Models\Stage as RecruitmentStage;
use Webkul\Security\Filament\Resources\Users\UserResource;

class ApplicantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        ProgressStepper::make('stage_id')
                            ->hiddenLabel()
                            ->inline()
                            ->options(fn () => RecruitmentStage::orderBy('sort')->get()->mapWithKeys(fn ($stage) => [$stage->id => $stage->name]))
                            ->columnSpan('full')
                            ->live()
                            ->reactive()
                            ->hidden(function ($record, Set $set) {
                                if ($record->refuse_reason_id) {
                                    $set('stage_id', null);

                                    return true;
                                }
                            })
                            ->afterStateUpdated(function ($state, Applicant $record) {
                                if ($record && $state) {
                                    DB::transaction(function () use ($state, $record) {
                                        $selectedStage = RecruitmentStage::find($state);

                                        if ($selectedStage && $selectedStage->hired_stage) {
                                            $record->setAsHired();
                                        } elseif ($record->stage && $record->stage->hired_stage) {
                                            $record->reopen();
                                        }

                                        $record->updateStage([
                                            'stage_id'                => $state,
                                            'last_stage_id'           => $record->stage_id,
                                            'date_last_stage_updated' => now(),
                                            'state'                   => RecruitmentStateEnum::NORMAL->value,
                                        ]);
                                    });
                                }
                            }),
                    ])->columnSpanFull(),
                Grid::make()
                    ->schema([
                        Section::make(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.title'))
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Actions::make([
                                            Action::make('good')
                                                ->hiddenLabel()
                                                ->outlined(false)
                                                ->icon(fn ($record) => $record?->priority >= 1 ? 'heroicon-s-star' : 'heroicon-o-star')
                                                ->color('warning')
                                                ->size(Size::ExtraLarge)
                                                ->iconButton()
                                                ->tooltip(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.evaluation-good'))
                                                ->action(function ($record) {
                                                    if ($record?->priority == 1) {
                                                        $record->update(['priority' => 0]);
                                                        $record->candidate->update(['priority' => 0]);
                                                    } else {
                                                        $record->update(['priority' => 1]);
                                                        $record->candidate->update(['priority' => 1]);
                                                    }
                                                }),
                                            Action::make('veryGood')
                                                ->hiddenLabel()
                                                ->icon(fn ($record) => $record?->priority >= 2 ? 'heroicon-s-star' : 'heroicon-o-star')
                                                ->color('warning')
                                                ->size(Size::ExtraLarge)
                                                ->iconButton()
                                                ->tooltip(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.evaluation-very-good'))
                                                ->action(function ($record) {
                                                    if ($record?->priority == 2) {
                                                        $record->update(['priority' => 0]);
                                                        $record->candidate->update(['priority' => 0]);
                                                    } else {
                                                        $record->update(['priority' => 2]);
                                                        $record->candidate->update(['priority' => 2]);
                                                    }
                                                }),
                                            Action::make('excellent')
                                                ->hiddenLabel()
                                                ->icon(fn ($record) => $record?->priority >= 3 ? 'heroicon-s-star' : 'heroicon-o-star')
                                                ->color('warning')
                                                ->size(Size::ExtraLarge)
                                                ->iconButton()
                                                ->tooltip(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.evaluation-very-excellent'))
                                                ->action(function ($record) {
                                                    if ($record?->priority == 3) {
                                                        $record->update(['priority' => 0]);
                                                        $record->candidate->update(['priority' => 0]);
                                                    } else {
                                                        $record->update(['priority' => 3]);
                                                        $record->candidate->update(['priority' => 3]);
                                                    }
                                                }),
                                        ]),
                                        Placeholder::make('application_status')
                                            ->live()
                                            ->hiddenLabel()
                                            ->hidden(fn ($record) => $record->application_status->value === ApplicationStatus::ONGOING->value)
                                            ->content(function ($record) {
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
                                        'class' => 'flex !items-center justify-between',
                                    ])
                                    ->columns(2),
                                Group::make()
                                    ->schema([
                                        Select::make('candidate_id')
                                            ->relationship('candidate', 'name')
                                            ->required()
                                            ->preload()
                                            ->searchable()
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.candidate-name'))
                                            ->live()
                                            ->afterStateHydrated(function (Set $set, Get $get, $state) {
                                                if ($state) {
                                                    $candidate = Candidate::find($state);

                                                    $set('candidate.email_from', $candidate?->email_from);
                                                    $set('candidate.phone', $candidate?->phone);
                                                    $set('candidate.linkedin_profile', $candidate?->linkedin_profile);
                                                }
                                            })
                                            ->columnSpan(1),
                                        TextInput::make('candidate.email_from')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.email'))
                                            ->email()
                                            ->columnSpan(1),
                                        TextInput::make('candidate.phone')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.phone'))
                                            ->tel()
                                            ->columnSpan(1),
                                        TextInput::make('candidate.linkedin_profile')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.linkedin-profile'))
                                            ->url()
                                            ->columnSpan(1),
                                        Select::make('job_id')
                                            ->relationship('job', 'name')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.job-position'))
                                            ->preload()
                                            ->live()
                                            ->reactive()
                                            ->afterStateHydrated(function (Set $set, Get $get, $state) {
                                                if (! $get('stage_id') && $state) {
                                                    $set('stage_id', RecruitmentStage::where('is_default', 1)->first()->id ?? null);
                                                }
                                            })
                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state, ?string $old) {
                                                if (is_null($state)) {
                                                    $set('stage_id', null);

                                                    return;
                                                }

                                                if (is_null($old) && $state) {
                                                    $set('stage_id', RecruitmentStage::where('is_default', 1)->first()->id ?? null);
                                                }

                                                if (! is_null($old) && ! is_null($state)) {
                                                    $jobPosition = JobPosition::find($state);

                                                    if ($jobPosition) {
                                                        if ($jobPosition->recruiter_id) {
                                                            $set('recruiter', $jobPosition->recruiter_id);
                                                        }

                                                        if ($jobPosition->interviewers) {
                                                            $set('recruitments_applicant_interviewers', $jobPosition->interviewers->pluck('id')->toArray() ?? []);
                                                        }

                                                        if ($jobPosition->department_id) {
                                                            $set('department_id', $jobPosition->department_id);
                                                        }
                                                    }
                                                }
                                            })
                                            ->searchable(),
                                        DatePicker::make('date_closed')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.hired-date'))
                                            ->hidden(fn ($record) => ! $record->date_closed)
                                            ->visible()
                                            ->disabled()
                                            ->live()
                                            ->columnSpan(1),
                                        Select::make('recruiter')
                                            ->relationship('recruiter', 'name')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.recruiter'))
                                            ->preload()
                                            ->live()
                                            ->reactive()
                                            ->searchable(),
                                        Select::make('recruitments_applicant_interviewers')
                                            ->relationship('interviewer', 'name')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.interviewer'))
                                            ->preload()
                                            ->multiple()
                                            ->searchable()
                                            ->dehydrated(true)
                                            ->saveRelationshipsUsing(function () {})
                                            ->createOptionForm(fn (Schema $schema) => UserResource::form($schema)),
                                        Select::make('recruitments_applicant_applicant_categories')
                                            ->multiple()
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.tags'))
                                            ->afterStateHydrated(function (Select $component, $state, $record) {
                                                if (
                                                    empty($state)
                                                    && $record?->candidate
                                                ) {
                                                    $component->state($record->candidate->categories->pluck('id')->toArray());
                                                }
                                            })
                                            ->relationship('categories', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->columns(2),
                            ])->columnSpanFull(),
                        Section::make()
                            ->schema([
                                RichEditor::make('applicant_notes')
                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.notes'))
                                    ->columnSpan(2),
                            ])->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 2]),
                Grid::make()
                    ->schema([
                        Section::make(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.education-and-availability.title'))
                            ->relationship('candidate', 'name')
                            ->schema([
                                Select::make('degree_id')
                                    ->relationship('degree', 'name')
                                    ->searchable()
                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.education-and-availability.fields.degree'))
                                    ->preload(),
                                DatePicker::make('availability_date')
                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.education-and-availability.fields.availability-date'))
                                    ->native(false),
                            ])->columnSpanFull(),
                        Section::make(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.department.title'))
                            ->schema([
                                Select::make('department_id')
                                    ->relationship('department', 'name')
                                    ->hiddenLabel()
                                    ->searchable()
                                    ->preload(),
                            ])->columnSpanFull(),
                        Section::make(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.salary.title'))
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('salary_expected')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.salary.fields.expected-salary'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999)
                                            ->step(0.01),
                                        TextInput::make('salary_expected_extra')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.salary.fields.salary-proposed-extra'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999)
                                            ->step(0.01),
                                    ])->columns(2),
                                Group::make()
                                    ->schema([
                                        TextInput::make('salary_proposed')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.salary.fields.proposed-salary'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999)
                                            ->step(0.01),
                                        TextInput::make('salary_proposed_extra')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.salary.fields.salary-expected-extra'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999)
                                            ->step(0.01),
                                    ])->columns(2),
                            ])->columnSpanFull(),
                        Section::make(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.source-and-medium.title'))
                            ->schema([
                                Select::make('source_id')
                                    ->relationship('source', 'name')
                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.source-and-medium.fields.source')),
                                Select::make('medium_id')
                                    ->relationship('medium', 'name')
                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.source-and-medium.fields.medium')),
                            ])->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
