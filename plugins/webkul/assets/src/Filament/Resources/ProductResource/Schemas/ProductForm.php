<?php

namespace Webkul\Asset\Filament\Resources\ProductResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Asset\Enums\AssetProductType;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\Schemas\AssetCategoryForm;
use Webkul\Asset\Filament\Resources\ProductResource;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetProduct;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/product.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('assets::filament/resources/product.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpanFull(),

                                Select::make('category_id')
                                    ->label(__('assets::filament/resources/product.form.sections.general.fields.category'))
                                    ->relationship(
                                        'category',
                                        'full_name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                            ->where(owned_by_company($get('company_id')))
                                            ->orderBy('full_name'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm(AssetCategoryForm::generalComponents())
                                    ->createOptionAction(fn ($action, Get $get) => $action
                                        ->visible(fn (): bool => ProductResource::canCreate())
                                        ->authorize(fn (): bool => ProductResource::canCreate())
                                        ->mutateDataUsing(fn (array $data): array => [
                                            ...$data,
                                            'company_id' => $get('company_id') ?: current_company_id(),
                                        ]))
                                    ->editOptionForm(AssetCategoryForm::generalComponents())
                                    ->editOptionAction(fn ($action, Select $component) => $action
                                        ->visible(fn (): bool => filled($component->getState()) && (bool) auth()->user()?->can('update_asset_product'))
                                        ->authorize(fn (): bool => filled($component->getState()) && (bool) auth()->user()?->can('update_asset_product'))),

                                Select::make('type')
                                    ->label(__('assets::filament/resources/product.form.sections.general.fields.type'))
                                    ->options(AssetProductType::class)
                                    ->default(AssetProductType::Asset->value)
                                    ->required()
                                    ->live()
                                    ->helperText(fn (Get $get): string => ($get('type') === AssetProductType::Consumable || $get('type') === AssetProductType::Consumable->value)
                                        ? __('assets::filament/resources/product.form.sections.general.fields.type-consumable-hint')
                                        : __('assets::filament/resources/product.form.sections.general.fields.type-asset-hint'))
                                    ->disabled(fn (?Model $record): bool => static::isReferenced($record))
                                    ->dehydrated(),

                                TextInput::make('model')
                                    ->label(__('assets::filament/resources/product.form.sections.general.fields.model'))
                                    ->maxLength(255),

                                TextInput::make('cost')
                                    ->label(__('assets::filament/resources/product.form.sections.general.fields.cost'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0),

                                Toggle::make('is_active')
                                    ->label(__('assets::filament/resources/product.form.sections.general.fields.is-active'))
                                    ->default(true),

                                Textarea::make('note')
                                    ->label(__('assets::filament/resources/product.form.sections.general.fields.note'))
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/product.form.sections.settings.title'))
                            ->schema([
                                Select::make('company_id')
                                    ->label(__('assets::filament/resources/product.form.sections.settings.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(fn (): ?int => current_company_id())
                                    ->disabled(fn (?Model $record): bool => static::isReferenced($record))
                                    ->dehydrated()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get, mixed $state) => clear_foreign_company_values($set, $get, [
                                        'category_id' => AssetCategory::class,
                                    ], $state)),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    protected static function isReferenced(?Model $record): bool
    {
        if (! $record instanceof AssetProduct || ! $record->exists) {
            return false;
        }

        return Asset::withoutGlobalScopes()->withTrashed()->where('product_id', $record->id)->exists();
    }
}
