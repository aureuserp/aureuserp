<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
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
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Webkul\Account\Enums\CommunicationStandard;
use Webkul\Account\Enums\CommunicationType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Filament\Exports\BillExporter;
use Webkul\Account\Filament\Resources\BillResource\Pages\CreateBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\ListBills;
use Webkul\Account\Filament\Resources\BillResource\Pages\ViewBill;
use Webkul\Account\Livewire\InvoiceSummary;
use Webkul\Account\Models\CashRounding;
use Webkul\Account\Models\Bill;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Product;
use Webkul\Account\Models\Tax;
use Webkul\Account\Settings\CustomerInvoiceSettings;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/bill.global-search.vendor')   => $record->partner?->name ?? '—',
            __('accounts::filament/resources/bill.global-search.date')     => $record?->invoice_date ?? '—',
            __('accounts::filament/resources/bill.global-search.due-date') => $record?->invoice_date_due ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
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
                                                    ->relationship('invoicePaymentTerm', 'name')
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
                                                        modifyQueryUsing: fn (Builder $query) => $query->where('type', JournalType::PURCHASE),
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
                                                                'company_id'               => $get('company_id') ?? Auth::user()->default_company_id,
                                                            ])
                                                    )
                                                    ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),

                                                Select::make('currency_id')
                                                    ->label(__('accounts::filament/resources/bill.form.section.general.fields.currency'))
                                                    ->relationship(
                                                        'currency',
                                                        'name',
                                                        modifyQueryUsing: fn (Builder $query) => $query->where('active', 1),
                                                    )
                                                    ->required()
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->reactive()
                                                    ->default(Auth::user()->defaultCompany?->currency_id)
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
                                static::getProductRepeater(),

                                Livewire::make(static::getSummaryComponent(), function (Get $get, $record, $livewire) {
                                    $totals = self::calculateMoveTotals($get, $livewire);

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
                                            })
                                            ->default(Auth::user()->default_company_id),
                                        Select::make('invoice_incoterm_id')
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.incoterm'))
                                            ->relationship('invoiceIncoterm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(fn (CustomerInvoiceSettings $settings) => $settings->incoterm_id),
                                        TextInput::make('incoterm_location')
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.incoterm-location')),
                                        Select::make('preferred_payment_method_line_id')
                                            ->relationship('paymentMethodLine', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->label(__('accounts::filament/resources/bill.form.tabs.other-information.fieldset.accounting.fields.payment-method')),
                                        Select::make('fiscal_position_id')
                                            ->relationship('fiscalPosition', 'name')
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
                                    ->columns(1),
                            ])
                            ->columns(2),

                        Tab::make(__('accounts::filament/resources/bill.form.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                RichEditor::make('narration')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('name')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.state'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('invoice_partner_display_name')
                    ->label(__('accounts::filament/resources/bill.table.columns.customer'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.bill-date'))
                    ->sortable(),
                TextColumn::make('invoice_date_due')
                    ->state(function ($record) {
                        if ($record->payment_state == PaymentState::PAID) {
                            return null;
                        }

                        if (! $record->invoice_date_due) {
                            return '-';
                        }

                        if ($record->invoice_date_due->isToday()) {
                            return 'Today';
                        }

                        return $record->invoice_date_due->diffForHumans();
                    })
                    ->color(function ($record) {
                        if ($record->payment_state == PaymentState::PAID) {
                            return null;
                        }

                        if (! $record->invoice_date_due) {
                            return null;
                        }

                        if ($record->invoice_date_due->isToday()) {
                            return 'warning';
                        }

                        if ($record->invoice_date_due->isPast()) {
                            return 'danger';
                        }

                        return null;
                    })
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.due-date'))
                    ->sortable(),
                TextColumn::make('amount_untaxed_in_currency_signed')
                    ->label(__('accounts::filament/resources/bill.table.columns.tax-excluded'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn ($record) => $record->currency?->name)
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/bill.table.total')))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('amount_tax_signed')
                    ->label(__('accounts::filament/resources/bill.table.columns.tax'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn ($record) => $record->currency?->name)
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/bill.table.total')))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('amount_total_in_currency_signed')
                    ->label(__('accounts::filament/resources/bill.table.columns.total'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/bill.table.total')))
                    ->money(fn ($record) => $record->currency?->name)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('amount_residual_signed')
                    ->label(__('accounts::filament/resources/bill.table.columns.amount-due'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/bill.table.summarizers.total')))
                    ->money(fn ($record) => $record->currency?->name)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('payment_state')
                    ->label(__('Payment State'))
                    ->placeholder('-')
                    ->color(fn (PaymentState $state) => $state->getColor())
                    ->icon(fn (PaymentState $state) => $state->getIcon())
                    ->formatStateUsing(fn (PaymentState $state) => $state->getLabel())
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                IconColumn::make('checked')
                    ->boolean()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.checked'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.accounting-date'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoice_origin')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.source-document'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reference')
                    ->label(__('accounts::filament/resources/bill.table.columns.reference'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoiceUser.name')
                    ->label(__('accounts::filament/resources/bill.table.columns.sales-person'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency.name')
                    ->label(__('accounts::filament/resources/bill.table.columns.bill-currency'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/bill.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_partner_display_name')
                    ->label(__('accounts::filament/resources/bill.table.groups.bill-partner-display-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date')
                    ->label(__('accounts::filament/resources/bill.table.groups.bill-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('checked')
                    ->label(__('accounts::filament/resources/bill.table.groups.checked'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date')
                    ->date()
                    ->label(__('accounts::filament/resources/bill.table.groups.date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date_due')
                    ->date()
                    ->label(__('accounts::filament/resources/bill.table.groups.bill-due-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_origin')
                    ->label(__('accounts::filament/resources/bill.table.groups.bill-origin'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoiceUser.name')
                    ->date()
                    ->label(__('accounts::filament/resources/bill.table.groups.sales-person'))
                    ->collapsible(),
                Tables\Grouping\Group::make('currency.name')
                    ->label(__('accounts::filament/resources/bill.table.groups.currency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('accounts::filament/resources/bill.table.groups.created-at'))
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('accounts::filament/resources/bill.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('accounts::filament/resources/bill.table.filters.number')),
                        TextConstraint::make('invoice_origin')
                            ->label(__('accounts::filament/resources/bill.table.filters.bill-origin')),
                        TextConstraint::make('reference')
                            ->label(__('accounts::filament/resources/bill.table.filters.reference')),
                        TextConstraint::make('invoice_partner_display_name')
                            ->label(__('accounts::filament/resources/bill.table.filters.bill-partner-display-name')),
                        TextConstraint::make('payment_reference')
                            ->label(__('accounts::filament/resources/bill.table.filters.payment-reference')),
                        TextConstraint::make('narration')
                            ->label(__('accounts::filament/resources/bill.table.filters.narration')),
                        RelationshipConstraint::make('partner')
                            ->label(__('accounts::filament/resources/bill.table.filters.partner'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('journal')
                            ->label(__('accounts::filament/resources/bill.table.filters.journal'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('fiscalPosition')
                            ->label(__('accounts::filament/resources/bill.table.filters.fiscal-position'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('currency')
                            ->label(__('accounts::filament/resources/bill.table.filters.currency'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('accounts::filament/resources/bill.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('date')
                            ->label(__('accounts::filament/resources/bill.table.filters.date')),
                        DateConstraint::make('invoice_date')
                            ->label(__('accounts::filament/resources/bill.table.filters.bill-date')),
                        DateConstraint::make('invoice_date_due')
                            ->label(__('accounts::filament/resources/bill.table.filters.bill-due-date')),
                        DateConstraint::make('delivery_date')
                            ->label(__('accounts::filament/resources/bill.table.filters.delivery-date')),
                        NumberConstraint::make('amount_untaxed')
                            ->label(__('accounts::filament/resources/bill.table.filters.amount-untaxed')),
                        NumberConstraint::make('amount_tax')
                            ->label(__('accounts::filament/resources/bill.table.filters.amount-tax')),
                        NumberConstraint::make('amount_total')
                            ->label(__('accounts::filament/resources/bill.table.filters.amount-total')),
                        NumberConstraint::make('amount_residual')
                            ->label(__('accounts::filament/resources/bill.table.filters.amount-residual')),
                        BooleanConstraint::make('checked')
                            ->label(__('accounts::filament/resources/bill.table.filters.checked')),
                        BooleanConstraint::make('posted_before')
                            ->label(__('accounts::filament/resources/bill.table.filters.posted-before')),
                        BooleanConstraint::make('is_move_sent')
                            ->label(__('accounts::filament/resources/bill.table.filters.is-move-sent')),
                        DateConstraint::make('created_at')
                            ->label(__('accounts::filament/resources/bill.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('accounts::filament/resources/bill.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->hidden(fn (Model $record): bool => $record->state == MoveState::POSTED)
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/bill.table.actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/bill.table.actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/bill.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/bill.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
                ExportAction::make()
                    ->label(__('accounts::filament/resources/bill.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(BillExporter::class),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => static::can('delete', $record) && $record->state !== MoveState::POSTED,
            )
            ->modifyQueryUsing(function (Builder $query) {
                $query->with('currency');
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = MoveState::options();

                        if ($record->state != MoveState::CANCEL->value) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        if ($record == null) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        return $options;
                    })
                    ->default(MoveState::DRAFT->value)
                    ->columnSpan('full'),

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
                                Group::make()
                                    ->schema([
                                        TextEntry::make('partner.name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.vendor'))
                                            ->visible(fn ($record) => $record->partner_id !== null)
                                            ->icon('heroicon-o-user'),
                                        TextEntry::make('reference')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.bill-reference')),
                                    ])
                                    ->columns(1),
                                Group::make()
                                    ->schema([
                                        TextEntry::make('invoice_partner_display_name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.vendor'))
                                            ->visible(fn ($record) => $record->partner_id === null)
                                            ->icon('heroicon-o-user'),
                                        TextEntry::make('invoice_date')
                                            ->date()
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.bill-date')),
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
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('journal.name')
                                                    ->placeholder('-')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.journal')),
                                                TextEntry::make('currency.name')
                                                    ->placeholder('-')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.currency')),
                                            ]),
                                    ])
                                    ->columns(1),
                            ])
                            ->columns(2),
                    ]),

                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('invoiceLines')
                                    ->columnManager()
                                    ->columnManagerColumns(2)
                                    ->live()
                                    ->hiddenLabel()
                                    ->table([
                                        InfolistTableColumn::make('name')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.product')),
                                        InfolistTableColumn::make('quantity')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.quantity')),
                                        InfolistTableColumn::make('uom')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.unit')),
                                        InfolistTableColumn::make('price_unit')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.unit-price')),
                                        InfolistTableColumn::make('discount')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.discount-percentage')),
                                        InfolistTableColumn::make('taxes')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.taxes')),
                                        InfolistTableColumn::make('price_subtotal')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.sub-total')),
                                    ])
                                    ->schema([
                                        TextEntry::make('name')
                                            ->placeholder('-'),
                                        TextEntry::make('quantity')
                                            ->placeholder('-'),
                                        TextEntry::make('uom')
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom),
                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency->name),
                                        TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->suffix('%'),
                                        TextEntry::make('taxes')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => [
                                                    'name' => $tax->name,
                                                ])->toArray();
                                            })
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-')
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency->name),
                                    ]),

                                Livewire::make(static::getSummaryComponent(), function ($record) {
                                    $rounding = $record->roundingLines->sum('balance');

                                    return [
                                        'currency'   => $record->currency,
                                        'subtotal'   => $record->amount_untaxed ?? 0,
                                        'totalTax'   => $record->amount_tax ?? 0,
                                        'amountTax'  => $record->amount_tax ?? 0,
                                        'grandTotal' => $record->amount_total ?? 0,
                                        'rounding'   => $rounding,
                                    ];
                                }),
                            ]),

                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.journal-items.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->columnManager()
                                    ->live()
                                    ->table([
                                        InfolistTableColumn::make('account')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.account')),
                                        InfolistTableColumn::make('partner')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.partner')),
                                        InfolistTableColumn::make('name')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.label')),
                                        InfolistTableColumn::make('currency')
                                            ->alignCenter()
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.currency')),
                                        InfolistTableColumn::make('date_maturity')
                                            ->alignCenter()
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.due-date')),
                                        InfolistTableColumn::make('taxes')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.taxes')),
                                        InfolistTableColumn::make('debit')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.debit')),
                                        InfolistTableColumn::make('credit')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.credit')),
                                    ])
                                    ->schema([
                                        TextEntry::make('account')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state['name'] ?? '-'),
                                        TextEntry::make('partner')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state ? ($state['name'] ?? '-') : '-'),
                                        TextEntry::make('name')
                                            ->placeholder('-'),
                                        TextEntry::make('currency')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state['name'] ?? '-'),
                                        TextEntry::make('date_maturity')
                                            ->placeholder('-')
                                            ->date(),
                                        TextEntry::make('taxes')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => [
                                                    'name' => $tax->name,
                                                ])->toArray();
                                            })
                                            ->formatStateUsing(fn ($state) => $state['name'] ?? '-')
                                            ->placeholder('-')
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('debit')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency?->name),
                                        TextEntry::make('credit')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency?->name),
                                    ])->columns(5),
                            ]),

                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Fieldset::make(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.title'))
                                    ->schema([
                                        TextEntry::make('company.name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.company')),
                                        TextEntry::make('invoiceIncoterm.name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.incoterm')),
                                        TextEntry::make('incoterm_location')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.incoterm-location')),
                                        TextEntry::make('fiscalPosition.name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.fiscal-position')),
                                        TextEntry::make('cashRounding.name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.cash-rounding')),
                                        TextEntry::make('paymentMethodLine.name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.payment-method')),
                                        IconEntry::make('checked')
                                            ->boolean()
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.checked')),
                                    ])
                                    ->columns(1),
                            ])
                            ->columns(2),

                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                TextEntry::make('narration')
                                    ->html()
                                    ->hiddenLabel(),
                            ]),
                    ]),
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
                TableColumn::make('price_unit')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.unit-price'))
                    ->width(150)
                    ->markAsRequired(),
                TableColumn::make('discount')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.discount-percentage'))
                    ->width(150)
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('taxes')
                    ->label(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.columns.taxes'))
                    ->width(250)
                    ->toggleable(),
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
                        fn ($query) => $query->where('category_id', 1)->orderBy('id'),
                    )
                    ->required()
                    ->live()
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
                        modifyQueryUsing: fn (Builder $query) => $query->where('type_tax_use', TypeTaxUse::PURCHASE),
                    )
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

    public static function getSummaryComponent()
    {
        return InvoiceSummary::class;
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

        $priceUnit = static::calculateUnitPrice($get('uom_id'), $product);

        if ($get('../../currency_id')) {
            $currency = Currency::find($get('../../currency_id'));

            $priceUnit = Auth::user()->defaultCompany->currency->convert(
                $priceUnit,
                $currency,
                Auth::user()->defaultCompany
            );
        }

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

        $priceUnit = static::calculateUnitPrice($get('uom_id'), $product);

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

    private static function calculateUnitPrice($uomId, $product)
    {
        $price = $product->price ?? $product->cost;

        if (! $uomId) {
            return $price;
        }

        $uomQty = Uom::find($uomId)->computeQuantity(1, $product->uom, true, 'HALF-UP');

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

        $baseLine = AccountFacade::prepareProductBaseLineForTaxesComputation($mockLine);

        $baseLine = TaxFacade::addTaxDetailsInBaseLine($baseLine, $company);

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

        [$baseLines] = AccountFacade::getRoundedBaseAndTaxLines($mockMove, false);

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(Str::contains(static::class, 'BillResource'), function (Builder $query) {
                $query->where('move_type', MoveType::IN_INVOICE);
            })
            ->orderByDesc('id');
    }

    protected static function disableTypeField($component): void
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
