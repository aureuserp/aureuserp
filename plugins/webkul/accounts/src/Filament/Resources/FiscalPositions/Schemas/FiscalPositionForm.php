<?php

namespace Webkul\Account\Filament\Resources\FiscalPositions\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FiscalPositionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.name'))
                                    ->required()
                                    ->placeholder(__('Name')),
                                TextInput::make('foreign_vat')
                                    ->label(__('Foreign VAT'))
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.foreign-vat'))
                                    ->required(),
                                Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.country')),
                                Select::make('country_group_id')
                                    ->relationship('countryGroup', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.country-group')),
                                TextInput::make('zip_from')
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.zip-from'))
                                    ->required(),
                                TextInput::make('zip_to')
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.zip-to'))
                                    ->required(),
                                Toggle::make('auto_reply')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/fiscal-position.form.fields.detect-automatically')),
                            ])->columns(2),
                        RichEditor::make('notes')
                            ->label(__('accounts::filament/resources/fiscal-position.form.fields.notes')),
                    ]),
            ]);
    }
}
