<?php

namespace Webkul\Account\Filament\Resources\AccountTags\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\Applicability;

class AccountTagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        ColorPicker::make('color')
                            ->label(__('accounts::filament/resources/account-tag.form.fields.color'))
                            ->hexColor(),
                        Select::make('country_id')
                            ->searchable()
                            ->preload()
                            ->label(__('accounts::filament/resources/account-tag.form.fields.country'))
                            ->relationship('country', 'name'),
                        Select::make('applicability')
                            ->options(Applicability::options())
                            ->default(Applicability::ACCOUNT->value)
                            ->label(__('accounts::filament/resources/account-tag.form.fields.applicability'))
                            ->required(),
                        TextInput::make('name')
                            ->required()
                            ->label(__('accounts::filament/resources/account-tag.form.fields.name'))
                            ->maxLength(255),
                        Group::make()
                            ->schema([
                                Toggle::make('tax_negate')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account-tag.form.fields.tax-negate'))
                                    ->required(),
                            ]),
                    ])->columns(2),
            ]);
    }
}
