<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Filament\Resources\IncotermResource;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Account\Models\Tax;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Inventory\Enums as InventoryEnums;
use Webkul\Inventory\Models\OperationType;
use Webkul\PluginManager\Package;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Packaging;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Enums\QtyReceivedMethod;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Enums\RequisitionType;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource;
use Webkul\Purchase\Livewire\OrderSummary;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Purchase\Models\Product;
use Webkul\Purchase\Models\Requisition;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class OrderForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = OrderState::options();

                        if ($record && $record->state !== OrderState::CANCELED) {
                            unset($options[OrderState::CANCELED->value]);
                        }

                        if ($record && $record->state !== OrderState::DONE) {
                            unset($options[OrderState::DONE->value]);
                        }

                        if (! $record || $record->state !== OrderState::TO_APPROVE) {
                            unset($options[OrderState::TO_APPROVE->value]);
                        }

                        return $options;
                    })
                    ->default(OrderState::DRAFT)
                    ->disabled(),
                Section::make(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.title'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Select::make('partner_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.vendor'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('id')->withTrashed()
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema) => VendorResource::form($schema))
                                    ->afterStateUpdated(fn ($state, Set $set, Get $get) => static::handleVendorChange($state, $set, $get))
                                    ->live()
                                    ->reactive()
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                TextInput::make('partner_reference')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.vendor-reference'))
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.vendor-reference-tooltip')),
                                Select::make('requisition_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.agreement'))
                                    ->relationship(
                                        name: 'requisition',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: function (Builder $query, Get $get, $operation, $state) {
                                            $query
                                                ->where('partner_id', $get('partner_id'))
                                                ->where('company_id', $get('company_id') ?? current_company_id())
                                                ->where(function ($query) use ($operation, $state) {
                                                    $query->where('state', RequisitionState::CONFIRMED);
                                                    if ($operation !== 'create' && $state) {
                                                        $query->orWhere('id', $state);
                                                    }
                                                })
                                                ->where(function ($query) {
                                                    $query->whereNull('ends_at')
                                                        ->orWhere('ends_at', '>=', now());
                                                })
                                                ->where(function ($query) {
                                                    $query->whereNull('starts_at')
                                                        ->orWhere('starts_at', '<=', now());
                                                });
                                        })
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->searchable()
                                    ->preload()
                                    ->visible(OrderResource::getOrderSettings()->enable_purchase_agreements)
                                    ->afterStateUpdated(fn ($state, Set $set, Get $get) => static::handleRequisitionChange($state, $set, $get))
                                    ->live(),
                                Select::make('currency_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.currency'))
                                    ->relationship(
                                        name: 'currency',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->active(),
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->default(current_company()?->currency_id)
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                            ]),

                        Group::make()
                            ->schema([
                                DateTimePicker::make('approved_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.confirmation-date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->default(now())
                                    ->disabled()
                                    ->visible(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                DateTimePicker::make('ordered_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.order-deadline'))
                                    ->native(false)
                                    ->required()
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->default(now())
                                    ->hidden(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                DateTimePicker::make('planned_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.expected-arrival'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->hint('Test')
                                    ->hint(fn ($record): string => $record && $record->mail_reminder_confirmed ? __('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.confirmed-by-vendor') : '')
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                Select::make('operation_type_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.deliver-to'))
                                    ->relationship(
                                        name: 'operationType',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                            ->whereIn('type', [
                                                InventoryEnums\OperationType::INCOMING,
                                                InventoryEnums\OperationType::DROPSHIP,
                                            ])
                                            ->where(function (Builder $query) use ($get) {
                                                $query->whereNull('warehouse_id')
                                                    ->orWhereHas('warehouse', fn (Builder $q) => $q->where('company_id', $get('company_id') ?? current_company_id()));
                                            }),
                                    )
                                    ->getOptionLabelFromRecordUsing(function (OperationType $record) {
                                        if (! $record->warehouse) {
                                            return $record->name;
                                        }

                                        return $record->warehouse->name.': '.$record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (): bool => static::canUseInventoryWarehouses())
                                    ->default(fn (Get $get) => static::getInventoryOperationTypeId($get('company_id') ?? current_company_id()))
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                            ]),
                    ])
                    ->columns(2),

                Tabs::make()
                    ->schema([
                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.title'))
                            ->schema([
                                static::getProductRepeater(),
                                Livewire::make(OrderSummary::class, function (Get $get, $livewire) {
                                    $totals = self::calculateOrderTotals($get, $livewire);

                                    return [
                                        'currency'   => Currency::find($get('currency_id')),
                                        'subtotal'   => $totals['subtotal'],
                                        'totalTax'   => $totals['totalTax'],
                                        'grandTotal' => $totals['grandTotal'],
                                    ];
                                })
                                    ->key('orderSummary')
                                    ->reactive()
                                    ->visible(fn (Get $get) => $get('currency_id') && ! empty($get('products'))),
                            ]),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.title'))
                            ->schema(array_merge([
                                Group::make()
                                    ->schema([
                                        Select::make('user_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.buyer'))
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(Auth::id())
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                        Select::make('company_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.company'))
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
                                            ->required()
                                            ->live()
                                            ->default(current_company_id())
                                            ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                                                $set('operation_type_id', static::getInventoryOperationTypeId($state ?? current_company_id()));

                                                clear_foreign_company_values($set, $get, [
                                                    'payment_term_id' => PaymentTerm::class,
                                                ], $state);
                                            })
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                        TextInput::make('origin')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.source-document'))
                                            ->maxLength(255),
                                        Select::make('incoterm_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.incoterm'))
                                            ->relationship('incoterm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema) => IncotermResource::form($schema))
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.incoterm-tooltip'))
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                        TextInput::make('incoterm_location')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.incoterm-location'))
                                            ->maxLength(255)
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                    ]),

                                Group::make()
                                    ->schema([
                                        Select::make('payment_term_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.payment-term'))
                                            ->relationship(
                                                'paymentTerm',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                    ]),
                            ], $customFormFields))
                            ->columns(2),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.terms.title'))
                            ->schema([
                                RichEditor::make('description')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function getProductRepeater(): Repeater
    {
        return Repeater::make('products')
            ->relationship('lines')
            ->hiddenLabel()
            ->live()
            ->compact()
            ->reactive()
            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.title'))
            ->addActionLabel(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.add-product-line'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(function (Action $action) {
                $action->requiresConfirmation();

                $action->before(function (Action $action, $livewire) {
                    $arguments = $action->getArguments();

                    if (
                        ! empty($arguments['item'] ?? '') &&
                        ! str_starts_with($arguments['item'] ?? '', 'record-')
                    ) {
                        return;
                    }

                    if ($livewire->getRecord()?->state === OrderState::PURCHASE) {
                        Notification::make()
                            ->danger()
                            ->title(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.delete-action.error.title'))
                            ->body(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.delete-action.error.body'))
                            ->send();

                        $action->cancel();

                        return;
                    }
                });

                $action->after(function (Get $get, $livewire) {
                    $totals = self::calculateOrderTotals($get, $livewire);

                    $livewire->dispatch('itemUpdated', $totals);
                });
            })
            ->deletable(fn ($record): bool => ! in_array($record?->state, [OrderState::DONE, OrderState::CANCELED]))
            ->addable(fn ($record): bool => ! in_array($record?->state, [OrderState::DONE, OrderState::CANCELED]))
            ->table(fn ($record) => [
                TableColumn::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.product'))
                    ->width(300)
                    ->resizable()
                    ->markAsRequired(),

                TableColumn::make('planned_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.expected-arrival'))
                    ->markAsRequired()
                    ->resizable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TableColumn::make('product_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.quantity'))
                    ->resizable()
                    ->markAsRequired(),

                TableColumn::make('qty_received')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.received'))
                    ->markAsRequired()
                    ->resizable()
                    ->visible(fn (): bool => Package::isPluginInstalled('inventories') && in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->toggleable(),

                TableColumn::make('qty_received_manual')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.received'))
                    ->markAsRequired()
                    ->resizable()
                    ->visible(fn (): bool => ! Package::isPluginInstalled('inventories') && in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->toggleable(),

                TableColumn::make('qty_invoiced')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.billed'))
                    ->resizable()
                    ->visible(fn (): bool => in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->toggleable(),

                TableColumn::make('uom_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.unit'))
                    ->resizable()
                    ->markAsRequired()
                    ->visible(OrderResource::getProductSettings()->enable_uom)
                    ->toggleable(),

                TableColumn::make('product_packaging_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.packaging-qty'))
                    ->resizable()
                    ->visible(OrderResource::getProductSettings()->enable_packagings)
                    ->toggleable(),

                TableColumn::make('product_packaging_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.packaging'))
                    ->resizable()
                    ->visible(OrderResource::getProductSettings()->enable_packagings)
                    ->toggleable(),

                TableColumn::make('price_unit')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.unit-price'))
                    ->resizable()
                    ->markAsRequired(),

                TableColumn::make('taxes')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.taxes'))
                    ->resizable()
                    ->wrapHeader(false)
                    ->toggleable(),

                TableColumn::make('discount')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.discount-percentage'))
                    ->resizable()
                    ->wrapHeader(false)
                    ->toggleable(isToggledHiddenByDefault: true),

                TableColumn::make('price_subtotal')
                    ->resizable()
                    ->wrapHeader(false)
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.columns.amount')),
            ])
            ->schema(fn ($record) => [
                Select::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.product'))
                    ->relationship(
                        'product',
                        'name',
                        fn (Builder $query, Get $get) => $query
                            ->withTrashed()
                            ->where('type', ProductType::GOODS)
                            ->whereNull('is_configurable')
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->wrapOptionLabels(false)
                    ->disabled(fn (Get $get): bool => filled($get('id')) && in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE, OrderState::CANCELED]))
                    ->disableOptionWhen(function ($value, $state, $component, $label) {
                        if (str_contains($label, ' (Deleted)')) {
                            return true;
                        }

                        $repeater = $component->getParentRepeater();

                        if (! $repeater) {
                            return false;
                        }

                        return collect($repeater->getState())
                            ->pluck(
                                (string) str($component->getStatePath())
                                    ->after("{$repeater->getStatePath()}.")
                                    ->after('.'),
                            )
                            ->flatten()
                            ->diff(Arr::wrap($state))
                            ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                            ->contains($value);
                    })
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterProductUpdated($set, $get);
                    }),
                DateTimePicker::make('planned_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.expected-arrival'))
                    ->native(false)
                    ->suffixIcon('heroicon-o-calendar')
                    ->required()
                    ->default(now())
                    ->default(function (Get $get, Set $set) {
                        if (empty($get('../../planned_at'))) {
                            $set('../../planned_at', now());
                        }

                        return now();
                    })
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        $set('../../planned_at', $state);
                    })
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                TextInput::make('product_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.quantity'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->rule(function (Get $get): \Closure {
                        return function (string $attribute, $value, \Closure $fail) use ($get): void {
                            $qtyReceived = (float) ($get('qty_received') ?? 0);

                            if ($qtyReceived > 0 && (float) $value < $qtyReceived) {
                                $fail(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.notifications.quantity-below-received.body', ['qty' => $qtyReceived]));
                            }
                        };
                    })
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterProductQtyUpdated($set, $get);
                    })
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                TextInput::make('qty_received')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.received'))
                    ->required()
                    ->default(0)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->visible(fn (): bool => Package::isPluginInstalled('inventories') && in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED]) || $record?->qty_received_method == QtyReceivedMethod::STOCK_MOVE),
                TextInput::make('qty_received_manual')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.received'))
                    ->required()
                    ->default(0)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->visible(fn ($record): bool => ! Package::isPluginInstalled('inventories') && in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED]) || $record?->qty_received_method == QtyReceivedMethod::STOCK_MOVE),

                TextInput::make('qty_invoiced')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.billed'))
                    ->default(0)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->visible(fn (): bool => in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE]))
                    ->disabled(),

                Select::make('uom_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get) {
                            $product = Product::withTrashed()->find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))->orderBy('id');
                        },
                    )
                    ->required()
                    ->live()
                    ->native(false)
                    ->wrapOptionLabels(false)
                    ->selectablePlaceholder(false)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterUOMUpdated($set, $get);
                    })
                    ->visible(OrderResource::getProductSettings()->enable_uom)
                    ->disabled(fn (Get $get): bool => filled($get('id')) && in_array($record?->state, [OrderState::PURCHASE, OrderState::DONE, OrderState::CANCELED])),

                TextInput::make('product_packaging_qty')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.packaging-qty'))
                    ->live(onBlur: true)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterProductPackagingQtyUpdated($set, $get);
                    })
                    ->visible(OrderResource::getProductSettings()->enable_packagings)
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                Select::make('product_packaging_id')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.packaging'))
                    ->relationship(
                        'productPackaging',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('product_id', $get('product_id')),
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->wrapOptionLabels(false)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterProductPackagingUpdated($set, $get);
                    })
                    ->visible(OrderResource::getProductSettings()->enable_packagings)
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                TextInput::make('price_unit')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.unit-price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        self::calculateLineTotals($set, $get);
                    })
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                Select::make('taxes')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.taxes'))
                    ->relationship(
                        'taxes',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->where('type_tax_use', TypeTaxUse::PURCHASE)
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->wrapOptionLabels(false)
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        self::calculateLineTotals($set, $get);
                    })
                    ->live()
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                TextInput::make('discount')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.discount-percentage'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        self::calculateLineTotals($set, $get);
                    })
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                TextInput::make('price_subtotal')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.amount'))
                    ->default(0)
                    ->readOnly()
                    ->disabled(fn (): bool => in_array($record?->state, [OrderState::DONE, OrderState::CANCELED])),

                Hidden::make('product_uom_qty')
                    ->default(0),

                Hidden::make('price_tax')
                    ->default(0),

                Hidden::make('price_total')
                    ->default(0),

                Hidden::make('from_requisition')
                    ->default(false),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $record) {
                $product = Product::find($data['product_id']);

                $qtyReceivedMethod = QtyReceivedMethod::MANUAL;

                if (Package::isPluginInstalled('inventories')) {
                    $qtyReceivedMethod = QtyReceivedMethod::STOCK_MOVE;
                }

                $data = array_merge($data, [
                    'name'                => $product->name,
                    'state'               => $record->state->value,
                    'qty_received_method' => $qtyReceivedMethod,
                    'uom_id'              => $data['uom_id'] ?? $product->uom_id,
                    'currency_id'         => $record->currency_id,
                    'partner_id'          => $record->partner_id,
                    'creator_id'          => Auth::id(),
                    'company_id'          => $record->company_id,
                ]);

                return $data;
            })->extraItemActions([
                Action::make('openProduct')
                    ->tooltip(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.actions.open-product.tooltip'))
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(function (array $arguments, Get $get): ?string {
                        $productId = $get("products.{$arguments['item']}.product_id");

                        if (! $productId) {
                            return null;
                        }

                        return ProductResource::getUrl('edit', ['record' => $productId]);
                    }, shouldOpenInNewTab: true)
                    ->hidden(
                        fn (array $arguments, Get $get): bool => empty($get("products.{$arguments['item']}.product_id"))
                    ),
            ]);
    }

    private static function afterProductUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $set('uom_id', $product->uom_id);

        $uomQuantity = static::calculateUnitQuantity($product->uom_id, $get('product_qty'), $product->uom_id);

        $set('product_uom_qty', round($uomQuantity, 2));

        $priceUnit = static::calculateUnitPrice($get);

        $set('price_unit', round($priceUnit, 2));

        $set('taxes', Tax::forProduct($product, TypeTaxUse::PURCHASE, $get('../../company_id')));

        $packaging = static::getBestPackaging($get('product_id'), round($uomQuantity, 2));

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductQtyUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('product_qty'), $product->uom_id);

        $set('product_uom_qty', round($uomQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $uomQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        if (! $get('from_requisition')) {
            $priceUnit = static::calculateUnitPrice($get);

            $set('price_unit', round($priceUnit, 2));
        }

        self::calculateLineTotals($set, $get);

        self::checkBlanketOrderQtyLimit($get);
    }

    private static function afterUOMUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('product_qty'), $product->uom_id);

        $set('product_uom_qty', round($uomQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $uomQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        $priceUnit = static::calculateUnitPrice($get);

        $set('price_unit', round($priceUnit, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductPackagingQtyUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        if ($get('product_packaging_id')) {
            $packaging = Packaging::find($get('product_packaging_id'));

            $packagingQty = floatval($get('product_packaging_qty') ?? 0);

            $productUOMQty = $packagingQty * $packaging->qty;

            $set('product_uom_qty', round($productUOMQty, 2));

            $uom = UOM::find($get('uom_id'));

            $productQty = $uom ? $productUOMQty * $uom->factor : $productUOMQty;

            $set('product_qty', round($productQty, 2));
        }

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductPackagingUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        if ($get('product_packaging_id')) {
            $packaging = Packaging::find($get('product_packaging_id'));

            $productUOMQty = $get('product_uom_qty') ?: 1;

            if ($packaging) {
                $packagingQty = $productUOMQty / $packaging->qty;

                $set('product_packaging_qty', $packagingQty);
            }
        } else {
            $set('product_packaging_qty', null);
        }

        self::calculateLineTotals($set, $get);
    }

    private static function calculateUnitQuantity($fromUomId, $quantity, $toUomId = null): float
    {
        if (! $fromUomId || ! filled($quantity)) {
            return (float) ($quantity ?? 0);
        }

        $fromUom = UOM::find($fromUomId);

        if (! $fromUom) {
            return (float) ($quantity ?? 0);
        }

        $toUom = $toUomId ? UOM::find($toUomId) : $fromUom;

        if (! $toUom) {
            return (float) ($quantity ?? 0);
        }

        return $fromUom->computeQuantity((float) ($quantity ?? 0), $toUom, false);
    }

    private static function calculateUnitPrice($get)
    {
        $product = Product::find($get('product_id'));

        $vendorPrices = $product->sellers->sortByDesc('sort');

        if ($get('../../partner_id')) {
            $vendorPrices = $vendorPrices->where('partner_id', $get('../../partner_id'));
        }

        $vendorPrices = $vendorPrices->where('min_qty', '<=', $get('product_qty') ?? 1)->where('currency_id', $get('../../currency_id'));

        if (! $vendorPrices->isEmpty()) {
            $vendorPrice = $vendorPrices->first()->price;
        } else {
            $vendorPrice = $product->cost ?: $product->price;
        }

        if (! $get('uom_id') || ! $product->uom) {
            return $vendorPrice;
        }

        $uomQty = UOM::find($get('uom_id'))->computeQuantity(1, $product->uom, false);

        return (float) ($vendorPrice * $uomQty);
    }

    private static function getBestPackaging($productId, $quantity)
    {
        $packagings = Packaging::where('product_id', $productId)
            ->orderByDesc('qty')
            ->get();

        foreach ($packagings as $packaging) {
            if ($quantity && $quantity % $packaging->qty == 0) {
                return [
                    'packaging_id'  => $packaging->id,
                    'packaging_qty' => round($quantity / $packaging->qty, 2),
                ];
            }
        }

        return null;
    }

    private static function calculateLineTotals(Set $set, Get $get, ?string $prefix = ''): void
    {
        if (! $get($prefix.'product_id')) {
            $set($prefix.'price_unit', 0);

            $set($prefix.'discount', 0);

            $set($prefix.'price_tax', 0);

            $set($prefix.'price_subtotal', 0);

            $set($prefix.'price_total', 0);

            return;
        }

        $priceUnit = floatval($get($prefix.'price_unit'));

        $quantity = floatval($get($prefix.'product_qty') ?? 1);

        $discountValue = floatval($get($prefix.'discount') ?? 0);

        $discountedUnit = $discountValue > 0 ? $priceUnit * (1 - ($discountValue / 100)) : $priceUnit;

        $taxIds = $get($prefix.'taxes') ?? [];

        $taxes = Tax::whereIn('id', $taxIds)->get();

        if ($taxes->isEmpty()) {
            $subTotal = round($discountedUnit * $quantity, 4);

            $set($prefix.'price_subtotal', $subTotal);

            $set($prefix.'price_tax', 0);

            $set($prefix.'price_total', $subTotal);
        } else {
            $taxResult = TaxFacade::computeAll($taxes, $discountedUnit, null, $quantity);

            $set($prefix.'price_subtotal', round($taxResult['total_excluded'], 4));

            $set($prefix.'price_tax', round($taxResult['total_included'] - $taxResult['total_excluded'], 4));

            $set($prefix.'price_total', round($taxResult['total_included'], 4));
        }
    }

    private static function calculateOrderTotals(Get $get, $livewire): array
    {
        $defaultTotals = [
            'subtotal'         => 0,
            'totalTax'         => 0,
            'grandTotal'       => 0,
        ];

        $products = $get('products') ?? [];

        if (empty($products)) {
            $livewire->dispatch('itemUpdated', $defaultTotals);

            return $defaultTotals;
        }

        $subtotal = 0;
        $totalTax = 0;
        $grandTotal = 0;

        foreach ($products as $product) {
            if (empty($product['product_id'])) {
                continue;
            }

            $subtotal += floatval($product['price_subtotal'] ?? 0);
            $totalTax += floatval($product['price_tax'] ?? 0);
            $grandTotal += floatval($product['price_total'] ?? 0);
        }

        $totals = [
            'subtotal'         => round($subtotal, 2),
            'totalTax'         => round($totalTax, 2),
            'grandTotal'       => round($grandTotal, 2),
        ];

        $livewire->dispatch('itemUpdated', $totals);

        return $totals;
    }

    private static function handleVendorChange($state, Set $set, Get $get): void
    {
        if (! $state) {
            $set('requisition_id', null);

            return;
        }

        $vendor = Partner::find($state);

        $set('payment_term_id', $vendor->property_supplier_payment_term_id);

        $set('requisition_id', null);

        if (OrderResource::getOrderSettings()->enable_purchase_agreements) {
            $activeAgreement = static::getActiveAgreementForVendor($state, $get('company_id') ?? current_company_id());

            if ($activeAgreement) {
                $set('requisition_id', $activeAgreement->id);

                $products = static::mapRequisitionLinesToProducts($activeAgreement);

                $set('products', $products);

                $set('currency_id', $activeAgreement?->currency_id);

                foreach (array_keys($products) as $key) {
                    self::calculateLineTotals($set, $get, "products.$key.");
                }

                return;
            }
        }

        static::updateProductPricesForVendor($state, $set, $get);
    }

    private static function handleRequisitionChange($state, Set $set, Get $get): void
    {
        if (! $state) {
            return;
        }

        $requisition = Requisition::find($state);

        if (! $requisition) {
            return;
        }

        $products = static::mapRequisitionLinesToProducts($requisition);

        $set('products', $products);

        $set('currency_id', $requisition?->currency_id);

        foreach (array_keys($products) as $key) {
            self::calculateLineTotals($set, $get, "products.$key.");
        }
    }

    private static function getActiveAgreementForVendor(int $partnerId, ?int $companyId = null): ?Requisition
    {
        return Requisition::where('partner_id', $partnerId)
            ->where('company_id', $companyId ?? current_company_id())
            ->where('state', RequisitionState::CONFIRMED)
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->latest()
            ->first();
    }

    private static function mapRequisitionLinesToProducts(Requisition $requisition): array
    {
        $products = [];

        foreach ($requisition->lines as $line) {
            $product = $line->product;
            $uom = $line->uom;

            $products[] = [
                'product_id'        => $product?->id,
                'uom_id'            => $uom?->id,
                'product_qty'       => $line->qty,
                'price_unit'        => $line->price_unit,
                'planned_at'        => now(),
                'taxes'             => $product ? Tax::forProduct($product, TypeTaxUse::PURCHASE, $requisition->company_id) : [],
                'discount'          => 0,
                'from_requisition'  => true,
            ];
        }

        return $products;
    }

    private static function updateProductPricesForVendor($partnerId, Set $set, Get $get): void
    {
        $products = $get('products');

        if (! is_array($products)) {
            return;
        }

        foreach ($products as $key => $product) {
            if (! isset($product['product_id'])) {
                continue;
            }

            $productModel = Product::find($product['product_id']);

            if (! $productModel) {
                continue;
            }

            $vendorPrices = $productModel->sellers
                ->where('partner_id', $partnerId)
                ->where('currency_id', $get('currency_id'))
                ->where('min_qty', '<=', $product['product_qty'] ?? 1)
                ->sortByDesc('sort');

            if ($vendorPrices->isNotEmpty()) {
                $vendorPrice = $vendorPrices->first()->price;
            } else {
                $vendorPrice = $productModel->cost ?? $productModel->price;
            }

            $set("products.$key.price_unit", round($vendorPrice, 2));

            self::calculateLineTotals($set, $get, "products.$key.");
        }
    }

    private static function checkBlanketOrderQtyLimit(Get $get, ?string $prefix = ''): void
    {
        $requisitionId = $get('../../requisition_id');

        if (! $requisitionId) {
            return;
        }

        $requisition = Requisition::find($requisitionId);

        if (! $requisition || $requisition->type !== RequisitionType::BLANKET_ORDER) {
            return;
        }

        $productId = $get($prefix.'product_id');
        $productQty = floatval($get($prefix.'product_qty') ?? 0);

        if (! $productId || $productQty <= 0) {
            return;
        }

        $requisitionLine = $requisition->lines->where('product_id', $productId)->first();

        if (! $requisitionLine) {
            return;
        }

        $orderedQty = (float) OrderLine::query()
            ->where('product_id', $productId)
            ->whereHas('order', fn ($query) => $query
                ->where('requisition_id', $requisitionId)
                ->whereIn('state', [OrderState::PURCHASE->value, OrderState::DONE->value])
            )
            ->sum('product_qty');

        $availableQty = $requisitionLine->qty - $orderedQty;

        if ($productQty > $availableQty) {
            Notification::make()
                ->warning()
                ->title(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.notifications.blanket-order-qty-limit.title'))
                ->body(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.notifications.blanket-order-qty-limit.body', [
                    'product_qty'     => $productQty,
                    'available_qty'   => $availableQty,
                ]))
                ->send();
        }
    }

    private static function getInventoryOperationTypeId(?int $companyId): ?int
    {
        if (! $companyId || ! static::canUseInventoryWarehouses()) {
            return null;
        }

        $operationType = OperationType::where('type', InventoryEnums\OperationType::INCOMING)
            ->whereHas('warehouse', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->first();

        if (! $operationType) {
            $operationType = OperationType::where('type', InventoryEnums\OperationType::INCOMING)
                ->whereDoesntHave('warehouse')
                ->first();
        }

        return $operationType?->id;
    }

    private static function canUseInventoryWarehouses(): bool
    {
        return Package::isPluginInstalled('inventories');
    }
}
