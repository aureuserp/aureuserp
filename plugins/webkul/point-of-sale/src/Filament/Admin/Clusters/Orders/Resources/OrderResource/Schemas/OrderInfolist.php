<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn as InfolistTableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\PointOfSale\Enums\OrderState;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->columnSpanFull()
                    ->options(function ($record): array {
                        $visible = match (true) {
                            $record->state === OrderState::CANCELED => [OrderState::DRAFT, OrderState::CANCELED],
                            $record->state === OrderState::INVOICED => [OrderState::DRAFT, OrderState::PAID, OrderState::DONE, OrderState::INVOICED],
                            default                                 => [OrderState::DRAFT, OrderState::PAID, OrderState::DONE],
                        };

                        return collect($visible)
                            ->mapWithKeys(fn (OrderState $state): array => [$state->value => $state->getLabel()])
                            ->all();
                    })
                    ->default(OrderState::DRAFT->value),

                Section::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.section.general.entries.order'))
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->size(TextSize::Large),
                            ])->columns(2),

                        Grid::make()
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->placeholder('-')
                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.section.general.entries.customer'))
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('session.name')
                                    ->placeholder('-')
                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.section.general.entries.session'))
                                    ->icon('heroicon-o-clock'),
                                TextEntry::make('ordered_at')
                                    ->placeholder('-')
                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.section.general.entries.ordered-at'))
                                    ->icon('heroicon-o-calendar')
                                    ->dateTime(),
                                TextEntry::make('config.name')
                                    ->placeholder('-')
                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.section.general.entries.point-of-sale'))
                                    ->icon('heroicon-o-building-storefront'),
                            ])->columns(2),
                    ]),

                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.order-line.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->table([
                                        InfolistTableColumn::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.order-line.entries.product'))
                                            ->width(250),
                                        InfolistTableColumn::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.order-line.entries.quantity'))
                                            ->width(100),
                                        InfolistTableColumn::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.order-line.entries.unit-price'))
                                            ->width(120),
                                        InfolistTableColumn::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.order-line.entries.taxes'))
                                            ->width(150),
                                        InfolistTableColumn::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.order-line.entries.discount'))
                                            ->width(100),
                                        InfolistTableColumn::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.order-line.entries.amount'))
                                            ->width(120),
                                    ])
                                    ->schema([
                                        TextEntry::make('full_product_name')
                                            ->placeholder('-')
                                            ->tooltip(fn ($record) => $record->product?->name)
                                            ->iconColor('primary'),

                                        TextEntry::make('qty')
                                            ->placeholder('-')
                                            ->numeric(),

                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->order?->currency?->name)
                                            ->weight(FontWeight::Medium),

                                        TextEntry::make('taxes')
                                            ->badge()
                                            ->state(fn ($record): array => $record->taxes->map(fn ($tax) => ['name' => $tax->name])->toArray())
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-'),

                                        TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->numeric()
                                            ->suffix('%'),

                                        TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->order?->currency?->name),
                                    ]),

                                Grid::make(3)
                                    ->schema([
                                        Grid::make(1)
                                            ->schema([
                                                TextEntry::make('amount_untaxed')
                                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.order-line.totals.untaxed'))
                                                    ->inlineLabel()
                                                    ->alignEnd()
                                                    ->money(fn ($record) => $record->currency?->name),
                                                TextEntry::make('amount_tax')
                                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.order-line.totals.taxes'))
                                                    ->inlineLabel()
                                                    ->alignEnd()
                                                    ->money(fn ($record) => $record->currency?->name),
                                                TextEntry::make('amount_total')
                                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.order-line.totals.total'))
                                                    ->inlineLabel()
                                                    ->alignEnd()
                                                    ->weight(FontWeight::Bold)
                                                    ->money(fn ($record) => $record->currency?->name),
                                                TextEntry::make('margin')
                                                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.order-line.totals.margin'))
                                                    ->inlineLabel()
                                                    ->alignEnd()
                                                    ->money(fn ($record) => $record->currency?->name)
                                                    ->formatStateUsing(fn ($state, $record) => money($state, $record->currency?->name)
                                                        .' ('.number_format((float) $record->margin_percent * 100, 2).'%)'),
                                            ])
                                            ->columnStart(3),
                                    ]),
                            ]),

                        Tab::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.payments.title'))
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                RepeatableEntry::make('payments')
                                    ->hiddenLabel()
                                    ->table([
                                        InfolistTableColumn::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.payments.entries.method'))
                                            ->width(250),
                                        InfolistTableColumn::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.payments.entries.amount'))
                                            ->width(150),
                                        InfolistTableColumn::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.payments.entries.paid-at'))
                                            ->width(200),
                                    ])
                                    ->schema([
                                        TextEntry::make('paymentMethod.name')
                                            ->placeholder('-'),
                                        TextEntry::make('amount')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->order?->currency?->name),
                                        TextEntry::make('paid_at')
                                            ->placeholder('-')
                                            ->dateTime(),
                                    ]),
                            ]),

                        Tab::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.extra-info.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextEntry::make('reference')
                                            ->placeholder('-')
                                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.extra-info.entries.receipt-number')),
                                        TextEntry::make('tracking_number')
                                            ->placeholder('-')
                                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.extra-info.entries.tracking-number')),
                                        TextEntry::make('email')
                                            ->placeholder('-')
                                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.extra-info.entries.email')),
                                        TextEntry::make('mobile')
                                            ->placeholder('-')
                                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.extra-info.entries.mobile')),
                                    ])->columns(2),
                            ]),

                        Tab::make(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextEntry::make('reference')
                                            ->placeholder('-')
                                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.other-information.entries.reference')),
                                        TextEntry::make('receipt_code')
                                            ->placeholder('-')
                                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.other-information.entries.receipt-number')),
                                        TextEntry::make('operation.name')
                                            ->placeholder('-')
                                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.other-information.entries.operation')),
                                        TextEntry::make('user.name')
                                            ->placeholder('-')
                                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.other-information.entries.cashier')),
                                        TextEntry::make('amount_paid')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency?->name)
                                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.other-information.entries.paid')),
                                        TextEntry::make('amount_return')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency?->name)
                                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.infolist.tabs.other-information.entries.change')),
                                    ])->columns(2),
                            ]),
                    ]),
            ]);
    }
}
