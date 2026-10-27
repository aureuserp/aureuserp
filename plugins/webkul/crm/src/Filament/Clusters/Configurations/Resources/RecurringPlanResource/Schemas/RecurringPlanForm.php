<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RecurringPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/recurring-plan.form.name'))
                    ->placeholder(__('crm::filament/clusters/configurations/resources/recurring-plan.form.name-placeholder'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('number_of_months')
                    ->label(__('crm::filament/clusters/configurations/resources/recurring-plan.form.months'))
                    ->helperText(__('crm::filament/clusters/configurations/resources/recurring-plan.form.months-helper'))
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
            ])
            ->columns(1);
    }
}
