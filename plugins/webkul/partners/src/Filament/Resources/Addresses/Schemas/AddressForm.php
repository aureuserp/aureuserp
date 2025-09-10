<?php

namespace Webkul\Partner\Filament\Resources\Addresses\Schemas;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Partner\Enums\AddressType;
use Webkul\Partner\Filament\Resources\Partners\Pages\ManageAddresses;
use Webkul\Partner\Filament\Resources\Partners\PartnerResource;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Radio::make('sub_type')
                ->hiddenLabel()
                ->options(AddressType::class)
                ->default(AddressType::INVOICE->value)
                ->inline()
                ->columnSpan(2),
            Select::make('parent_id')
                ->label(__('partners::filament/resources/address.form.partner'))
                ->relationship('parent', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->columnSpan(2)
                ->hiddenOn([ManageAddresses::class])
                ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema)),
            TextInput::make('name')
                ->label(__('partners::filament/resources/address.form.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label(__('partners::filament/resources/address.form.email'))
                ->email()
                ->maxLength(255),
            TextInput::make('phone')
                ->label(__('partners::filament/resources/address.form.phone'))
                ->tel()
                ->maxLength(255),
            TextInput::make('mobile')
                ->label(__('partners::filament/resources/address.form.mobile'))
                ->tel(),
            TextInput::make('street1')
                ->label(__('partners::filament/resources/address.form.street1'))
                ->maxLength(255),
            TextInput::make('street2')
                ->label(__('partners::filament/resources/address.form.street2'))
                ->maxLength(255),
            TextInput::make('city')
                ->label(__('partners::filament/resources/address.form.city'))
                ->maxLength(255),
            TextInput::make('zip')
                ->label(__('partners::filament/resources/address.form.zip'))
                ->maxLength(255),
            Select::make('country_id')
                ->label(__('partners::filament/resources/address.form.country'))
                ->relationship(name: 'country', titleAttribute: 'name')
                ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(function (Set $set, Get $get) {
                    $set('state_id', null);
                }),
            Select::make('state_id')
                ->label(__('partners::filament/resources/address.form.state'))
                ->relationship(
                    name: 'state',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                )
                ->createOptionForm(function (Schema $schema, Get $get, Set $set) {
                    return $schema
                        ->components([
                            TextInput::make('name')
                                ->label(__('partners::filament/resources/address.form.name'))
                                ->required(),
                            TextInput::make('code')
                                ->label(__('partners::filament/resources/address.form.code'))
                                ->required()
                                ->unique('states'),
                            Select::make('country_id')
                                ->label(__('partners::filament/resources/address.form.country'))
                                ->relationship('country', 'name')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->default($get('country_id'))
                                ->afterStateUpdated(function (Get $get) use ($set) {
                                    $set('country_id', $get('country_id'));
                                }),
                        ]);
                })
                ->searchable()
                ->preload(),
        ])
            ->columns(2);
    }
}
