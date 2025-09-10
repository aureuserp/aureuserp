<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Employee\Filament\Resources\Departments\DepartmentResource;
use Webkul\Security\Filament\Resources\Companies\CompanyResource;

class ActivityPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('employees::filament/clusters/configurations/resources/activity-plan.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('employees::filament/clusters/configurations/resources/activity-plan.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Select::make('department_id')
                            ->label(__('employees::filament/clusters/configurations/resources/activity-plan.form.sections.general.fields.department'))
                            ->relationship(name: 'department', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Schema $schema) => DepartmentResource::form($schema))
                            ->editOptionForm(fn (Schema $schema) => DepartmentResource::form($schema)),
                        Select::make('company_id')
                            ->label(__('employees::filament/clusters/configurations/resources/activity-plan.form.sections.general.fields.company'))
                            ->relationship(name: 'company', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Schema $schema) => CompanyResource::form($schema))
                            ->editOptionForm(fn (Schema $schema) => CompanyResource::form($schema)),
                        Toggle::make('is_active')
                            ->label(__('employees::filament/clusters/configurations/resources/activity-plan.form.sections.general.fields.status'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
            ]);
    }
}
