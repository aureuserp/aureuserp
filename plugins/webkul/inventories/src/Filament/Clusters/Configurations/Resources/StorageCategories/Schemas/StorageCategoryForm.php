<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\AllowNewProduct;

class StorageCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('inventories::filament/clusters/configurations/resources/storage-category.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('inventories::filament/clusters/configurations/resources/storage-category.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('max_weight')
                            ->label(__('inventories::filament/clusters/configurations/resources/storage-category.form.sections.general.fields.max-weight'))
                            ->numeric()
                            ->default(0.0000)
                            ->minValue(0)
                            ->maxValue(99999999),
                        Select::make('allow_new_products')
                            ->label(__('inventories::filament/clusters/configurations/resources/storage-category.form.sections.general.fields.allow-new-products'))
                            ->options(AllowNewProduct::class)
                            ->required()
                            ->default(AllowNewProduct::MIXED),
                        Select::make('company_id')
                            ->label(__('inventories::filament/clusters/configurations/resources/storage-category.form.sections.general.fields.company'))
                            ->relationship(name: 'company', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->default(Auth::user()->default_company_id),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
