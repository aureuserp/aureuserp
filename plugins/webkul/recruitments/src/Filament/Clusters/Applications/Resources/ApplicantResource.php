<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Recruitment\Enums\ApplicationStatus;
use Webkul\Recruitment\Enums\RecruitmentState as RecruitmentStateEnum;
use Webkul\Recruitment\Filament\Clusters\Applications;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\Pages;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\RelationManagers;
use Webkul\Recruitment\Models\Applicant;
use Webkul\Recruitment\Models\JobPosition;
use Webkul\Recruitment\Models\Stage as RecruitmentStage;
use Webkul\Security\Filament\Resources\UserResource;

class ApplicantResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = Applications::class;

    protected static ?int $navigationSort = 2;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        $currentRoute = Route::currentRouteName();

        if ($currentRoute === 'livewire.update') {
            $previousUrl = url()->previous();

            return str_contains($previousUrl, '/index') || str_contains($previousUrl, '?tableGrouping') || str_contains($previousUrl, '?tableFilters')
                ? SubNavigationPosition::Start
                : SubNavigationPosition::Top;
        }

        return str_contains($currentRoute, '.index')
            ? SubNavigationPosition::Start
            : SubNavigationPosition::Top;
    }

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/applications/resources/applicant.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/applications/resources/applicant.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/applications/resources/applicant.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'candidate.name',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
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
                    ])->columns(2),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.title'))
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('good')
                                                ->hiddenLabel()
                                                ->outlined(false)
                                                ->icon(fn ($record) => $record?->priority >= 1 ? 'heroicon-s-star' : 'heroicon-o-star')
                                                ->color('warning')
                                                ->size(ActionSize::ExtraLarge)
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
                                            Forms\Components\Actions\Action::make('veryGood')
                                                ->hiddenLabel()
                                                ->icon(fn ($record) => $record?->priority >= 2 ? 'heroicon-s-star' : 'heroicon-o-star')
                                                ->color('warning')
                                                ->size(ActionSize::ExtraLarge)
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
                                            Forms\Components\Actions\Action::make('excellent')
                                                ->hiddenLabel()
                                                ->icon(fn ($record) => $record?->priority >= 3 ? 'heroicon-s-star' : 'heroicon-o-star')
                                                ->color('warning')
                                                ->size(ActionSize::ExtraLarge)
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
                                        Forms\Components\Placeholder::make('application_status')
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
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Select::make('candidate_id')
                                            ->relationship('candidate', 'name')
                                            ->required()
                                            ->preload()
                                            ->searchable()
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.candidate-name'))
                                            ->live()
                                            ->afterStateHydrated(function (Set $set, Get $get, $state) {
                                                if ($state) {
                                                    $candidate = \Webkul\Recruitment\Models\Candidate::find($state);

                                                    $set('candidate.email_from', $candidate?->email_from);
                                                    $set('candidate.phone', $candidate?->phone);
                                                    $set('candidate.linkedin_profile', $candidate?->linkedin_profile);
                                                }
                                            })
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('candidate.email_from')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.email'))
                                            ->email()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('candidate.phone')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.phone'))
                                            ->tel()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('candidate.linkedin_profile')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.linkedin-profile'))
                                            ->url()
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('job_id')
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
                                        Forms\Components\DatePicker::make('date_closed')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.hired-date'))
                                            ->hidden(fn ($record) => ! $record->date_closed)
                                            ->visible()
                                            ->disabled()
                                            ->live()
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('recruiter')
                                            ->relationship('recruiter', 'name')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.recruiter'))
                                            ->preload()
                                            ->live()
                                            ->reactive()
                                            ->searchable(),
                                        Forms\Components\Select::make('recruitments_applicant_interviewers')
                                            ->relationship('interviewer', 'name')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.interviewer'))
                                            ->preload()
                                            ->multiple()
                                            ->searchable()
                                            ->dehydrated(true)
                                            ->saveRelationshipsUsing(function () {})
                                            ->createOptionForm(fn (Form $form) => UserResource::form($form)),
                                        Forms\Components\Select::make('recruitments_applicant_applicant_categories')
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
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\RichEditor::make('applicant_notes')
                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.general-information.fields.notes'))
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.education-and-availability.title'))
                            ->relationship('candidate', 'name')
                            ->schema([
                                Forms\Components\Select::make('degree_id')
                                    ->relationship('degree', 'name')
                                    ->searchable()
                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.education-and-availability.fields.degree'))
                                    ->preload(),
                                Forms\Components\DatePicker::make('availability_date')
                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.education-and-availability.fields.availability-date'))
                                    ->native(false),
                            ]),
                        Forms\Components\Section::make(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.department.title'))
                            ->schema([
                                Forms\Components\Select::make('department_id')
                                    ->relationship('department', 'name')
                                    ->hiddenLabel()
                                    ->searchable()
                                    ->preload(),
                            ]),
                        Forms\Components\Section::make(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.salary.title'))
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('salary_expected')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.salary.fields.expected-salary'))
                                            ->numeric()
                                            ->step(0.01),
                                        Forms\Components\TextInput::make('salary_expected_extra')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.salary.fields.salary-proposed-extra'))
                                            ->numeric()
                                            ->step(0.01),
                                    ])->columns(2),
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('salary_proposed')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.salary.fields.proposed-salary'))
                                            ->numeric()
                                            ->step(0.01),
                                        Forms\Components\TextInput::make('salary_proposed_extra')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.salary.fields.salary-expected-extra'))
                                            ->numeric()
                                            ->step(0.01),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Section::make(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.source-and-medium.title'))
                            ->schema([
                                Forms\Components\Select::make('source_id')
                                    ->relationship('source', 'name')
                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.source-and-medium.fields.source')),
                                Forms\Components\Select::make('medium_id')
                                    ->relationship('medium', 'name')
                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.form.sections.source-and-medium.fields.medium')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('candidate.partner.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.partner-name'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('create_date')
                    ->date()
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.applied-on'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('job.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.job-position'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stage.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.stage'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('candidate.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.candidate-name'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('application_status')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.application-status'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->state(function (Applicant $record) {
                        return [
                            'label' => $record->application_status->getLabel(),
                            'color' => $record->application_status->getColor(),
                        ];
                    })
                    ->tooltip(fn ($record) => $record->refuseReason?->name)
                    ->formatStateUsing(function ($record) {
                        $html = '<span style="display: inline-flex; align-items: center; background-color: '.$record->application_status->getColor().'; color: white; padding: 4px 8px; border-radius: 12px; font-size: 18px; font-weight: 500;">';

                        $html .= view('filament::components.icon', [
                            'icon'  => $record->application_status->getIcon(),
                            'class' => 'w-6 h-6',
                        ])->render();

                        $html .= $record->application_status->getLabel();
                        $html .= '</span>';

                        return new HtmlString($html);
                    })
                    ->placeholder('-'),
                TextColumn::make('refuseReason.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.refuse-reason'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.evaluation'))
                    ->toggleable(isToggledHiddenByDefault: true)
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
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.tags'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->weight(FontWeight::Bold)
                    ->state(function (Applicant $record): array {
                        $tags = $record->categories ?? $record->candidate->categories;

                        return $tags->map(fn ($category) => [
                            'label' => $category->name,
                            'color' => $category->color ?? '#808080',
                        ])->toArray();
                    })
                    ->formatStateUsing(fn ($state) => $state['label'])
                    ->color(fn ($state) => Color::hex($state['color'])),
                Tables\Columns\TextColumn::make('candidate.email_from')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.email'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('recruiter.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.recruiter'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('interviewer.name')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.interviewer'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('candidate.phone')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.candidate-phone'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('medium.name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.medium'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('source.name')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.source'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('salary_expected')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.salary-expected'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('candidate.availability_date')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.columns.availability-date'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('stage.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.stage'))
                    ->collapsible(),
                Tables\Grouping\Group::make('job.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.job-position'))
                    ->collapsible(),
                Tables\Grouping\Group::make('candidate.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.candidate-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('recruiter.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.responsible'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.creation-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_closed')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.hired-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('lastStage.name')
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.last-stage'))
                    ->collapsible(),
                Tables\Grouping\Group::make('refuseReason.name')
                    ->label(__('Refuse Reason'))
                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.groups.refuse-reason'))
                    ->collapsible(),
            ])
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(5)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('source')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.source'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('medium')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.medium'))
                            ->icon('heroicon-o-link')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('candidate')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.candidate'))
                            ->icon('heroicon-o-user-circle')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('date_last_stage_updated')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.date-last-stage-updated'))
                            ->icon('heroicon-o-user-circle')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('stage')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.stage'))
                            ->icon('heroicon-o-user-circle')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('job')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.job-position'))
                            ->icon('heroicon-o-briefcase')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('priority')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.priority'))
                            ->icon('heroicon-o-exclamation-circle'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('salary_proposed_extra')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.salary-proposed-extra'))
                            ->icon('heroicon-o-currency-dollar'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('salary_expected_extra')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.salary-expected-extra'))
                            ->icon('heroicon-o-currency-dollar'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('applicant_notes')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.applicant-notes'))
                            ->icon('heroicon-o-document-text'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('create_date')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.create-date'))
                            ->icon('heroicon-o-calendar'),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('date_closed')
                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.table.filters.date-closed'))
                            ->icon('heroicon-o-check-badge'),
                    ]),
            ])
            ->defaultGroup('stage.name')
            ->columnToggleFormColumns(3)
            ->filtersFormColumns(2)
            ->filtersLayout(FiltersLayout::Dropdown)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/applications/resources/applicant.table.actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/applications/resources/applicant.table.actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/applications/resources/applicant.table.bulk-actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/applications/resources/applicant.table.bulk-actions.delete.notification.body'))
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/applications/resources/applicant.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/applications/resources/applicant.table.bulk-actions.force-delete.notification.body'))
                        ),
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/applications/resources/applicant.table.bulk-actions.restore.notification.title'))
                                ->body(__('recruitments::filament/clusters/applications/resources/applicant.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('state', '!=', RecruitmentStateEnum::BLOCKED->value)
                    ->orWhereNull('state');
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(['default' => 3])
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.title'))
                                    ->schema([
                                        Infolists\Components\Group::make()
                                            ->schema([
                                                Infolists\Components\TextEntry::make('priority')
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
                                                Infolists\Components\TextEntry::make('stage.name')
                                                    ->hiddenLabel()
                                                    ->badge(),
                                                Infolists\Components\TextEntry::make('application_status')
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
                                        Infolists\Components\TextEntry::make('candidate.name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.candidate-name')),
                                        Infolists\Components\TextEntry::make('candidate.email_from')
                                            ->icon('heroicon-o-envelope')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.email')),
                                        Infolists\Components\TextEntry::make('candidate.phone')
                                            ->icon('heroicon-o-phone')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.phone')),
                                        Infolists\Components\TextEntry::make('candidate.linkedin_profile')
                                            ->icon('heroicon-o-link')
                                            ->placeholder('—')
                                            ->url(fn ($record) => $record->candidate->linkedin_profile)
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.linkedin-profile')),
                                        Infolists\Components\TextEntry::make('job.name')
                                            ->icon('heroicon-o-briefcase')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.job-position')),
                                        Infolists\Components\TextEntry::make('recruiter.name')
                                            ->icon('heroicon-o-user-circle')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.recruiter')),
                                        Infolists\Components\TextEntry::make('recruiter.name')
                                            ->icon('heroicon-o-user-circle')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.recruiter')),
                                        Infolists\Components\TextEntry::make('categories.name')
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
                                            ->color(fn ($state) => Color::hex($state['color']))
                                            ->listWithLineBreaks()
                                            ->label('Tags'),
                                        Infolists\Components\TextEntry::make('interviewer.name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('—')
                                            ->badge()
                                            ->label('Interviewers'),
                                    ])
                                    ->columns(2),
                                Infolists\Components\Section::make()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('applicant_notes')
                                            ->formatStateUsing(fn ($state) => new HtmlString($state))
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.general-information.entries.notes')),
                                    ]),
                            ])->columnSpan(2),
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.education-and-availability.title'))
                                    ->relationship('candidate', 'name')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('degree.name')
                                            ->icon('heroicon-o-academic-cap')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.education-and-availability.entries.degree')),
                                        Infolists\Components\TextEntry::make('availability_date')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.education-and-availability.entries.availability-date')),
                                    ]),
                                Infolists\Components\Section::make(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.salary.title'))
                                    ->schema([
                                        Infolists\Components\Group::make()
                                            ->schema([
                                                Infolists\Components\TextEntry::make('salary_expected')
                                                    ->icon('heroicon-o-currency-dollar')
                                                    ->placeholder('—')
                                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.salary.entries.expected-salary')),
                                                Infolists\Components\TextEntry::make('salary_expected_extra')
                                                    ->icon('heroicon-o-currency-dollar')
                                                    ->placeholder('—')
                                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.salary.entries.salary-expected-extra')),
                                            ])->columns(2),
                                        Infolists\Components\Group::make()
                                            ->schema([
                                                Infolists\Components\TextEntry::make('salary_proposed')
                                                    ->icon('heroicon-o-currency-dollar')
                                                    ->placeholder('—')
                                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.salary.entries.proposed-salary')),
                                                Infolists\Components\TextEntry::make('salary_proposed_extra')
                                                    ->icon('heroicon-o-currency-dollar')
                                                    ->placeholder('—')
                                                    ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.salary.entries.salary-proposed-extra')),
                                            ])->columns(2),
                                    ]),
                                Infolists\Components\Section::make(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.source-and-medium.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('source.name')
                                            ->icon('heroicon-o-globe-alt')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.source-and-medium.entries.source')),
                                        Infolists\Components\TextEntry::make('medium.name')
                                            ->icon('heroicon-o-globe-alt')
                                            ->placeholder('—')
                                            ->label(__('recruitments::filament/clusters/applications/resources/applicant.infolist.sections.source-and-medium.entries.medium')),
                                    ]),
                            ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewApplicant::class,
            Pages\EditApplicant::class,
            Pages\ManageSkill::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Manage Skills', [
                RelationManagers\SkillsRelationManager::class,
            ])
                ->icon('heroicon-o-bolt'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListApplicants::route('/'),
            'view'   => Pages\ViewApplicant::route('/{record}'),
            'edit'   => Pages\EditApplicant::route('/{record}/edit'),
            'skills' => Pages\ManageSkill::route('/{record}/skills'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
