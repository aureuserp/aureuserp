<?php

namespace Webkul\Account\Filament\Resources\BillResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
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
use Illuminate\Support\Arr;
use Webkul\Account\Enums\CommunicationStandard;
use Webkul\Account\Enums\CommunicationType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Filament\Resources\BankAccountResource;
use Webkul\Account\Filament\Resources\JournalResource;
use Webkul\Account\Models\Bill;
use Webkul\Account\Models\CashRounding;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Product;
use Webkul\Account\Models\Tax;
use Webkul\Account\Settings\CustomerInvoiceSettings;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class BillForm
{
    public static function configure(Schema $schema, string $resource, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = MoveState::options();

                        if ($record?->state !== MoveState::CANCEL) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        return $options;
                    })
                    ->default(MoveState::DRAFT->value)
                    ->columnSpan('full')
                    ->disabled()
                    ->live()
                    ->reactive(),

                Section::make(__('accounts::filament/resources/bill.form.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Actions::make([
                            Action::make('payment_state')
                                ->icon(fn ($record) => $record->payment_state->getIcon())
                                ->color(fn ($record) => $record->payment_state->getColor())
                                ->visible(fn ($record) => in_array($record?->payment_state, [PaymentState::PAID, PaymentState::REVERSED]))
                                ->label(fn ($record) => $record->payment_state->getLabel())
                                ->size(Size::ExtraLarge->value),
                        ]),

                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Select::make('partner_id')
                                            ->label(__('accounts::filament/resources/bill.form.section.general.fields.vendor'))
                                            ->relationship(
                                                'partner',
                                                'name',
                                                fn (Builder $query) => $query->orderBy('id')->withTrashed(),
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $partner = $state ? Partner::find($state) : null;

                                                $set('partner_bank_id', $partner?->bankAccounts->first()?->id);

                                                $set('preferred_payment_method_line_id', $partner?->property_outbound_payment_method_line_id);

                                                $set('invoice_payment_term_id', $partner?->property_supplier_payment_term_id);
                                            })
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        TextInput::make('reference')
                                            ->label(__('accounts::filament/resources/bill.form.section.general.fields.bill-reference'))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ]),

                                Group::make()
                                    ->schema([
                                        DatePicker::make('invoice_date')
                                            ->label(__('accounts::filament/resources/bill.form.section.general.fields.bill-date'))
                                            ->native(false)
                                            ->required()
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        DatePicker::make('date')
                                            ->label(__('accounts::filament/resources/bill.form.section.general.fields.accounting-date'))
                                            ->default(now())
                                            ->native(false)
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        TextInput::make('payment_reference')
                                            ->label(__('accounts::filament/resources/bill.form.section.general.fields.payment-reference'))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        Select::make('partner_bank_id')
                                            ->relationship(
                                                'partnerBank',
                                                'account_number',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('partner_id', $get('partner_id'))->withTrashed(),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->account_number.' - '.$record->bank->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/bill.form.section.general.fields.recipient-bank'))
                                            ->createOptionForm(fn (Schema $schema, Get $get) => BankAccountResource::form($schema)->fill([
                                                'partner_id' => $get('partner_id'),
                                            ]))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),

                                        Group::make()
                                            ->schema([
                                                DatePicker::make('invoice_date_due')
                                                    ->required()
                                                    ->default(now())
                                                    ->native(false)
                                                    ->live()
                                                    ->hidden(fn (Get $get) => $get('invoice_payment_term_id') !== null)
                                                    ->label(__('accounts::filament/resources/bill.form.section.general.fields.due-date')),
                                                Select::make('invoice_payment_term_id')
                                                    ->relationship(
                                                        'invoicePaymentTerm',
                                                        'name',
                                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                                    )
                                                    ->required(fn (Get $get) => $get('invoice_date_due') === null)
                                                    ->live()
                                                    ->searchable()
                                                    ->preload()
                                                    ->label(__('accounts::filament/resources/bill.form.section.general.fields.payment-term'))
                                                    ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                            ])
                                            ->columns(2),

                                        Group::make()
                                            ->schema([
                                                Select::make('journal_id')
                                                    ->relationship(
                                                        'journal',
                                                        'name',
                                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                                            ->where('type', JournalType::PURCHASE)
                                                            ->where(owned_by_company($get('company_id'))),
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->label(__('accounts::filament/resources/bill.form.section.general.fields.journal'))
                                                    ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
                                                    ->createOptionForm(function ($form) {
                                                        $schema = JournalResource::form($form);

                                                        $components = $schema->getComponents();

                                                        foreach ($components as $component) {
                                                            static::disableTypeField($component);
                                                        }

                                                        return $schema;
                                                    })
                                                    ->createOptionAction(
                                                        fn (Action $action, Get $get) => $action
                                                            ->fillForm(fn () => [
                                                                'type'                     => JournalType::PURCHASE,
                                                                'invoice_reference_type'   => CommunicationType::INVOICE,
                                                                'invoice_reference_model'  => CommunicationStandard::AUREUS,
                                                                'company_id'               => $get('company_id') ?? current_company_id(),
                                                            ])
                                                    )
                                                    ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),

                                                Select::make('currency_id')
                                                    ->label(__('accounts::filament/resources/bill.form.section.general.fields.currency'))
                                                    ->relationship(
                                                        name: 'currency',
                                                        titleAttribute: 'name',
                                                        modifyQueryUsing: fn (Builder $query) => $query->active(),
                                                    )
                                                    ->required()
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->reactive()
                                                    ->default(current_company()?->currency_id)
                                                    ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                            ])
                                            ->columns(2),
                                    ]),
                            ])
                            ->columns(2),
                    ]),

                Tabs::make()
                    ->schema([
                        Tab::make(__('accounts::filament/resources/bill.form.tabs.invoice-lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                $resource::getProductRepeater(),

                                Livewire::make($resource::getSummaryComponent(), function (Get $get, $record, $livewire) {
                                    $totals = static::calculateMoveTotals($get, $livewire);

                                    $currency = Currency::find($get('currency_id'));

                                    return [
                                        'record'     => $record,
                                        'rounding'   => $totals['rounding'],
                                        'amountTax'  => $totals['totalTax'],
                                        'subtotal'   => $totals['subtotal'],
                                        'totalTax'   => $totals['totalTax'],
                                        'grandTotal' => $totals['grandTotal'] + $totals['rounding'],
                                        'currency'   => $currency,
                                    ];
                                })
                                    ->key('invoiceSummary')
                                    ->reactive()
                                    ->visible(fn (Get $get) => $get('currency_id') && ! empty($get('products'))),
                            ]),

                        Tab::make(__('accounts::filament/resources/bill.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Fieldset::make(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.title'))
                                    ->schema([
                                        Select::make('company_id')
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                $company = $get('company_id') ? Company::find($get('company_id')) : null;

                                                if ($company) {
                                                    $set('currency_id', $company->currency_id);
                                                }

                                                $set('journal_id', Journal::query()
                                                    ->where('type', JournalType::PURCHASE)
                                                    ->where('company_id', $company?->id)
                                                    ->value('id'));

                                                clear_foreign_company_values($set, $get, [
                                                    'fiscal_position_id' => FiscalPosition::class,
                                                ], $company?->id);
                                            })
                                            ->default(current_company_id()),
                                        Select::make('invoice_incoterm_id')
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.incoterm'))
                                            ->relationship('invoiceIncoterm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(fn (CustomerInvoiceSettings $settings) => $settings->incoterm_id),
                                        TextInput::make('incoterm_location')
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.incoterm-location')),
                                        Select::make('preferred_payment_method_line_id')
                                            ->relationship(
                                                name: 'paymentMethodLine',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn ($query) => $query->whereHas('paymentMethod', fn ($q) => $q->where('payment_type', PaymentType::SEND)),
                                            )
                                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name)
                                            ->preload()
                                            ->searchable()
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.payment-method')),
                                        Select::make('fiscal_position_id')
                                            ->relationship(
                                                'fiscalPosition',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                            )
                                            ->preload()
                                            ->searchable()
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.fiscal-position'))
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.fiscal-position-tooltip'))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        Select::make('invoice_cash_rounding_id')
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.cash-rounding'))
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.cash-rounding-tooltip'))
                                            ->relationship('invoiceCashRounding', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->reactive()
                                            ->live()
                                            ->nullable()
                                            ->visible(fn (CustomerInvoiceSettings $settings) => (bool) $settings->group_cash_rounding)
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        Toggle::make('checked')
                                            ->inline(false)
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.checked')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columns(2),

                        Tab::make(__('accounts::filament/resources/bill.form.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                RichEditor::make('narration')
                                    ->hiddenLabel(),
                            ]),
                    ]),

                Section::make()
                    ->schema($customFormFields)
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function getProductRepeater(): Repeater
    {
        return Repeater::make('products')
            ->relationship('invoiceLines')
            ->hiddenLabel()
            ->live()
            ->reactive()
            ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.title'))
            ->addActionLabel(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.add-product'))
            ->collapsible()
            ->compact()
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
            ->deletable(fn ($record): bool => ! in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
            ->addable(fn ($record): bool => ! in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
            ->table([
                TableColumn::make('product_id')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.product'))
                    ->width(300)
                    ->resizable()
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('quantity')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.quantity'))
                    ->resizable()
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('uom_id')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.unit'))
                    ->resizable()
                    ->markAsRequired()
                    ->visible(fn () => settings(ProductSettings::class)->enable_uom)
                    ->toggleable(),
                TableColumn::make('price_unit')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.unit-price'))
                    ->resizable()
                    ->markAsRequired(),
                TableColumn::make('discount')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.discount-percentage'))
                    ->resizable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('taxes')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.taxes'))
                    ->resizable()
                    ->toggleable(),
                TableColumn::make('price_subtotal')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.sub-total'))
                    ->resizable()
                    ->toggleable(),
            ])
            ->schema([
                Select::make('product_id')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.product'))
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->withTrashed()
                            ->whereNull('is_configurable')
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->wrapOptionLabels(false)
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
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
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductUpdated($set, $get))
                    ->required(),
                TextInput::make('quantity')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.quantity'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductQtyUpdated($set, $get)),
                Select::make('uom_id')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get) {
                            $product = Product::find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))->orderBy('id');
                        },
                    )
                    ->wrapOptionLabels(false)
                    ->required()
                    ->live()
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterUOMUpdated($set, $get))
                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom),
                TextInput::make('price_unit')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.unit-price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get)),
                TextInput::make('discount')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.discount-percentage'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get)),
                Select::make('taxes')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.taxes'))
                    ->relationship(
                        'taxes',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->where('type_tax_use', TypeTaxUse::PURCHASE)
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->wrapOptionLabels(false)
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateHydrated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                    ->live(),
                TextInput::make('price_subtotal')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.sub-total'))
                    ->default(0)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                Hidden::make('product_uom_qty')
                    ->default(0),
                Hidden::make('price_tax')
                    ->default(0),
                Hidden::make('price_total')
                    ->default(0),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, $record) => static::mutateProductRelationship($data, $record))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, $record) => static::mutateProductRelationship($data, $record));
    }

    private static function mutateProductRelationship(array $data, $record): array
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

        $priceUnit = static::calculateUnitPrice($product->uom_id, $product);

        if ($get('../../currency_id')) {
            $currency = Currency::find($get('../../currency_id'));

            $company = Company::find($get('../../company_id')) ?? current_company();

            $priceUnit = $company->currency->convert(
                $priceUnit,
                $currency,
                $company
            );
        }

        $set('price_unit', round($priceUnit, 2));

        $set('taxes', Tax::forProduct($product, TypeTaxUse::PURCHASE, $get('../../company_id')));

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

        $priceUnit = static::calculateUnitPrice($get('uom_id'), $product);

        $set('price_unit', round($priceUnit, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function calculateUnitQuantity($uomId, $quantity)
    {
        if (! $uomId || ! filled($quantity)) {
            return (float) ($quantity ?? 0);
        }

        $fromUom = UOM::find($uomId);

        if (! $fromUom) {
            return (float) ($quantity ?? 0);
        }

        $referenceUom = UOM::where('category_id', $fromUom->category_id)->orderBy('factor')->first();

        if (! $referenceUom) {
            return (float) ($quantity ?? 0);
        }

        return $fromUom->computeQuantity((float) ($quantity ?? 0), $referenceUom, false);
    }

    private static function calculateUnitPrice($uomId, $product)
    {
        $price = $product->price ?? $product->cost;

        if (! $uomId || ! $product->uom) {
            return $price;
        }

        $uomQty = UOM::find($uomId)->computeQuantity(1, $product->uom, false);

        return (float) ($price * $uomQty);
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

        $currencyId = $get('../../currency_id');
        $companyId = $get('../../company_id');
        $productId = $get('product_id');

        if (! $currencyId || ! $companyId || ! $productId) {
            return;
        }

        $currency = Currency::find($currencyId);
        $company = Company::find($companyId);
        $product = Product::find($productId);

        if (! $currency || ! $company || ! $product) {
            return;
        }

        $mockLine = new MoveLine([
            'quantity'     => $get('quantity') ?? 1,
            'price_unit'   => $get('price_unit') ?? 0,
            'discount'     => $get('discount') ?? 0,
            'display_type' => DisplayType::PRODUCT,
        ]);

        $mockMove = new Bill([
            'move_type'   => $get('../../move_type'),
            'currency_id' => $currencyId,
            'company_id'  => $companyId,
        ]);

        $taxIds = $get('taxes') ?? [];
        $mockLine->setRelation('taxes', Tax::whereIn('id', $taxIds)->get());
        $mockLine->setRelation('currency', $currency);
        $mockLine->setRelation('company', $company);
        $mockLine->setRelation('product', $product);
        $mockLine->setRelation('move', $mockMove);

        $mockMove->setRelation('currency', $currency);
        $mockMove->setRelation('company', $company);

        $baseLine = AccountFacade::productBaseLine($mockLine);

        $baseLine = TaxFacade::withTaxDetails($baseLine, $company);

        $subtotal = $baseLine['tax_details']['raw_total_excluded_currency'];
        $total = $baseLine['tax_details']['raw_total_included_currency'];
        $tax = $total - $subtotal;

        $set('price_subtotal', round($subtotal, 4));
        $set('price_tax', round($tax, 4));
        $set('price_total', round($total, 4));
    }

    private static function calculateMoveTotals(Get $get, $livewire): array
    {
        $defaultTotals = [
            'subtotal'   => 0,
            'totalTax'   => 0,
            'grandTotal' => 0,
            'rounding'   => 0,
        ];

        $currencyId = $get('currency_id');
        $companyId = $get('company_id');
        $products = $get('products') ?? [];

        if (! $currencyId || ! $companyId || empty($products)) {
            $livewire->dispatch('itemUpdated', $defaultTotals);

            return $defaultTotals;
        }

        $currency = Currency::find($currencyId);
        $company = Company::find($companyId);

        if (! $currency || ! $company) {
            $livewire->dispatch('itemUpdated', $defaultTotals);

            return $defaultTotals;
        }

        $cashRoundingId = $get('invoice_cash_rounding_id');

        $mockMove = new Bill([
            'move_type'                => $get('move_type'),
            'currency_id'              => $currency->id,
            'company_id'               => $company->id,
            'invoice_cash_rounding_id' => $cashRoundingId,
        ]);

        $mockMove->setRelation('currency', $currency);
        $mockMove->setRelation('company', $company);

        if ($cashRoundingId) {
            $cashRounding = CashRounding::find($cashRoundingId);

            if ($cashRounding) {
                $mockMove->setRelation('invoiceCashRounding', $cashRounding);
            }
        }

        $mockLines = collect($products)
            ->filter(fn ($productData) => ! empty($productData['product_id']))
            ->map(function ($productData) use ($currency, $company, $mockMove) {
                $product = Product::find($productData['product_id']);

                if (! $product) {
                    return null;
                }

                $mockLine = new MoveLine([
                    'quantity'     => $productData['quantity'] ?? 1,
                    'price_unit'   => $productData['price_unit'] ?? 0,
                    'discount'     => $productData['discount'] ?? 0,
                    'display_type' => DisplayType::PRODUCT,
                ]);

                $mockLine->setRelation('taxes', Tax::whereIn('id', $productData['taxes'] ?? [])->get());
                $mockLine->setRelation('currency', $currency);
                $mockLine->setRelation('company', $company);
                $mockLine->setRelation('product', $product);
                $mockLine->setRelation('move', $mockMove);

                return $mockLine;
            })
            ->filter();

        if ($mockLines->isEmpty()) {
            $livewire->dispatch('itemUpdated', $defaultTotals);

            return $defaultTotals;
        }

        $mockMove->setRelation('lines', $mockLines);

        [$baseLines] = AccountFacade::roundedBaseAndTaxLines($mockMove, false);

        $subtotal = 0;
        $grandTotal = 0;
        $rounding = 0;

        foreach ($baseLines as $baseLine) {
            $specialType = $baseLine['special_type'] ?? null;

            if ($specialType === 'cash_rounding') {
                $rounding = $baseLine['tax_details']['raw_total_excluded_currency'];
            } else {
                $subtotal += $baseLine['tax_details']['raw_total_excluded_currency'] ?? 0;
                $grandTotal += $baseLine['tax_details']['raw_total_included_currency'] ?? 0;
            }
        }

        if ($rounding == 0 && $cashRoundingId) {
            $cashRounding = CashRounding::find($cashRoundingId);

            if ($cashRounding) {
                $rounding = $cashRounding->computeDifference($currency, $grandTotal);
            }
        }

        $defaultTotals = [
            'subtotal'   => round($subtotal, 2),
            'totalTax'   => round($grandTotal - $subtotal, 2),
            'grandTotal' => round($grandTotal, 2),
            'rounding'   => round($rounding, 2),
        ];

        $livewire->dispatch('itemUpdated', $defaultTotals);

        return $defaultTotals;
    }

    private static function disableTypeField($component): void
    {
        if (method_exists($component, 'getChildComponents')) {
            foreach ($component->getChildComponents() as $child) {
                static::disableTypeField($child);
            }
        }

        if (method_exists($component, 'getName') && $component->getName() === 'type') {
            $component->disabled()->dehydrated();
        }
    }
}
