<?php

namespace Webkul\Employee\Filament\Resources\DepartmentResource\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/resources/department.form.sections.general.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true),
                                        Select::make('parent_id')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.parent-department'))
                                            ->relationship(
                                                name: 'parent',
                                                titleAttribute: 'complete_name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                                    ->withTrashed()
                                                    ->where(owned_by_company($get('company_id'))),
                                            )
                                            ->getOptionLabelFromRecordUsing(
                                                fn (Model $record): string => $record->complete_name.($record->trashed() ? ' (Deleted)' : ''),
                                            )
                                            ->disableOptionWhen(
                                                fn (string $label): bool => str_contains($label, ' (Deleted)'),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->live(),
                                        Select::make('manager_id')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.manager'))
                                            ->relationship(
                                                'manager',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->placeholder(__('employees::filament/resources/department.form.sections.general.fields.manager-placeholder'))
                                            ->nullable(),
                                        Select::make('company_id')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.company'))
                                            ->relationship('company', 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                                            ->getOptionLabelFromRecordUsing(function (Model $record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->preload()
                                            ->searchable()
                                            ->placeholder(__('employees::filament/resources/department.form.sections.general.fields.company-placeholder'))
                                            ->default(current_company_id())
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                                                'parent_id'  => Department::class,
                                                'manager_id' => Employee::class,
                                            ], $state))
                                            ->nullable(),
                                        ColorPicker::make('color')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.color'))
                                            ->hexColor(),
                                    ])
                                    ->columns(2)->columnSpanFull(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}
