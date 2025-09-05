<?php

namespace Webkul\Employee\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Filament\Resources\Departments\DepartmentResource;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Support\Models\Company;

class DepartmentForm
{
    use HasCustomFields;

    public static function getModel()
    {
        return DepartmentResource::getModel();
    }

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
                                        Hidden::make('creator_id')
                                            ->default(Auth::id())
                                            ->required(),
                                        TextInput::make('name')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true),
                                        Select::make('parent_id')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.parent-department'))
                                            ->relationship('parent', 'complete_name')
                                            ->searchable()
                                            ->preload()
                                            ->live(onBlur: true),
                                        Select::make('manager_id')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.manager'))
                                            ->relationship('manager', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder(__('employees::filament/resources/department.form.sections.general.fields.manager-placeholder'))
                                            ->nullable(),
                                        Select::make('company_id')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.company'))
                                            ->relationship('company', 'name')
                                            ->options(fn () => Company::pluck('name', 'id'))
                                            ->searchable()
                                            ->placeholder(__('employees::filament/resources/department.form.sections.general.fields.company-placeholder'))
                                            ->nullable(),
                                        ColorPicker::make('color')
                                            ->label(__('employees::filament/resources/department.form.sections.general.fields.color'))
                                            ->hexColor(),
                                    ])
                                    ->columns(2),
                                Section::make(__('employees::filament/resources/department.form.sections.additional.title'))
                                    ->visible(! empty($customFormFields = static::getCustomFormFields()))
                                    ->description(__('employees::filament/resources/department.form.sections.additional.description'))
                                    ->schema($customFormFields),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}
