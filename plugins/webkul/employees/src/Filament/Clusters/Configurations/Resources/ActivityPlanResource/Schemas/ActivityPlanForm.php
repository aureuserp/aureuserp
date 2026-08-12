<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Employee\Filament\Resources\DepartmentResource;
use Webkul\Employee\Models\Department;
use Webkul\Security\Filament\Resources\CompanyResource;

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
                            ->relationship(
                                name: 'department',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                            )
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Schema $schema) => DepartmentResource::form($schema))
                            ->editOptionForm(fn (Schema $schema) => DepartmentResource::form($schema)),
                        Select::make('company_id')
                            ->label(__('employees::filament/clusters/configurations/resources/activity-plan.form.sections.general.fields.company'))
                            ->relationship(name: 'company', titleAttribute: 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Schema $schema) => CompanyResource::form($schema))
                            ->getOptionLabelFromRecordUsing(
                                fn (Model $record): string => $record->name.($record->trashed() ? ' (Deleted)' : ''),
                            )
                            ->disableOptionWhen(
                                fn (string $label): bool => str_contains($label, ' (Deleted)'),
                            )
                            ->default(current_company_id())
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                                'department_id' => Department::class,
                            ], $state)),
                        Toggle::make('is_active')
                            ->label(__('employees::filament/clusters/configurations/resources/activity-plan.form.sections.general.fields.status'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }
}
