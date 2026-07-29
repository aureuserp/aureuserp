<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\CategoryResource;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry as Registry;
use Webkul\Product\Models\Category;
use Webkul\Support\Models\UOM;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        $leftGroup = array_merge(
            [static::generalSection()],
            Registry::renderForm('left.general.after'),
            [static::mediaSection()],
            Registry::hasFormSlot('left.inventory')
                ? Registry::renderForm('left.inventory')
                : [static::inventorySection()],
            Registry::renderForm('left.append'),
        );

        $rightGroup = array_merge(
            [static::settingsSection()],
            Registry::renderForm('right.settings.after'),
            [static::pricingSection()],
            Registry::renderForm('right.append'),
        );

        return $schema
            ->components(array_merge([
                Group::make()
                    ->schema($leftGroup)
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema($rightGroup)
                    ->columnSpan(['lg' => 1]),
            ], Registry::renderForm('hidden')))
            ->columns(3);
    }

    public static function generalSection(): Section
    {
        return Section::make()
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
            ]);
    }

    public static function mediaSection(): Section
    {
        return Section::make(__('products::filament/resources/product.form.sections.images.title'))
            ->schema([
                FileUpload::make('images')
                    ->image()
                    ->multiple()
                    ->storeFileNamesIn('products'),
            ]);
    }

    public static function inventorySection(): Section
    {
        return Section::make(__('products::filament/resources/product.form.sections.inventory.title'))
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
            ->visible(fn (Get $get): bool => $get('type') == ProductType::GOODS);
    }

    public static function settingsSection(): Section
    {
        return Section::make(__('products::filament/resources/product.form.sections.settings.title'))
            ->schema([
                Radio::make('type')
                    ->label(__('products::filament/resources/product.form.sections.settings.fields.type'))
                    ->options(ProductType::class)
                    ->default(ProductType::GOODS->value)
                    ->live()
                    ->afterStateUpdated(function (Set $set, ProductType|string|null $state): void {
                        $defaultUomId = ProductResource::getDefaultUomIdByProductType($state);

                        if (! $defaultUomId) {
                            return;
                        }

                        $set('uom_id', $defaultUomId);
                        $set('uom_po_id', $defaultUomId);
                    }),
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
                    ->relationship(
                        'company',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                    ->searchable()
                    ->preload()
                    ->default(Auth::user()->default_company_id),
            ]);
    }

    public static function pricingSection(): Section
    {
        return Section::make(__('products::filament/resources/product.form.sections.pricing.title'))
            ->schema(array_merge([
                FusedGroup::make([
                    TextInput::make('price')
                        ->numeric()
                        ->required()
                        ->default(0.00)
                        ->minValue(0)
                        ->columnSpan(2),
                    Select::make('uom_id')
                        ->placeholder(__('products::filament/resources/product.form.sections.pricing.fields.uom-placeholder'))
                        ->native(false)
                        ->required()
                        ->options(UOM::pluck('name', 'id'))
                        ->default(fn (Get $get): ?int => ProductResource::getDefaultUomIdByProductType($get('type')))
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('uom_po_id', $state)),
                ])
                    ->label(__('products::filament/resources/product.form.sections.pricing.fields.price'))
                    ->columns(3),
                FusedGroup::make([
                    TextInput::make('cost')
                        ->numeric()
                        ->default(0.00)
                        ->minValue(0)
                        ->columnSpan(2),
                    Select::make('uom_po_id')
                        ->placeholder(__('products::filament/resources/product.form.sections.pricing.fields.uom-placeholder'))
                        ->native(false)
                        ->required()
                        ->options(UOM::pluck('name', 'id'))
                        ->default(fn (Get $get): ?int => ProductResource::getDefaultUomIdByProductType($get('type')))
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('uom_id', $state)),
                ])
                    ->label(__('products::filament/resources/product.form.sections.pricing.fields.cost'))
                    ->columns(3),
            ], Registry::renderForm('right.pricing.fields')));
    }
}
