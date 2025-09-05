<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Webkul\Sale\Livewire\Summary;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Settings\PriceSettings;
use Webkul\Sale\Settings\ProductSettings;

class QuotationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('state')
                            ->badge(),
                    ])
                    ->compact()->columnSpanFull(),
                Section::make(__('sales::filament/clusters/orders/resources/quotation.infolist.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
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
                    ])->columnSpanFull(),
                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.product'))
                                            ->icon('heroicon-o-cube'),
                                        TextEntry::make('product_uom_qty')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.quantity'))
                                            ->icon('heroicon-o-hashtag'),
                                        TextEntry::make('uom.name')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.uom'))
                                            ->icon('heroicon-o-scale'),
                                        TextEntry::make('customer_lead')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.lead-time'))
                                            ->icon('heroicon-o-clock'),
                                        TextEntry::make('product_packaging_qty')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_packagings)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.packaging-qty'))
                                            ->icon('heroicon-o-arrow-path-rounded-square'),
                                        TextEntry::make('product_packaging_id')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_packagings)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.packaging'))
                                            ->icon('heroicon-o-archive-box'),
                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.unit-price'))
                                            ->icon('heroicon-o-currency-dollar')
                                            ->money(fn ($record) => $record->currency->code),
                                        TextEntry::make('purchase_price')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.cost'))
                                            ->icon('heroicon-o-banknotes')
                                            ->money(fn ($record) => $record->currency->code),
                                        TextEntry::make('margin')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.margin'))
                                            ->icon('heroicon-o-currency-dollar')
                                            ->visible(fn (PriceSettings $settings) => $settings->enable_margin)
                                            ->money(fn ($record) => $record->currency->code),
                                        TextEntry::make('margin_percent')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.margin-percentage'))
                                            ->icon('heroicon-o-chart-bar')
                                            ->visible(fn (PriceSettings $settings) => $settings->enable_margin)
                                            ->suffix('%'),
                                        TextEntry::make('taxes.name')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => [
                                                    'name' => $tax->name,
                                                ])->toArray();
                                            })
                                            ->icon('heroicon-o-receipt-percent')
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.taxes'))
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.discount-percentage'))
                                            ->icon('heroicon-o-tag')
                                            ->suffix('%'),
                                        TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.products.entries.sub-total'))
                                            ->icon('heroicon-o-calculator')
                                            ->money(fn ($record) => $record->currency->code),
                                    ])->columns(5),
                                Livewire::make(Summary::class, function ($record, PriceSettings $settings) {
                                    return [
                                        'currency'     => $record->currency,
                                        'enableMargin' => $settings->enable_margin,
                                        'products'     => $record->lines->map(function ($item) {
                                            return [
                                                ...$item->toArray(),
                                                'taxes' => $item->taxes->pluck('id')->toArray() ?? [],
                                            ];
                                        })->toArray(),
                                    ];
                                }),
                            ]),
                        Tab::make(__('Optional Products'))
                            ->icon('heroicon-o-arrow-path-rounded-square')
                            ->hidden(fn (Order $record) => $record->optionalLines->isEmpty())
                            ->schema([
                                RepeatableEntry::make('optionalLines')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.product'))
                                            ->icon('heroicon-o-cube'),
                                        TextEntry::make('uom.name')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.uom'))
                                            ->icon('heroicon-o-scale'),
                                        TextEntry::make('quantity')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.quantity'))
                                            ->icon('heroicon-o-hashtag'),
                                        TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.discount-percentage'))
                                            ->icon('heroicon-o-tag')
                                            ->suffix('%'),
                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.unit-price'))
                                            ->icon('heroicon-o-currency-dollar'),
                                        TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.infolist.tabs.order-line.repeater.product-optional.entries.sub-total'))
                                            ->icon('heroicon-o-calculator'),
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
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ]);
    }
}
