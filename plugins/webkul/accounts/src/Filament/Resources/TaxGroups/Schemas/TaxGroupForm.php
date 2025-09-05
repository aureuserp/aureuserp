<?php

namespace Webkul\Account\Filament\Resources\TaxGroups\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaxGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->label(__('accounts::filament/resources/tax-group.form.sections.fields.company'))
                            ->preload(),
                        Select::make('country_id')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->label(__('accounts::filament/resources/tax-group.form.sections.fields.country'))
                            ->preload(),
                        TextInput::make('name')
                            ->required()
                            ->label(__('accounts::filament/resources/tax-group.form.sections.fields.name'))
                            ->maxLength(255),
                        TextInput::make('preceding_subtotal')
                            ->label(__('accounts::filament/resources/tax-group.form.sections.fields.preceding-subtotal'))
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }
}
