<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Settings\OrderSettings;
use Webkul\Purchase\Settings\ProductSettings;

class OrderInfolist
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
                    ->compact(),

                Section::make(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.title'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Group::make([
                                    TextEntry::make('partner.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.vendor'))
                                        ->icon('heroicon-o-user-group'),
                                    TextEntry::make('partner_reference')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.vendor-reference'))
                                        ->icon('heroicon-o-document-text')
                                        ->placeholder('—'),
                                    TextEntry::make('requisition.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.agreement'))
                                        ->placeholder('—')
                                        ->icon('heroicon-o-document-check')
                                        ->visible(fn (OrderSettings $setting): bool => $setting->enable_purchase_agreements),
                                    TextEntry::make('currency.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.currency'))
                                        ->icon('heroicon-o-currency-dollar'),
                                ]),

                                Group::make([
                                    TextEntry::make('approved_at')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.confirmation-date'))
                                        ->dateTime()
                                        ->icon('heroicon-o-calendar')
                                        ->visible(fn ($record): bool => ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                    TextEntry::make('ordered_at')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.order-deadline'))
                                        ->dateTime()
                                        ->icon('heroicon-o-calendar')
                                        ->hidden(fn ($record): bool => ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                    TextEntry::make('planned_at')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.expected-arrival'))
                                        ->dateTime()
                                        ->icon('heroicon-o-calendar')
                                        ->hintColor('success')
                                        ->hint(fn ($record): string => $record->mail_reminder_confirmed ? __('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.confirmed-by-vendor') : ''),
                                ]),
                            ]),
                    ]),

                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.title'))
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.title'))
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                TextEntry::make('product.name')
                                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.product'))
                                                    ->icon('heroicon-o-cube'),
                                                TextEntry::make('planned_at')
                                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.expected-arrival'))
                                                    ->dateTime()
                                                    ->icon('heroicon-o-calendar'),
                                                TextEntry::make('product_qty')
                                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.quantity'))
                                                    ->icon('heroicon-o-calculator'),
                                                TextEntry::make('qty_received')
                                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.received'))
                                                    ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE]))
                                                    ->icon('heroicon-o-calculator'),
                                                TextEntry::make('qty_invoiced')
                                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.billed'))
                                                    ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE]))
                                                    ->icon('heroicon-o-calculator'),
                                                TextEntry::make('uom.name')
                                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.unit'))
                                                    ->icon('heroicon-o-beaker')
                                                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom),
                                                TextEntry::make('product_packaging_qty')
                                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.packaging-qty'))
                                                    ->icon('heroicon-o-calculator')
                                                    ->visible(fn (ProductSettings $settings) => $settings->enable_packagings),
                                                TextEntry::make('productPackaging.name')
                                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.packaging'))
                                                    ->icon('heroicon-o-gift')
                                                    ->visible(fn (ProductSettings $settings) => $settings->enable_packagings),
                                                TextEntry::make('price_unit')
                                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.unit-price'))
                                                    ->money(fn ($record) => $record->order->currency->code),
                                                TextEntry::make('taxes.name')
                                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.taxes'))
                                                    ->badge()
                                                    ->state(function ($record): array {
                                                        return $record->taxes->map(fn ($tax) => [
                                                            'name' => $tax->name,
                                                        ])->toArray();
                                                    })
                                                    ->icon('heroicon-o-receipt-percent')
                                                    ->formatStateUsing(fn ($state) => $state['name'])
                                                    ->placeholder('-'),
                                                TextEntry::make('discount')
                                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.discount-percentage'))
                                                    ->suffix('%'),
                                                TextEntry::make('price_subtotal')
                                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.amount'))
                                                    ->money(fn ($record) => $record->order->currency->code),
                                            ]),
                                    ])
                                    ->columnSpanFull(),

                                Group::make([
                                    TextEntry::make('untaxed_amount')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.untaxed-amount'))
                                        ->money(fn (Order $record) => $record->currency->code),
                                    TextEntry::make('tax_amount')
                                        ->label('Tax Amount')
                                        ->money(fn (Order $record) => $record->currency->code),
                                    TextEntry::make('total_amount')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.total-amount'))
                                        ->money(fn (Order $record) => $record->currency->code),
                                    TextEntry::make('invoice_status')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/order.table.columns.billing-status'))
                                        ->badge(),
                                ])
                                    ->columnSpanFull()
                                    ->columns(4),
                            ]),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.title'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Group::make([
                                            TextEntry::make('user.name')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.entries.buyer'))
                                                ->placeholder('—'),
                                            TextEntry::make('company.name')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.entries.company'))
                                                ->placeholder('—'),
                                            TextEntry::make('reference')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.entries.source-document'))
                                                ->placeholder('—'),
                                            TextEntry::make('incoterm.name')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.entries.incoterm'))
                                                ->icon('heroicon-o-question-mark-circle')
                                                ->placeholder('—')
                                                ->tooltip(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.entries.incoterm-tooltip')),
                                        ]),

                                        Group::make([
                                            TextEntry::make('paymentTerm.name')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.additional.entries.payment-term'))
                                                ->placeholder('—'),
                                        ]),
                                    ]),
                            ]),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.terms.title'))
                            ->schema([
                                TextEntry::make('description')
                                    ->hiddenLabel()
                                    ->markdown()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}
