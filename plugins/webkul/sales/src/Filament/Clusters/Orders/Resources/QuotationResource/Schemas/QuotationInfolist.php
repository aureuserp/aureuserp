<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Schemas;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Sale\Livewire\QuotationSummary;
use Webkul\Sale\Settings\PriceSettings;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class QuotationInfolist
{
    public static function configure(Schema $schema, array $customInfolistEntries = []): Schema
    {
        return $schema
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = OrderState::options();

                        if ($record->state !== OrderState::CANCEL) {
                            unset($options[OrderState::CANCEL->value]);
                        } else {
                            unset($options[OrderState::SALE->value]);
                        }

                        return $options;
                    })
                    ->default(OrderState::DRAFT->value),

                Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.sale-order'))
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->size(TextSize::Large),
                            ])->columns(2),
                        Grid::make()
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.customer'))
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('validity_date')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.expiration'))
                                    ->icon('heroicon-o-calendar')
                                    ->date(),
                                TextEntry::make('date_order')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.quotation-date'))
                                    ->icon('heroicon-o-calendar')
                                    ->date(),
                                TextEntry::make('paymentTerm.name')
                                    ->placeholder('-')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.entries.payment-term'))
                                    ->icon('heroicon-o-calendar-days'),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->live()
                                    ->columnManager()
                                    ->columnManagerColumns(2)
                                    ->table(fn ($record) => [
                                        InfolistTableColumn::make('name')
                                            ->width(250)
                                            ->toggleable()
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.product')),
                                        InfolistTableColumn::make('product_uom_qty')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.quantity'))
                                            ->width(100)
                                            ->toggleable(),
                                        InfolistTableColumn::make('qty_delivered')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.qty-delivered'))
                                            ->width(100)
                                            ->toggleable()
                                            ->visible(in_array($record?->state, [OrderState::SALE])),
                                        InfolistTableColumn::make('qty_invoiced')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.qty-invoiced'))
                                            ->width(100)
                                            ->toggleable()
                                            ->visible(in_array($record?->state, [OrderState::SALE])),
                                        InfolistTableColumn::make('uom')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.uom'))
                                            ->width(100)
                                            ->toggleable()
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom),
                                        InfolistTableColumn::make('customer_lead')
                                            ->width(100)
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.lead-time')),
                                        InfolistTableColumn::make('product_packaging_qty')
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->width(150)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.packaging-qty'))
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_packagings),
                                        InfolistTableColumn::make('productPackaging')
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->width(150)
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_packagings)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.packaging')),
                                        InfolistTableColumn::make('price_unit')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.unit-price'))
                                            ->toggleable()
                                            ->width(100),
                                        InfolistTableColumn::make('margin')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.margin'))
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->width(100)
                                            ->visible(fn (PriceSettings $settings) => $settings->enable_margin),
                                        InfolistTableColumn::make('margin_percent')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.margin-percentage'))
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->width(100)
                                            ->visible(fn (PriceSettings $settings) => $settings->enable_margin),
                                        InfolistTableColumn::make('taxes')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.taxes'))
                                            ->toggleable()
                                            ->width(150),
                                        InfolistTableColumn::make('discount')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.discount-percentage'))
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->width(100)
                                            ->visible(fn (PriceSettings $settings) => $settings->enable_discount),
                                        InfolistTableColumn::make('price_subtotal')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.amount'))
                                            ->toggleable()
                                            ->width(100),
                                    ])
                                    ->schema([
                                        TextEntry::make('name')
                                            ->placeholder('-')
                                            ->tooltip(fn ($record) => $record->product?->name)
                                            ->iconColor('primary'),

                                        TextEntry::make('product_uom_qty')
                                            ->placeholder('-')
                                            ->numeric(),

                                        TextEntry::make('qty_delivered')
                                            ->placeholder('-')
                                            ->numeric()
                                            ->visible(fn ($record): bool => in_array($record?->state, [OrderState::SALE])),

                                        TextEntry::make('qty_invoiced')
                                            ->placeholder('-')
                                            ->numeric()
                                            ->visible(fn ($record): bool => in_array($record?->state, [OrderState::SALE])),

                                        TextEntry::make('uom')
                                            ->formatStateUsing(function ($state, ProductSettings $settings) {
                                                return $settings->enable_uom && $state ? $state['name'] : '-';
                                            })
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom),

                                        TextEntry::make('customer_lead')
                                            ->placeholder('-')
                                            ->numeric()
                                            ->suffix(' days'),

                                        TextEntry::make('product_packaging_qty')
                                            ->formatStateUsing(function ($state, ProductSettings $settings) {
                                                return $settings->enable_packagings && $state ? $state : '-';
                                            })
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_packagings),

                                        TextEntry::make('productPackaging')
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_packagings),

                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency->code)
                                            ->weight(FontWeight::Medium),

                                        TextEntry::make('margin')
                                            ->formatStateUsing(function ($state, PriceSettings $settings) {
                                                return $settings->enable_margin && $state ? $state : '-';
                                            })
                                            ->visible(fn (PriceSettings $settings) => $settings->enable_margin)
                                            ->money(fn ($record) => $record->currency->code),

                                        TextEntry::make('margin_percent')
                                            ->formatStateUsing(function ($state, PriceSettings $settings) {
                                                return $settings->enable_margin && $state ? $state : '-';
                                            })
                                            ->visible(fn (PriceSettings $settings) => $settings->enable_margin),
                                        TextEntry::make('taxes')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => ['name' => $tax->name])->toArray();
                                            })

                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-'),

                                        TextEntry::make('discount')
                                            ->formatStateUsing(function ($state, PriceSettings $settings) {
                                                return $settings->enable_discount && $state ? $state : '-';
                                            })
                                            ->visible(fn (PriceSettings $settings) => $settings->enable_discount)
                                            ->numeric()
                                            ->suffix('%'),

                                        TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency->code),
                                    ])
                                    ->extraItemActions([
                                        Action::make('viewProduct')
                                            ->tooltip(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.actions.open-product.tooltip'))
                                            ->size(fn () => 'sm') // problematic if not evaluated
                                            ->iconButton()
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->url(function (array $arguments, Get $get): ?string {
                                                $productId = $get('lines')[$arguments['item']]['product_id'] ?? null;

                                                return $productId ? ProductResource::getUrl('view', ['record' => $productId]) : null;
                                            })
                                            ->openUrlInNewTab(true)
                                            ->visible(function (array $arguments, Get $get): bool {
                                                $productId = $get('lines')[$arguments['item']]['product_id'] ?? null;

                                                return filled($productId) ?? null;
                                            }),
                                    ]),

                                Livewire::make(QuotationSummary::class, function ($record, PriceSettings $settings) {
                                    $subtotal = 0;
                                    $totalTax = 0;
                                    $grandTotal = 0;
                                    $margin = 0;

                                    foreach ($record->lines as $line) {
                                        $subtotal += floatval($line->price_subtotal ?? 0);
                                        $totalTax += floatval($line->price_tax ?? 0);
                                        $grandTotal += floatval($line->price_total ?? 0);
                                        $margin += floatval($line->margin ?? 0);
                                    }

                                    $marginPercentage = ($subtotal > 0) ? ($margin / $subtotal) * 100 : 0;

                                    return [
                                        'currency'         => $record->currency,
                                        'enableMargin'     => $settings->enable_margin,
                                        'subtotal'         => round($subtotal, 2),
                                        'totalTax'         => round($totalTax, 2),
                                        'grandTotal'       => round($grandTotal, 2),
                                        'margin'           => round($margin, 2),
                                        'marginPercentage' => round($marginPercentage, 2),
                                    ];
                                })
                                    ->key('quotation-summary-view'),
                            ]),

                        Tab::make(__('Optional Products'))
                            ->icon('heroicon-o-arrow-path-rounded-square')
                            ->hidden(fn ($record) => $record->optionalLines->isEmpty()
                                || ! in_array($record->state, [OrderState::DRAFT, OrderState::SENT]))
                            ->schema([
                                RepeatableEntry::make('optionalLines')
                                    ->hiddenLabel()
                                    ->columnManager()
                                    ->table([
                                        InfolistTableColumn::make('product')
                                            ->toggleable()
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.product')),
                                        InfolistTableColumn::make('uom.name')
                                            ->toggleable()
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.uom'))
                                            ->alignment(Alignment::Start)
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom),
                                        InfolistTableColumn::make('quantity')
                                            ->toggleable()
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.quantity'))
                                            ->alignment(Alignment::Start),
                                        InfolistTableColumn::make('discount')
                                            ->toggleable()
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.discount-percentage'))
                                            ->alignment(Alignment::Start)
                                            ->visible(fn (PriceSettings $settings) => $settings->enable_discount),
                                        InfolistTableColumn::make('price_unit')
                                            ->toggleable()
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.unit-price'))
                                            ->alignment(Alignment::Start),
                                    ])
                                    ->schema([
                                        TextEntry::make('product')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.product'))
                                            ->alignment(Alignment::Start),
                                        TextEntry::make('uom.name')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.uom'))
                                            ->alignment(Alignment::Start),
                                        TextEntry::make('quantity')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.quantity'))
                                            ->alignment(Alignment::Start),
                                        TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.discount-percentage'))
                                            ->visible(fn (PriceSettings $settings) => $settings->enable_discount)
                                            ->suffix('%')
                                            ->alignment(Alignment::Start),
                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.unit-price'))
                                            ->alignment(Alignment::Start),
                                    ])->columns(4),
                            ]),

                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.title'))
                                    ->icon('heroicon-o-user-group')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('user.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.entries.sales-person'))
                                                    ->icon('heroicon-o-user'),
                                                TextEntry::make('client_order_ref')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.entries.customer-reference'))
                                                    ->icon('heroicon-o-hashtag'),
                                                TextEntry::make('tags.name')
                                                    ->badge()
                                                    ->state(function ($record): array {
                                                        return $record->tags->map(fn ($tag) => [
                                                            'name' => $tag->name,
                                                        ])->toArray();
                                                    })
                                                    ->formatStateUsing(fn ($state) => $state['name'])
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.sales.entries.tags'))
                                                    ->icon('heroicon-o-tag'),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.shipping.title'))
                                    ->icon('heroicon-o-truck')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('commitment_date')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.shipping.entries.commitment-date'))
                                                    ->icon('heroicon-o-calendar')
                                                    ->date(),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.title'))
                                    ->icon('heroicon-o-chart-bar')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('origin')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.entries.source-document'))
                                                    ->icon('heroicon-o-document'),
                                                TextEntry::make('campaign.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.entries.campaign'))
                                                    ->icon('heroicon-o-presentation-chart-line'),
                                                TextEntry::make('medium.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.entries.medium'))
                                                    ->icon('heroicon-o-device-phone-mobile'),
                                                TextEntry::make('utmSource.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.tracking.entries.source'))
                                                    ->icon('heroicon-o-link'),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.additional-information.title'))
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('company.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.additional-information.entries.company'))
                                                    ->icon('heroicon-o-building-office'),
                                                TextEntry::make('currency.name')
                                                    ->placeholder('-')
                                                    ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.other-information.fieldset.additional-information.entries.currency'))
                                                    ->icon('heroicon-o-currency-dollar'),
                                            ])->columns(2),
                                    ]),
                            ]),

                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                TextEntry::make('note')
                                    ->html()
                                    ->placeholder('-')
                                    ->hiddenLabel(),
                            ]),
                    ]),
                ...$customInfolistEntries,
            ])
            ->columns(1);
    }
}
