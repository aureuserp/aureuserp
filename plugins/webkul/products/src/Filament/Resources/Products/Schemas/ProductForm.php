<?php

namespace Webkul\Product\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Category;
use Webkul\Support\Models\UOM;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('products::filament/resources/product.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('products::filament/resources/product.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),

                                RichEditor::make('description')
                                    ->label(__('products::filament/resources/product.form.sections.general.fields.description')),
                                Select::make('tags')
                                    ->label(__('products::filament/resources/product.form.sections.general.fields.tags'))
                                    ->relationship(name: 'tags', titleAttribute: 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label(__('products::filament/resources/product.form.sections.general.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->unique('products_tags'),
                                    ]),
                            ]),

                        Section::make(__('products::filament/resources/product.form.sections.images.title'))
                            ->schema([
                                FileUpload::make('images')
                                    ->multiple()
                                    ->storeFileNamesIn('products'),
                            ]),

                        Section::make(__('products::filament/resources/product.form.sections.inventory.title'))
                            ->schema([
                                Fieldset::make(__('products::filament/resources/product.form.sections.inventory.fieldsets.logistics.title'))
                                    ->schema([
                                        TextInput::make('weight')
                                            ->label(__('products::filament/resources/product.form.sections.inventory.fieldsets.logistics.fields.weight'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999),
                                        TextInput::make('volume')
                                            ->label(__('products::filament/resources/product.form.sections.inventory.fieldsets.logistics.fields.volume'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999),
                                    ]),
                            ])
                            ->visible(fn (Get $get): bool => $get('type') == ProductType::GOODS->value),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/product.form.sections.settings.title'))
                            ->schema([
                                Radio::make('type')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.type'))
                                    ->options(ProductType::class)
                                    ->default(ProductType::GOODS->value)
                                    ->live(),
                                TextInput::make('reference')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.reference'))
                                    ->maxLength(255),
                                TextInput::make('barcode')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.barcode'))
                                    ->maxLength(255),
                                Select::make('category_id')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.category'))
                                    ->required()
                                    ->relationship('category', 'full_name')
                                    ->searchable()
                                    ->preload()
                                    ->default(Category::first()?->id)
                                    ->createOptionForm(fn (Schema $schema): Schema => CategoryResource::form($schema)),
                                Select::make('company_id')
                                    ->label(__('products::filament/resources/product.form.sections.settings.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::user()->default_company_id),
                            ]),

                        Section::make(__('products::filament/resources/product.form.sections.pricing.title'))
                            ->schema([
                                TextInput::make('price')
                                    ->label(__('products::filament/resources/product.form.sections.pricing.fields.price'))
                                    ->numeric()
                                    ->required()
                                    ->default(0.00)
                                    ->minValue(0),
                                TextInput::make('cost')
                                    ->label(__('products::filament/resources/product.form.sections.pricing.fields.cost'))
                                    ->numeric()
                                    ->default(0.00)
                                    ->minValue(0),
                                Hidden::make('uom_id')
                                    ->default(UOM::first()->id),
                                Hidden::make('uom_po_id')
                                    ->default(UOM::first()->id),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
