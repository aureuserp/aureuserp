<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\PartnerLevelResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PartnerLevelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/partner-level.form.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('partner_weight')
                    ->label(__('crm::filament/clusters/configurations/resources/partner-level.form.weight'))
                    ->helperText(__('crm::filament/clusters/configurations/resources/partner-level.form.weight-helper'))
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(1)
                    ->required(),
            ])
            ->columns(1);
    }
}
