<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class OrderTemplateProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('orderTemplate.name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.order-template')),
                        Select::make('company.name')
                            ->searchable()
                            ->preload()
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.company')),
                        Select::make('product.name')
                            ->searchable()
                            ->preload()
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.product')),
                        Select::make('uom.name')
                            ->searchable()
                            ->preload()
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.product-uom')),
                        Hidden::make('creator_id')
                            ->default(Auth::user()->id),
                        TextInput::make('display_type')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.display-type'))
                            ->maxLength(255),
                        TextInput::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.name'))
                            ->maxLength(255),
                        TextInput::make('quantity')
                            ->label(__('sales::filament/clusters/configurations/resources/order-template.form.fields.quantity'))
                            ->required()
                            ->numeric(),
                    ])->columns(2),
            ]);
    }
}
