<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class EmployeeCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.form.fields.name'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('Enter the name of the tag'),
                ColorPicker::make('color')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.form.fields.color'))
                    ->hexColor(),
                Hidden::make('creator_id')
                    ->default(Auth::user()->id),
            ]);
    }
}
