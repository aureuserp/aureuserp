<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPriceResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ManageVendors;
use Webkul\Purchase\Models\Product;

class VendorPriceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.title'))
                            ->schema([
                                Select::make('partner_id')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.vendor'))
                                    ->relationship('partner', 'name')
                                    ->searchable()
                                    ->required()
                                    ->preload(),
                                TextInput::make('product_name')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.vendor-product-name'))
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.vendor-product-name-tooltip')),
                                TextInput::make('product_code')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.vendor-product-code'))
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.vendor-product-code-tooltip')),
                                TextInput::make('delay')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.delay'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.general.fields.delay-tooltip'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999)
                                    ->default(1),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.title'))
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.product'))
                                    ->relationship(
                                        'product',
                                        'name',
                                        fn (Builder $query, Get $get) => $query
                                            ->where('is_configurable', null)
                                            ->where(owned_by_company($get('company_id')))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->hiddenOn(ManageVendors::class),
                                TextInput::make('min_qty')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.quantity'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.quantity-tooltip'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->default(0),
                                Group::make()
                                    ->schema([
                                        TextInput::make('price')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.unit-price'))
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.unit-price-tooltip'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999)
                                            ->default(0),
                                        Select::make('currency_id')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.currency'))
                                            ->relationship(
                                                name: 'currency',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->active(),
                                            )
                                            ->required()
                                            ->searchable()
                                            ->default(current_company()?->currency_id)
                                            ->preload(),
                                        DatePicker::make('starts_at')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.valid-from'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar'),
                                        DatePicker::make('ends_at')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.valid-to'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar'),
                                    ])
                                    ->columns(2),
                                TextInput::make('discount')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.discount'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->default(0),
                                Select::make('company_id')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.form.sections.prices.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->default(current_company_id())
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                                        'product_id' => Product::class,
                                    ], $state)),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
