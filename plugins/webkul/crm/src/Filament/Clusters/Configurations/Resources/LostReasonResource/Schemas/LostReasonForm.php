<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LostReasonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/lost-reason.form.fields.name'))
                    ->required()
                    ->maxLength(255),
            ])
            ->columns(1);
    }
}
