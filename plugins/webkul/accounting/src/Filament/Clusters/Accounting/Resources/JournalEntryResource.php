<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources;

use Dom\Text;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
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
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\CreateJournalEntry;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\EditJournalEntry;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\ListJournalEntries;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\ViewJournalEntry;
use Webkul\Account\Livewire\InvoiceSummary;
use Webkul\Account\Models\CashRounding;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Product;
use Webkul\Account\Filament\Resources\JournalResource;
use Webkul\Account\Filament\Resources\BankAccountResource;
use Webkul\Account\Models\Tax;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;
use Webkul\Accounting\Filament\Clusters\Accounting;

class JournalEntryResource extends Resource
{
    protected static ?string $model = AccountMove::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Accounting::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/accounting/resources/journal-entry.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/accounting/resources/journal-entry.navigation.title');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounting::filament/clusters/accounting/resources/journal-entry.global-search.number')           => $record?->name ?? '—',
            __('accounting::filament/clusters/accounting/resources/journal-entry.global-search.customer')         => $record?->invoice_partner_display_name ?? '—',
            __('accounting::filament/clusters/accounting/resources/journal-entry.global-search.invoice-date')     => $record?->invoice_date ?? '—',
            __('accounting::filament/clusters/accounting/resources/journal-entry.global-search.invoice-date-due') => $record?->invoice_date_due ?? '—',
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

                Section::make(__('accounting::filament/clusters/accounting/resources/journal-entry.form.section.general.title'))
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
                                        TextInput::make('reference')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.section.general.fields.reference'))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ]),

                                Group::make()
                                    ->schema([
                                        DatePicker::make('date')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.section.general.fields.accounting-date'))
                                            ->default(now())
                                            ->native(false)
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                            
                                        Select::make('journal_id')
                                            ->relationship(
                                                'journal',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->where('type', JournalType::GENERAL),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.section.general.fields.journal'))
                                            ->createOptionForm(fn ($form) => JournalResource::form($form))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ]),
                            ])->columns(2),
                    ]),

                Tabs::make()
                    ->schema([
                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                static::getLineRepeater(),
                                
                                Livewire::make(InvoiceSummary::class, function  (Get $get, $record, $livewire) {
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
                                ->visible(fn (Get $get) => $get('currency_id') && ! empty($get('lines'))),
                            ]),

                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Select::make('company_id')
                                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.company'))
                                    ->relationship('company', 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set, $state) => $set('currency_id', Company::find($state)?->currency_id))
                                    ->default(Auth::user()->default_company_id)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $company = Company::find($get('company_id'));

                                        if ($company?->currency_id) {
                                            $set('currency_id', $company->currency_id);
                                        }
                                    }),
                                Toggle::make('checked')
                                    ->inline(false)
                                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.checked')),
                                Select::make('fiscal_position_id')
                                    ->relationship('fiscalPosition', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.fiscal-position'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.fiscal-position-tooltip'))
                                    ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                            ])
                            ->columns(2),
                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.term-and-conditions.title'))
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
                TextColumn::make('invoice_date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.invoice-date'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.date'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_partner_display_name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.partner'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('reference')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.reference'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('journal.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.journal'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.company'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('amount_total_in_currency_signed')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.total'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->summarize(Sum::make()->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.total')))
                    ->money(fn ($record) => $record->company->currency?->name),
                TextColumn::make('state')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.state'))
                    ->sortable(),
                IconColumn::make('checked')
                    ->boolean()
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.checked'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('invoice_partner_display_name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.partner'))
                    ->collapsible(),
                Tables\Grouping\Group::make('journal.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.journal'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.state'))
                    ->collapsible(),
                Tables\Grouping\Group::make('paymentMethodLine.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.payment-method'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date')
                    ->date()
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.invoice-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.company'))
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.number')),
                        TextConstraint::make('invoice_origin')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.invoice-origin')),
                        TextConstraint::make('reference')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.reference')),
                        TextConstraint::make('invoice_partner_display_name')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.invoice-partner-display-name')),
                        DateConstraint::make('invoice_date')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.invoice-date')),
                        DateConstraint::make('invoice_date_due')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.invoice-due-date')),
                        DateConstraint::make('created_at')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounting::filament/clusters/accounting/resources/journal-entry.table.actions.delete.notification.title'))
                                ->body(__('accounting::filament/clusters/accounting/resources/journal-entry.table.actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounting::filament/clusters/accounting/resources/journal-entry.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounting::filament/clusters/accounting/resources/journal-entry.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->with('currency');
            });
    }


    public static function getLineRepeater(): Repeater
    {
        return Repeater::make('lines')
            ->relationship('invoiceLines')
            ->hiddenLabel()
            ->compact()
            ->live()
            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.title'))
            ->addActionLabel(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.add-item'))
            ->collapsible()
            ->defaultItems(0)
            ->deleteAction(function(Action $action) {
                $action->requiresConfirmation();

                $action->after(function (Get $get, $livewire) {
                    $totals = self::calculateMoveTotals($get, $livewire);

                    $livewire->dispatch('itemUpdated', $totals);
                });
            })
            ->addable(fn ($record): bool => ! in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
            ->deletable(fn ($record): bool => ! in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
            ->table([
                TableColumn::make('account_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.account'))
                    ->width(200)
                    ->markAsRequired(),
                TableColumn::make('partner_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.partner'))
                    ->width(160)
                    ->toggleable(),
                TableColumn::make('name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.label'))
                    ->width(160)
                    ->toggleable(),
                TableColumn::make('amount_currency')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.amount-currency'))
                    ->width(160)
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('currency_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.currency'))
                    ->width(100)
                    ->markAsRequired()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('debit')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.debit'))
                    ->width(100)
                    ->markAsRequired(),
                TableColumn::make('credit')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.credit'))
                    ->width(100)
                    ->markAsRequired(),
                TableColumn::make('discount_amount_currency')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.discount-amount-currency'))
                    ->width(200)
                    ->wrapHeader()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->schema([
                Select::make('account_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.account'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->live()
                    ->selectablePlaceholder(false)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                Select::make('partner_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.partner'))
                    ->relationship('partner', 'name')
                    ->searchable()
                    ->preload()
                    ->selectablePlaceholder(false)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                TextInput::make('name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.label'))
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                TextInput::make('amount_currency')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.amount-currency'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::amountCurrencyUpdated($set, $get))
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                Select::make('currency_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.currency'))
                    ->relationship(
                        'currency',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('active', 1),
                    )
                    ->default(Auth::user()->defaultCompany?->currency_id)
                    ->required()
                    ->live()
                    ->selectablePlaceholder(false)
                    ->dehydrated()
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::currencyUpdated($set, $get))
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                TextInput::make('debit')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.debit'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::debitUpdated($set, $get)),
                TextInput::make('credit')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.credit'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::creditUpdated($set, $get)),
                TextInput::make('discount_amount_currency')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.discount-amount-currency'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::discountAmountCurrencyUpdated($set, $get)),
            ]);
    }

    private static function currencyUpdated(Set $set, Get $get): void
    {
    }

    private static function amountCurrencyUpdated(Set $set, Get $get): void
    {
        if ($get('amount_currency') >= 0) {
            $set('debit', $get('amount_currency'));
            $set('credit', 0);
        } else {
            $set('debit', 0);
            $set('credit', abs($get('amount_currency')));
        }
    }

    private static function debitUpdated(Set $set, Get $get): void
    {
        $set('credit', 0);

        $set('amount_currency', $get('debit'));
    }

    private static function creditUpdated(Set $set, Get $get): void
    {
        $set('debit', 0);

        $set('amount_currency', -1 * $get('credit'));
    }

    private static function discountAmountCurrencyUpdated(Set $set, Get $get): void
    {
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewJournalEntry::class,
            EditJournalEntry::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListJournalEntries::route('/'),
            'create' => CreateJournalEntry::route('/create'),
            'view'   => ViewJournalEntry::route('/{record}'),
            'edit'   => EditJournalEntry::route('/{record}/edit'),
        ];
    }
}