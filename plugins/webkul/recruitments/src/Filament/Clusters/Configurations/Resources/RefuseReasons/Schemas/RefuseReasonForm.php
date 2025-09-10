<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasons\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RefuseReasonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder(__('recruitments::filament/clusters/configurations/resources/refuse-reason.form.fields.name-placeholder')),
                Select::make('template')
                    ->label(__('recruitments::filament/clusters/configurations/resources/refuse-reason.form.fields.template.title'))
                    ->options([
                        'applicant-refuse'         => __('recruitments::filament/clusters/configurations/resources/refuse-reason.form.fields.template.applicant-refuse'),
                        'applicant-not-interested' => __('recruitments::filament/clusters/configurations/resources/refuse-reason.form.fields.template.applicant-not-interested'),
                    ])
                    ->required(),
            ]);
    }
}
