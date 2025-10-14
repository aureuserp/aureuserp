<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
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
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Tax;
use Webkul\Account\Filament\Resources\BillResource\Pages\CreateBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\ListBills;
use Webkul\Account\Filament\Resources\BillResource\Pages\ViewBill;
use Webkul\Account\Livewire\InvoiceSummary;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\InvoiceResource;
use Webkul\Invoice\Models\Product;
use Webkul\Invoice\Settings\ProductSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class BillResource extends Resource
{
    protected static ?string $model = AccountMove::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/bill.global-search.number')           => $record?->name ?? '—',
            __('accounts::filament/resources/bill.global-search.customer')         => $record?->invoice_partner_display_name ?? '—',
            __('accounts::filament/resources/bill.global-search.invoice-date')     => $record?->invoice_date ?? '—',
            __('accounts::filament/resources/bill.global-search.invoice-date-due') => $record?->invoice_date_due ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
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
                Section::make(__('accounts::filament/resources/bill.form.section.general.title'))
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
                                            ->label(__('accounts::filament/resources/bill.form.section.general.fields.vendor'))
                                            ->relationship(
                                                'partner',
                                                'name',
                                                fn ($query) => $query->where('sub_type', 'supplier')->orderBy('id'),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ]),
                                DatePicker::make('invoice_date')
                                    ->label(__('accounts::filament/resources/bill.form.section.general.fields.bill-date'))
                                    ->default(now())
                                    ->native(false)
                                    ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                TextInput::make('reference')
                                    ->label(__('accounts::filament/resources/bill.form.section.general.fields.bill-reference'))
                                    ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                DatePicker::make('date')
                                    ->label(__('accounts::filament/resources/bill.form.section.general.fields.accounting-date'))
                                    ->default(now())
                                    ->native(false)
                                    ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                TextInput::make('payment_reference')
                                    ->label(__('accounts::filament/resources/bill.form.section.general.fields.payment-reference'))
                                    ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                Select::make('partner_bank_id')
                                    ->relationship(
                                        'partnerBank',
                                        'account_number',
                                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->account_number.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/bill.form.section.general.fields.recipient-bank'))
                                    ->createOptionForm(fn ($form) => BankAccountResource::form($form))
                                    ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                DatePicker::make('invoice_date_due')
                                    ->required()
                                    ->default(now())
                                    ->native(false)
                                    ->live()
                                    ->hidden(fn (Get $get) => $get('invoice_payment_term_id') !== null)
                                    ->label(__('accounts::filament/resources/bill.form.section.general.fields.due-date')),
                                Select::make('invoice_payment_term_id')
                                    ->relationship('invoicePaymentTerm', 'name')
                                    ->required(fn (Get $get) => $get('invoice_date_due') === null)
                                    ->live()
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/bill.form.section.general.fields.payment-term')),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->schema([
                        Tab::make(__('accounts::filament/resources/bill.form.tabs.invoice-lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                static::getProductRepeater(),
                                Livewire::make(InvoiceSummary::class, function (Get $get) {
                                    return [
                                        'currency' => Currency::find($get('currency_id')),
                                        'products' => $get('products'),
                                    ];
                                })
                                    ->live()
                                    ->reactive(),
                            ]),
                        Tab::make(__('accounts::filament/resources/bill.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Fieldset::make(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.title'))
                                    ->schema([
                                        Select::make('invoice_incoterm_id')
                                            ->relationship('invoiceIncoterm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.incoterm')),
                                        TextInput::make('incoterm_location')
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.incoterm-location')),
                                    ]),
                                Fieldset::make(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.secured.title'))
                                    ->schema([
                                        Select::make('preferred_payment_method_line_id')
                                            ->relationship('paymentMethodLine', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.secured.fields.payment-method')),
                                        Toggle::make('auto_post')
                                            ->default(0)
                                            ->inline(false)
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.secured.fields.auto-post'))
                                            ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        Toggle::make('checked')
                                            ->inline(false)
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.secured.fields.checked')),
                                    ]),
                                Fieldset::make(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.additional-information.title'))
                                    ->schema([
                                        Select::make('company_id')
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.additional-information.fields.company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                $company = $get('company_id') ? \Webkul\Support\Models\Company::find($get('company_id')) : null;

                                                if ($company) {
                                                    $set('currency_id', $company->currency_id);
                                                }
                                            })
                                            ->default(Auth::user()->default_company_id),
                                        Select::make('currency_id')
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.additional-information.fields.currency'))
                                            ->relationship('currency', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->reactive()
                                            ->default(Auth::user()->defaultCompany?->currency_id),
                                    ]),
                            ]),
                        Tab::make(__('accounts::filament/resources/bill.form.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                RichEditor::make('narration')
                                    ->hiddenLabel(),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return InvoiceResource::table($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('payment_state')
                            ->badge(),
                    ])
                    ->compact(),
                Section::make(__('accounts::filament/resources/bill.infolist.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.vendor-invoice'))
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->size(TextSize::Large),
                            ])->columns(2),
                        Grid::make()
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.vendor'))
                                    ->visible(fn ($record) => $record->partner_id !== null)
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('invoice_partner_display_name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.vendor'))
                                    ->visible(fn ($record) => $record->partner_id === null)
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('invoice_date')
                                    ->date()
                                    ->icon('heroicon-o-calendar')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.bill-date')),
                                TextEntry::make('reference')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.bill-reference')),
                                TextEntry::make('date')
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.accounting-date')),
                                TextEntry::make('payment_reference')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.payment-reference')),
                                TextEntry::make('partnerBank.account_number')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.recipient-bank')),
                                TextEntry::make('invoice_date_due')
                                    ->icon('heroicon-o-clock')
                                    ->placeholder('-')
                                    ->date()
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.due-date')),
                                TextEntry::make('invoicePaymentTerm.name')
                                    ->placeholder('-')
                                    ->icon('heroicon-o-calendar-days')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.payment-term')),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.product'))
                                            ->icon('heroicon-o-cube'),
                                        TextEntry::make('quantity')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.quantity'))
                                            ->icon('heroicon-o-hashtag'),
                                        TextEntry::make('uom.name')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.unit'))
                                            ->icon('heroicon-o-scale'),
                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.unit-price'))
                                            ->icon('heroicon-o-currency-dollar')
                                            ->money(fn ($record) => $record->currency->name),
                                        TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.discount-percentage'))
                                            ->icon('heroicon-o-tag')
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
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.taxes'))
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.sub-total'))
                                            ->icon('heroicon-o-calculator')
                                            ->money(fn ($record) => $record->currency->name),
                                    ])->columns(5),
                                Livewire::make(InvoiceSummary::class, function ($record) {
                                    return [
                                        'currency'  => $record->currency,
                                        'amountTax' => $record->amount_tax ?? 0,
                                        'products'  => $record->lines->map(function ($item) {
                                            return [
                                                ...$item->toArray(),
                                                'taxes' => $item->taxes->pluck('id')->toArray() ?? [],
                                            ];
                                        })->toArray(),
                                    ];
                                }),
                            ]),
                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.title'))
                                    ->icon('heroicon-o-calculator')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('invoiceIncoterm.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.incoterm'))
                                                    ->icon('heroicon-o-globe-alt'),
                                                TextEntry::make('incoterm_location')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.incoterm-location'))
                                                    ->icon('heroicon-o-map-pin'),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.secured.title'))
                                    ->icon('heroicon-o-shield-check')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('paymentMethodLine.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.secured.entries.payment-method'))
                                                    ->icon('heroicon-o-credit-card'),
                                                IconEntry::make('auto_post')
                                                    ->boolean()
                                                    ->placeholder('-')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.secured.entries.auto-post')),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.additional-information.title'))
                                    ->icon('heroicon-o-puzzle-piece')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('company.name')
                                                    ->placeholder('-')
                                                    ->icon('heroicon-o-building-office')
                                                    ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.additional-information.entries.company')),
                                                TextEntry::make('currency.name')
                                                    ->placeholder('-')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.additional-information.entries.currency')),
                                            ])->columns(2),
                                    ]),
                            ]),
                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                TextEntry::make('narration')
                                    ->html()
                                    ->hiddenLabel(),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBills::route('/'),
            'create' => CreateBill::route('/create'),
            'edit'   => EditBill::route('/{record}/edit'),
            'view'   => ViewBill::route('/{record}'),
        ];
    }

    public static function getProductRepeater(): Repeater
    {
        return Repeater::make('products')
            ->relationship('lines')
            ->hiddenLabel()
            ->live()
            ->reactive()
            ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.title'))
            ->addActionLabel(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.add-product'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
            ->table([
                TableColumn::make('product_id')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.product'))
                    ->width(250)
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('quantity')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.quantity'))
                    ->width(150)
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('uom_id')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.unit'))
                    ->width(150)
                    ->markAsRequired()
                    ->visible(fn () => resolve(ProductSettings::class)->enable_uom)
                    ->toggleable(),
                TableColumn::make('taxes')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.taxes'))
                    ->width(250)
                    ->toggleable(),
                TableColumn::make('discount')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.discount-percentage'))
                    ->width(150)
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('price_unit')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.unit-price'))
                    ->width(150)
                    ->markAsRequired(),
                TableColumn::make('price_subtotal')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.sub-total'))
                    ->width(150)
                    ->toggleable(),
            ])
            ->schema([
                Select::make('product_id')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.product'))
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->withTrashed()
                            ->whereNull('is_configurable'),
                    )
                    ->searchable()
                    ->preload()
                    ->live()
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
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
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
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductQtyUpdated($set, $get)),
                Select::make('uom_id')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.unit'))
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
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.taxes'))
                    ->relationship(
                        'taxes',
                        'name',
                        function (Builder $query) {
                            return $query->where('type_tax_use', TypeTaxUse::PURCHASE->value);
                        },
                    )
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->dehydrated()
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateHydrated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                    ->live(),
                TextInput::make('discount')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.discount-percentage'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get)),
                TextInput::make('price_unit')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.unit-price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get)),
                TextInput::make('price_subtotal')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.sub-total'))
                    ->default(0)
                    ->dehydrated()
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
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

        $priceUnit = static::calculateUnitPrice($get('uom_id'), $product->cost ?: $product->price);

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

        $priceUnit = static::calculateUnitPrice($get('uom_id'), $product->cost ?: $product->price);

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
