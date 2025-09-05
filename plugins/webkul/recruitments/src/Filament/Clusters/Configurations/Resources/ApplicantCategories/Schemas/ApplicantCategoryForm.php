<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ApplicantCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/applicant-category.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder(__('recruitments::filament/clusters/configurations/resources/applicant-category.form.fields.name-placeholder')),
                ColorPicker::make('color')
                    ->label(__('recruitments::filament/clusters/configurations/resources/applicant-category.form.fields.color'))
                    ->required()
                    ->hexColor(),
            ]);
    }
}
