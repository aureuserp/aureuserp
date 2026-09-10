<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategoryResource\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

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
                    ->placeholder(__('employees::filament/clusters/configurations/resources/employee-category.form.fields.name-placeholder')),
                ColorPicker::make('color')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.form.fields.color'))
                    ->hexColor(),
            ]);
    }
}
