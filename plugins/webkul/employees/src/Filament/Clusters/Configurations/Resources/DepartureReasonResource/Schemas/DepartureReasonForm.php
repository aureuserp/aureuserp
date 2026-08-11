<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DepartureReasonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/departure-reason.form.fields.name'))
                    ->required(),
            ])->columns(1);
    }
}
