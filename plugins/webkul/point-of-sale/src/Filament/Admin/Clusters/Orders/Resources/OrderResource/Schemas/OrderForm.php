<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        $prefix = 'point-of-sale::filament/admin/clusters/orders/resources/order.form.';

        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->columnSpanFull()
                    ->options(function ($record): array {
                        $visible = match (true) {
                            $record?->state === OrderState::CANCELED => [OrderState::DRAFT, OrderState::CANCELED],
                            $record?->state === OrderState::INVOICED => [OrderState::DRAFT, OrderState::PAID, OrderState::DONE, OrderState::INVOICED],
                            default                                  => [OrderState::DRAFT, OrderState::PAID, OrderState::DONE],
                        };

                        return collect($visible)
                            ->mapWithKeys(fn (OrderState $state): array => [$state->value => $state->getLabel()])
                            ->all();
                    })
                    ->default(OrderState::DRAFT->value)
                    ->disabled()
                    ->live()
                    ->reactive(),

                Section::make(__($prefix.'section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__($prefix.'section.general.fields.order'))
                                    ->disabled(),
                                DateTimePicker::make('ordered_at')
                                    ->label(__($prefix.'section.general.fields.ordered-at'))
                                    ->disabled(),
                                Select::make('partner_id')
                                    ->label(__($prefix.'section.general.fields.customer'))
                                    ->relationship('partner', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn ($record): bool => $record?->state === OrderState::INVOICED
                                        || ($record?->isRefund() && $record->refundedOrder?->partner_id)),
                                Select::make('user_id')
                                    ->label(__($prefix.'section.general.fields.cashier'))
                                    ->relationship('user', 'name')
                                    ->disabled(),
                            ])->columns(2),
                    ]),

                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__($prefix.'tabs.products.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Repeater::make('lines')
                                    ->hiddenLabel()
                                    ->compact()
                                    ->relationship()
                                    ->addable(false)
                                    ->deletable(false)
                                    ->table([
                                        TableColumn::make('full_product_name')
                                            ->label(__($prefix.'tabs.products.columns.product'))
                                            ->width(280)
                                            ->toggleable(),
                                        TableColumn::make('lots')
                                            ->label(__($prefix.'tabs.products.columns.lot'))
                                            ->toggleable(),
                                        TableColumn::make('qty')
                                            ->label(__($prefix.'tabs.products.columns.quantity'))
                                            ->toggleable(),
                                        TableColumn::make('uom')
                                            ->label(__($prefix.'tabs.products.columns.uom'))
                                            ->toggleable(),
                                        TableColumn::make('price_unit')
                                            ->label(__($prefix.'tabs.products.columns.unit-price'))
                                            ->toggleable(),
                                        TableColumn::make('discount')
                                            ->label(__($prefix.'tabs.products.columns.discount'))
                                            ->toggleable(),
                                        TableColumn::make('taxes')
                                            ->label(__($prefix.'tabs.products.columns.taxes'))
                                            ->toggleable(),
                                        TableColumn::make('price_subtotal')
                                            ->label(__($prefix.'tabs.products.columns.tax-excluded'))
                                            ->toggleable(),
                                        TableColumn::make('price_subtotal_incl')
                                            ->label(__($prefix.'tabs.products.columns.tax-included'))
                                            ->toggleable(),
                                        TableColumn::make('name')
                                            ->label(__($prefix.'tabs.products.columns.full-product-name'))
                                            ->toggleable(isToggledHiddenByDefault: true),
                                        TableColumn::make('customer_note')
                                            ->label(__($prefix.'tabs.products.columns.customer-note'))
                                            ->toggleable(isToggledHiddenByDefault: true),
                                        TableColumn::make('total_cost')
                                            ->label(__($prefix.'tabs.products.columns.total-cost'))
                                            ->toggleable(isToggledHiddenByDefault: true),
                                        TableColumn::make('margin')
                                            ->label(__($prefix.'tabs.products.columns.margin'))
                                            ->toggleable(isToggledHiddenByDefault: true),
                                        TableColumn::make('margin_percent')
                                            ->label(__($prefix.'tabs.products.columns.margin-percent'))
                                            ->toggleable(isToggledHiddenByDefault: true),
                                        TableColumn::make('refunded_qty')
                                            ->label(__($prefix.'tabs.products.columns.refunded-quantity'))
                                            ->toggleable(isToggledHiddenByDefault: true),
                                    ])
                                    ->schema([
                                        TextInput::make('full_product_name')->disabled(),
                                        TextInput::make('lots')
                                            ->disabled()
                                            ->formatStateUsing(fn ($record): ?string => $record?->lots->pluck('lot_name')->implode(', ')),
                                        TextInput::make('qty')->disabled(),
                                        TextInput::make('uom')
                                            ->disabled()
                                            ->formatStateUsing(fn ($record): ?string => $record?->uom?->name),
                                        TextInput::make('price_unit')->disabled(),
                                        TextInput::make('discount')->disabled(),
                                        TextInput::make('taxes')
                                            ->disabled()
                                            ->formatStateUsing(fn ($record): ?string => $record?->taxes->pluck('name')->implode(', ')),
                                        TextInput::make('price_subtotal')->disabled(),
                                        TextInput::make('price_subtotal_incl')->disabled(),
                                        TextInput::make('name')->disabled(),
                                        TextInput::make('customer_note')->disabled(),
                                        TextInput::make('total_cost')->disabled(),
                                        TextInput::make('margin')->disabled(),
                                        TextInput::make('margin_percent')->disabled(),
                                        TextInput::make('refunded_qty')->disabled(),
                                    ])
                                    ->extraItemActions([
                                        Action::make('viewProduct')
                                            ->tooltip(__($prefix.'tabs.products.actions.open-product'))
                                            ->size('sm')
                                            ->iconButton()
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->url(function (array $arguments, Get $get): ?string {
                                                $productId = $get('lines')[$arguments['item']]['product_id'] ?? null;

                                                return $productId
                                                    ? ProductResource::getUrl('view', ['record' => $productId])
                                                    : null;
                                            })
                                            ->openUrlInNewTab()
                                            ->visible(fn (array $arguments, Get $get): bool => filled($get('lines')[$arguments['item']]['product_id'] ?? null)),
                                    ]),

                                View::make('point-of-sale::filament.admin.clusters.orders.resources.order.summary')
                                    ->columnSpanFull(),
                            ]),

                        Tab::make(__($prefix.'tabs.payments.title'))
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Repeater::make('payments')
                                    ->hiddenLabel()
                                    ->compact()
                                    ->relationship()
                                    ->addActionLabel(__($prefix.'tabs.payments.add'))
                                    ->table([
                                        TableColumn::make('paid_at')
                                            ->label(__($prefix.'tabs.payments.fields.paid-at'))
                                            ->width(200),
                                        TableColumn::make('payment_method_id')
                                            ->label(__($prefix.'tabs.payments.fields.method'))
                                            ->markAsRequired()
                                            ->width(200),
                                        TableColumn::make('amount')
                                            ->label(__($prefix.'tabs.payments.fields.amount'))
                                            ->markAsRequired()
                                            ->width(150),
                                        TableColumn::make('card_type')
                                            ->label(__($prefix.'tabs.payments.fields.card-type'))
                                            ->toggleable(),
                                        TableColumn::make('card_brand')
                                            ->label(__($prefix.'tabs.payments.fields.card-brand'))
                                            ->toggleable(),
                                        TableColumn::make('cardholder_name')
                                            ->label(__($prefix.'tabs.payments.fields.cardholder-name'))
                                            ->toggleable(),
                                    ])
                                    ->schema([
                                        DateTimePicker::make('paid_at')
                                            ->default(now())
                                            ->disabled(static::isSettledPayment(...)),
                                        Select::make('payment_method_id')
                                            ->relationship(
                                                'paymentMethod',
                                                'name',
                                                fn (Builder $query, $livewire): Builder => $query->whereIn('id', $livewire->getRecord()?->config?->paymentMethods->pluck('id')->all() ?? []),
                                            )
                                            ->required()
                                            ->disabled(static::isSettledPayment(...)),
                                        TextInput::make('amount')
                                            ->numeric()
                                            ->required()
                                            ->disabled(static::isSettledPayment(...)),
                                        TextInput::make('card_type')
                                            ->disabled(static::isSettledPayment(...)),
                                        TextInput::make('card_brand')
                                            ->disabled(static::isSettledPayment(...)),
                                        TextInput::make('cardholder_name')
                                            ->disabled(static::isSettledPayment(...)),
                                    ])
                                    ->deletable(fn ($livewire): bool => ! static::isLockedOrder($livewire->getRecord()))
                                    ->addable(fn ($livewire): bool => ! static::isLockedOrder($livewire->getRecord())),
                            ]),

                        Tab::make(__($prefix.'tabs.extra-info.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextInput::make('reference')
                                            ->label(__($prefix.'tabs.extra-info.fields.receipt-number'))
                                            ->disabled(),
                                        TextInput::make('tracking_number')
                                            ->label(__($prefix.'tabs.extra-info.fields.tracking-number'))
                                            ->disabled(),
                                        TextInput::make('email')
                                            ->label(__($prefix.'tabs.extra-info.fields.email'))
                                            ->email(),
                                        TextInput::make('mobile')
                                            ->label(__($prefix.'tabs.extra-info.fields.mobile'))
                                            ->tel(),
                                    ])->columns(2),
                            ]),

                        Tab::make(__($prefix.'tabs.notes.title'))
                            ->icon('heroicon-o-chat-bubble-bottom-center-text')
                            ->schema([
                                Textarea::make('note')
                                    ->hiddenLabel()
                                    ->rows(4),
                            ]),
                    ]),
            ]);
    }

    /**
     * A payment that is already on the order is a settled fact, so only lines
     * added in this editing session accept input.
     */
    protected static function isSettledPayment(?Model $record): bool
    {
        return $record?->exists ?? false;
    }

    protected static function isLockedOrder(?Model $order): bool
    {
        return in_array($order?->state, [OrderState::DONE, OrderState::INVOICED], true)
            || ($order?->print_count ?? 0) > 0;
    }
}
