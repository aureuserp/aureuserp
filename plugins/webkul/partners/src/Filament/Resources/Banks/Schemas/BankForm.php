<?php

namespace Webkul\Partner\Filament\Resources\Banks\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BankForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('partners::filament/resources/bank.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('partners::filament/resources/bank.form.sections.general.fields.name'))
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('code')
                            ->label(__('partners::filament/resources/bank.form.sections.general.fields.code'))
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('partners::filament/resources/bank.form.sections.general.fields.email'))
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label(__('partners::filament/resources/bank.form.sections.general.fields.phone'))
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make(__('partners::filament/resources/bank.form.sections.address.title'))
                    ->schema([
                        Select::make('country_id')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.country'))
                            ->relationship(name: 'country', titleAttribute: 'name')
                            ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                            ->searchable()
                            ->preload()
                            ->live(),
                        Select::make('state_id')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.state'))
                            ->relationship(
                                name: 'state',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                            )
                            ->searchable()
                            ->preload(),
                        TextInput::make('street1')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.street1'))
                            ->maxLength(255),
                        TextInput::make('street2')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.street2'))
                            ->maxLength(255),
                        TextInput::make('city')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.city'))
                            ->maxLength(255),
                        TextInput::make('zip')
                            ->label(__('partners::filament/resources/bank.form.sections.address.fields.zip'))
                            ->maxLength(255),
                        Hidden::make('creator_id')
                            ->default(Auth::user()->id),
                    ])
                    ->columns(2),
            ]);
    }
}
