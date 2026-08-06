<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\Purchase\Livewire\OrderSummary;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class OrderInfolist
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

                        if ($record->state !== OrderState::CANCELED) {
                            unset($options[OrderState::CANCELED->value]);
                        }

                        if ($record->state !== OrderState::DONE) {
                            unset($options[OrderState::DONE->value]);
                        }

                        return $options;
                    })
                    ->default(OrderState::DRAFT),

                Section::make(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.title'))
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.sections.general.entries.purchase-order'))
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->size(TextSize::Large),
                            ])->columns(2),

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
                                        ->visible(OrderResource::getOrderSettings()->enable_purchase_agreements),
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
                                ...$customInfolistEntries,
                            ]),
                    ]),

                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.title'))
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->columnManager()
                                    ->columnManagerColumns(2)
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.title'))
                                    ->table(fn ($record) => [
                                        InfolistTableColumn::make('name')
                                            ->alignStart()
                                            ->toggleable()
                                            ->width(250)
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.product')),
                                        InfolistTableColumn::make('planned_at')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.expected-arrival')),
                                        InfolistTableColumn::make('product_qty')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.quantity')),
                                        InfolistTableColumn::make('qty_received')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->visible(fn (): bool => in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.received')),
                                        InfolistTableColumn::make('qty_invoiced')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->visible(fn (): bool => in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.billed')),
                                        InfolistTableColumn::make('uom')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->visible(OrderResource::getProductSettings()->enable_uom)
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.unit')),
                                        InfolistTableColumn::make('product_packaging_qty')
                                            ->alignStart()
                                            ->width(150)
                                            ->toggleable()
                                            ->visible(OrderResource::getProductSettings()->enable_packagings)
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.packaging-qty')),
                                        InfolistTableColumn::make('productPackaging')
                                            ->alignStart()
                                            ->width(150)
                                            ->toggleable()
                                            ->visible(OrderResource::getProductSettings()->enable_packagings)
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.packaging')),
                                        InfolistTableColumn::make('price_unit')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.unit-price')),
                                        InfolistTableColumn::make('taxes')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.taxes')),
                                        InfolistTableColumn::make('discount')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.discount-percentage')),
                                        InfolistTableColumn::make('price_subtotal')
                                            ->alignStart()
                                            ->width(100)
                                            ->toggleable()
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.amount')),
                                    ])
                                    ->schema([

                                        TextEntry::make('name'),
                                        TextEntry::make('planned_at')
                                            ->date(),
                                        TextEntry::make('product_qty'),
                                        TextEntry::make('qty_received')
                                            ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE])),
                                        TextEntry::make('qty_invoiced')
                                            ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE])),
                                        TextEntry::make('uom')
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->visible(OrderResource::getProductSettings()->enable_uom),
                                        TextEntry::make('product_packaging_qty')
                                            ->visible(OrderResource::getProductSettings()->enable_packagings),
                                        TextEntry::make('productPackaging')
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->visible(OrderResource::getProductSettings()->enable_packagings),
                                        TextEntry::make('price_unit')
                                            ->money(fn ($record) => $record->order->currency->code),
                                        TextEntry::make('taxes')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => [
                                                    'name' => $tax->name,
                                                ])->toArray();
                                            })
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-'),
                                        TextEntry::make('discount')
                                            ->suffix('%'),
                                        TextEntry::make('price_subtotal')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.infolist.tabs.products.repeater.products.entries.amount'))
                                            ->money(fn ($record) => $record->order->currency->code),
                                    ]),

                                Livewire::make(OrderSummary::class, function ($record) {
                                    $subtotal = 0;
                                    $totalTax = 0;
                                    $grandTotal = 0;

                                    foreach ($record->lines as $line) {
                                        $subtotal += floatval($line->price_subtotal ?? 0);
                                        $totalTax += floatval($line->price_tax ?? 0);
                                        $grandTotal += floatval($line->price_total ?? 0);
                                    }

                                    return [
                                        'currency'         => $record->currency,
                                        'subtotal'         => round($subtotal, 2),
                                        'totalTax'         => round($totalTax, 2),
                                        'grandTotal'       => round($grandTotal, 2),
                                    ];
                                })
                                    ->key('order-summary-view'),
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
