<?php

namespace Webkul\Account\Filament\Resources\Invoices\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Tax;
use Webkul\Account\Filament\Resources\BankAccounts\BankAccountResource;
use Webkul\Account\Livewire\InvoiceSummary;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Invoice\Models\Product;
use Webkul\Invoice\Settings\ProductSettings;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = MoveState::options();

                        if (
                            $record
                            && $record->state != MoveState::CANCEL->value
                        ) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        if ($record == null) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        return $options;
                    })
                    ->default(MoveState::DRAFT->value)
                    ->columnSpan('full')
                    ->disabled()
                    ->live()
                    ->reactive(),
                Section::make(__('accounts::filament/resources/invoice.form.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Actions::make([
                            Action::make('payment_state')
                                ->icon(fn ($record) => $record->payment_state->getIcon())
                                ->color(fn ($record) => $record->payment_state->getColor())
                                ->visible(fn ($record) => $record && in_array($record->payment_state, [PaymentState::PAID, PaymentState::REVERSED]))
                                ->label(fn ($record) => $record->payment_state->getLabel())
                                ->size(Size::ExtraLarge->value),
                        ]),
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Select::make('partner_id')
                                            ->label(__('accounts::filament/resources/invoice.form.section.general.fields.customer'))
                                            ->relationship(
                                                'partner',
                                                'name',
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ]),
                                DatePicker::make('invoice_date')
                                    ->label(__('accounts::filament/resources/invoice.form.section.general.fields.invoice-date'))
                                    ->default(now())
                                    ->native(false)
                                    ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                DatePicker::make('invoice_date_due')
                                    ->required()
                                    ->default(now())
                                    ->native(false)
                                    ->live()
                                    ->hidden(fn (Get $get) => $get('invoice_payment_term_id') !== null)
                                    ->label(__('accounts::filament/resources/invoice.form.section.general.fields.due-date')),
                                Select::make('invoice_payment_term_id')
                                    ->relationship(
                                        'invoicePaymentTerm',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->required(fn (Get $get) => $get('invoice_date_due') === null)
                                    ->live()
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/invoice.form.section.general.fields.payment-term')),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->schema([
                        Tab::make(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                self::getProductRepeater(),
                                Livewire::make(InvoiceSummary::class, function (Get $get) {
                                    return [
                                        'currency' => Currency::find($get('currency_id')),
                                        'products' => $get('products'),
                                    ];
                                })
                                    ->live()
                                    ->reactive(),
                            ]),
                        Tab::make(__('accounts::filament/resources/invoice.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Fieldset::make(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.title'))
                                    ->schema([
                                        TextInput::make('reference')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.customer-reference'))
                                            ->maxLength(255),
                                        Select::make('invoice_user_id')
                                            ->relationship('invoiceUser', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.sales-person')),
                                        Select::make('partner_bank_id')
                                            ->relationship('partnerBank', 'account_number')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.recipient-bank'))
                                            ->createOptionForm(fn ($form) => BankAccountResource::form($form))
                                            ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        TextInput::make('payment_reference')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.payment-reference')),
                                        DatePicker::make('delivery_date')
                                            ->native(false)
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.delivery-date'))
                                            ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ]),
                                Fieldset::make(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.title'))
                                    ->schema([
                                        Select::make('invoice_incoterm_id')
                                            ->relationship('invoiceIncoterm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fieldset.incoterm')),
                                        TextInput::make('incoterm_location')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fieldset.incoterm-location')),
                                        Select::make('preferred_payment_method_line_id')
                                            ->relationship('paymentMethodLine', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fieldset.payment-method')),
                                        Toggle::make('auto_post')
                                            ->default(0)
                                            ->inline(false)
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fieldset.auto-post'))
                                            ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        Toggle::make('checked')
                                            ->inline(false)
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fieldset.checked')),
                                    ]),
                                Fieldset::make(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.additional-information.title'))
                                    ->schema([
                                        Select::make('company_id')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.additional-information.fields.company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(Auth::user()->default_company_id),
                                        Select::make('currency_id')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.additional-information.fields.currency'))
                                            ->relationship('currency', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->reactive()
                                            ->default(Auth::user()->defaultCompany?->currency_id),
                                    ]),
                                Fieldset::make(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.marketing.title'))
                                    ->schema([
                                        Select::make('campaign_id')
                                            ->relationship('campaign', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.marketing.fields.campaign')),
                                        Select::make('medium_id')
                                            ->relationship('medium', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.marketing.fields.medium')),
                                        Select::make('source_id')
                                            ->relationship('source', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.marketing.fields.source')),
                                    ]),
                            ]),
                        Tab::make(__('accounts::filament/resources/invoice.form.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                RichEditor::make('narration')
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
            ->reactive()
            ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.title'))
            ->addActionLabel(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.add-product'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(function ($state) {
                if (! empty($state['name'])) {
                    return $state['name'];
                }

                $product = Product::find($state['product_id']);

                return $product->name ?? null;
            })
            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
            ->schema([
                Group::make()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.product'))
                                    ->relationship(
                                        'product',
                                        'name',
                                        fn (Builder $query) => $query->where('is_configurable', null),
                                    )
                                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                                        if ($record->product) {
                                            return $record->product->name;
                                        }

                                        return $record->name;
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->dehydrated()
                                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductUpdated($set, $get))
                                    ->required(),
                                TextInput::make('quantity')
                                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.quantity'))
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->dehydrated()
                                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductQtyUpdated($set, $get)),
                                Select::make('uom_id')
                                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.unit'))
                                    ->relationship(
                                        'uom',
                                        'name',
                                        fn ($query) => $query->where('category_id', 1)->orderBy('id'),
                                    )
                                    ->required()
                                    ->live()
                                    ->selectablePlaceholder(false)
                                    ->dehydrated()
                                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterUOMUpdated($set, $get))
                                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom),
                                Select::make('taxes')
                                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.taxes'))
                                    ->relationship(
                                        'taxes',
                                        'name',
                                        function (Builder $query) {
                                            return $query->where('type_tax_use', TypeTaxUse::SALE->value);
                                        },
                                    )
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->dehydrated()
                                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                                    ->afterStateHydrated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                                    ->afterStateUpdated(fn (Get $get, Set $set, $state) => self::calculateLineTotals($set, $get))
                                    ->live(),
                                TextInput::make('discount')
                                    ->label(__('Discount Percentage'))
                                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.discount-percentage'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->dehydrated()
                                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get)),
                                TextInput::make('price_unit')
                                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.unit-price'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required()
                                    ->live()
                                    ->dehydrated()
                                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get)),
                                TextInput::make('price_subtotal')
                                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.sub-total'))
                                    ->default(0)
                                    ->dehydrated()
                                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                                Hidden::make('product_uom_qty')
                                    ->default(0),
                                Hidden::make('price_tax')
                                    ->default(0),
                                Hidden::make('price_total')
                                    ->default(0),
                            ]),
                    ])
                    ->columns(1),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, $record) => static::mutateProductRelationship($data, $record))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, $record) => static::mutateProductRelationship($data, $record));
    }

    public static function mutateProductRelationship(array $data, $record): array
    {
        $data['currency_id'] = $record->currency_id;

        return $data;
    }

    private static function afterProductUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $set('uom_id', $product->uom_id);

        $priceUnit = static::calculateUnitPrice($get('uom_id'), $product->price ?? $product->cost);

        $set('price_unit', round($priceUnit, 2));

        $set('taxes', $product->productTaxes->pluck('id')->toArray());

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('quantity'));

        $set('product_uom_qty', round($uomQuantity, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductQtyUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('quantity'));

        $set('product_uom_qty', round($uomQuantity, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function afterUOMUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('quantity'));

        $set('product_uom_qty', round($uomQuantity, 2));

        $product = Product::find($get('product_id'));

        $priceUnit = static::calculateUnitPrice($get('uom_id'), $product->cost ?? $product->price);

        $set('price_unit', round($priceUnit, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function calculateUnitQuantity($uomId, $quantity)
    {
        if (! $uomId) {
            return $quantity;
        }

        $uom = Uom::find($uomId);

        return (float) ($quantity ?? 0) / $uom->factor;
    }

    private static function calculateUnitPrice($uomId, $price)
    {
        if (! $uomId) {
            return $price;
        }

        $uom = Uom::find($uomId);

        return (float) ($price / $uom->factor);
    }

    private static function calculateLineTotals(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            $set('price_unit', 0);

            $set('discount', 0);

            $set('price_tax', 0);

            $set('price_subtotal', 0);

            $set('price_total', 0);

            return;
        }

        $priceUnit = floatval($get('price_unit'));

        $quantity = floatval($get('quantity') ?? 1);

        $subTotal = $priceUnit * $quantity;

        $discountValue = floatval($get('discount') ?? 0);

        if ($discountValue > 0) {
            $discountAmount = $subTotal * ($discountValue / 100);

            $subTotal = $subTotal - $discountAmount;
        }

        $taxIds = $get('taxes') ?? [];

        [$subTotal, $taxAmount] = Tax::collect($taxIds, $subTotal, $quantity);

        $set('price_subtotal', round($subTotal, 4));

        $set('price_tax', $taxAmount);

        $set('price_total', $subTotal + $taxAmount);
    }
}
