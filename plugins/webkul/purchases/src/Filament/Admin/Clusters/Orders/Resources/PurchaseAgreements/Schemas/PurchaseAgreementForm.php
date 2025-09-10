<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Enums\RequisitionType;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\PurchaseAgreementResource;
use Webkul\Purchase\Settings\ProductSettings;

class PurchaseAgreementForm
{
    use HasCustomFields;

    public static function getModel()
    {
        return PurchaseAgreementResource::getModel();
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(RequisitionState::options())
                    ->default(RequisitionState::DRAFT)
                    ->disabled(),
                Section::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.title'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Select::make('partner_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.vendor'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                        fn ($query) => $query->where('sub_type', 'supplier')
                                    )
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->disabled(fn ($record): bool => $record && $record?->state != RequisitionState::DRAFT),
                                Select::make('user_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.buyer'))
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn ($record): bool => $record && $record?->state != RequisitionState::DRAFT),
                                Select::make('type')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.agreement-type'))
                                    ->options(RequisitionType::class)
                                    ->required()
                                    ->default(RequisitionType::BLANKET_ORDER)
                                    ->disabled(fn ($record): bool => $record && $record?->state != RequisitionState::DRAFT)
                                    ->live(),
                                Select::make('currency_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.currency'))
                                    ->relationship('currency', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        DatePicker::make('starts_at')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.valid-from'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar'),
                                        DatePicker::make('ends_at')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.valid-to'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar'),
                                    ])
                                    ->columns(2)
                                    ->hidden(function (Get $get): bool {
                                        return $get('type') != RequisitionType::BLANKET_ORDER;
                                    }),
                                TextInput::make('reference')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.reference'))
                                    ->maxLength(255)
                                    ->placeholder(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.reference-placeholder')),
                                Select::make('company_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->default(auth()->user()->default_company_id)
                                    ->disabled(fn ($record): bool => $record && $record?->state != RequisitionState::DRAFT),
                            ]),
                    ])
                    ->columns(2),

                Tabs::make()
                    ->schema([
                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.title'))
                            ->schema([
                                static::getProductsRepeater(),
                            ]),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.additional.title'))
                            ->visible(! empty($customFormFields = static::getCustomFormFields()))
                            ->schema($customFormFields),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.terms.title'))
                            ->schema([
                                RichEditor::make('description')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function getProductsRepeater(): Repeater
    {
        $columns = 3;

        if (app(ProductSettings::class)->enable_uom) {
            $columns++;
        }

        return Repeater::make('lines')
            ->hiddenLabel()
            ->relationship()
            ->schema([
                Select::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.product'))
                    ->relationship('product', 'name')
                    ->relationship(
                        'product',
                        'name',
                        fn ($query) => $query->where('type', ProductType::GOODS),
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        if ($product = Product::find($get('product_id'))) {
                            $set('uom_id', $product->uom_id);
                        }
                    })
                    ->disabled(fn ($record): bool => in_array($record?->requisition->state, [RequisitionState::CLOSED, RequisitionState::CANCELED])),
                TextInput::make('qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.quantity'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required()
                    ->disabled(fn ($record): bool => in_array($record?->requisition->state, [RequisitionState::CLOSED, RequisitionState::CANCELED])),
                Select::make('uom_id')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        fn ($query) => $query->where('category_id', 1),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                    ->disabled(fn ($record): bool => in_array($record?->requisition->state, [RequisitionState::CLOSED, RequisitionState::CANCELED])),
                TextInput::make('price_unit')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.fields.unit-price'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required()
                    ->disabled(fn ($record): bool => in_array($record?->requisition->state, [RequisitionState::CLOSED, RequisitionState::CANCELED])),
            ])
            ->columns($columns);
    }
}
