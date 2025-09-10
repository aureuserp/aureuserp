<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Schemas;

use Filament\Actions\Action;
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
use Webkul\Security\Filament\Resources\Companies\CompanyResource;

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
                                Section::make(__('employees::filament/clusters/configurations/resources/job-position.form.sections.employment-information.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.job-position-title'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('employees::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.job-position-title-tooltip')),
                                        Select::make('department_id')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department'))
                                            ->relationship(name: 'department', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                                $department = Department::find($state);

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
                                                    ->modalHeading(__('employees::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.department-modal-title'));
                                            }),
                                        Select::make('company_id')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.company'))
                                            ->relationship(name: 'company', titleAttribute: 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->createOptionForm(fn (Schema $schema) => CompanyResource::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                return $action
                                                    ->modalIcon('heroicon-o-building-office')
                                                    ->modalHeading(__('employees::filament/clusters/configurations/resources/job-position.form.sections.employment-information.fields.company-modal-title'));
                                            }),
                                    ])->columns(2),
                                Section::make()
                                    ->hiddenLabel()
                                    ->schema([
                                        RichEditor::make('description')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.form.sections.job-description.fields.job-description'))
                                            ->columnSpanFull(),
                                        RichEditor::make('requirements')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.form.sections.job-description.fields.job-requirements'))
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('no_of_recruitment')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.recruitment-target'))
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
                                        Select::make('employment_type_id')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.employment-type'))
                                            ->relationship('employmentType', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Toggle::make('is_active')
                                            ->label(__('employees::filament/clusters/configurations/resources/job-position.form.sections.workforce-planning.fields.status')),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }
}
