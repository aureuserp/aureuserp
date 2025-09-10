<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Webkul\Employee\Filament\Resources\Departments\DepartmentResource;
use Webkul\Employee\Models\Department;
use Webkul\Partner\Filament\Resources\Industries\IndustryResource;
use Webkul\Security\Filament\Resources\Companies\CompanyResource;
use Webkul\Security\Filament\Resources\Users\UserResource;

class JobPositionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.job-position-title'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.job-position-title-tooltip')),
                                        Select::make('department_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department'))
                                            ->relationship(name: 'department', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->reactive()
                                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                                $department = Department::find($state);

                                                if (
                                                    ! $get('manager_id')
                                                    && $department?->manager_id
                                                ) {
                                                    $set('manager_id', $department->manager_id);
                                                }

                                                if (
                                                    ! $get('company_id')
                                                    && $department?->company_id
                                                ) {
                                                    $set('company_id', $department->company_id);
                                                }
                                            })
                                            ->createOptionForm(fn (Schema $schema) => DepartmentResource::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalHeading(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department-modal-title'));
                                            }),
                                        Select::make('manager_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.manager'))
                                            ->relationship(name: 'manager', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->reactive()
                                            ->live(),
                                        Select::make('company_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.company'))
                                            ->relationship(name: 'company', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->createOptionForm(fn (Schema $schema) => CompanyResource::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalIcon('heroicon-o-building-office')
                                                    ->modalHeading(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.company-modal-title'));
                                            }),
                                        Select::make('recruiter_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.recruiter'))
                                            ->relationship(name: 'recruiter', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->createOptionForm(fn (Schema $schema) => CompanyResource::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalIcon('heroicon-o-building-office')
                                                    ->modalHeading(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.company-modal-title'));
                                            }),
                                        Select::make('recruitments_job_position_interviewers')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.interviewers'))
                                            ->relationship(name: 'interviewers', titleAttribute: 'name')
                                            ->searchable()
                                            ->multiple()
                                            ->preload()
                                            ->live()
                                            ->createOptionForm(fn (Schema $schema) => UserResource::form($schema)),
                                        Select::make('address_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.job-location'))
                                            ->relationship(name: 'address', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema) => DepartmentResource::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalHeading(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department-modal-title'))
                                                    ->modalSubmitActionLabel(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department-modal-title'))
                                                    ->modalWidth('2xl');
                                            }),
                                        Select::make('industry_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.industry'))
                                            ->relationship('industry', 'name')
                                            ->searchable()
                                            ->createOptionForm(fn ($form) => IndustryResource::form($form)->columns(2))
                                            ->preload(),
                                    ])->columns(2),
                                Section::make()
                                    ->hiddenLabel()
                                    ->schema([
                                        RichEditor::make('description')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.job-description.fields.job-description'))
                                            ->columnSpanFull(),
                                        RichEditor::make('requirements')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.job-description.fields.job-requirements'))
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        DatePicker::make('date_from')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.date-from'))
                                            ->native(false),
                                        DatePicker::make('date_to')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.date-to'))
                                            ->native(false),
                                    ])->columns(2),
                                Section::make()
                                    ->schema([
                                        TextInput::make('no_of_recruitment')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.recruitment-target'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999)
                                            ->default(0),
                                        TextInput::make('no_of_employee')
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('expected_employees')
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('no_of_hired_employee')
                                            ->disabled(),
                                        Select::make('job_position_skills')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.expected-skills'))
                                            ->relationship('skills', 'name')
                                            ->searchable()
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->preload(),
                                        Select::make('employment_type_id')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.employment-type'))
                                            ->relationship('employmentType', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Toggle::make('is_active')
                                            ->label(__('recruitments::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.status'))
                                            ->inline(false),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }
}
