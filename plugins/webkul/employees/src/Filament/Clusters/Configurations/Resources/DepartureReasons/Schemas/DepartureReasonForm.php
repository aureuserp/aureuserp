<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasons\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class DepartureReasonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/departure-reason.form.fields.name'))
                    ->required(),
                Hidden::make('creator_id')
                    ->default(Auth::user()->id),
            ])->columns(1);
    }
}
