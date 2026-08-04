<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Filament\Resources\JournalResource;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Tax;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Filament\Summarizers\Sum;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class JournalEntryForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
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
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                                    ->where('type', JournalType::GENERAL)
                                                    ->where('company_id', $get('company_id') ?? current_company_id()),
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
                                    ->default(current_company_id())
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $company = Company::find($get('company_id'));

                                        if ($company?->currency_id) {
                                            $set('currency_id', $company->currency_id);
                                        }

                                        clear_foreign_company_values($set, $get, [
                                            'journal_id'         => Journal::class,
                                            'fiscal_position_id' => FiscalPosition::class,
                                        ], $get('company_id'));
                                    }),
                                Toggle::make('checked')
                                    ->inline(false)
                                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.checked')),
                                Select::make('fiscal_position_id')
                                    ->relationship(
                                        'fiscalPosition',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                    )
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

                Section::make()
                    ->schema($customFormFields)
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function getLineRepeater(): Repeater
    {
        return Repeater::make('lines')
            ->relationship('lines')
            ->hiddenLabel()
            ->compact()
            ->live()
            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.title'))
            ->addActionLabel(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.add-item'))
            ->collapsible()
            ->defaultItems(0)
            ->deleteAction(function (Action $action) {
                $action->requiresConfirmation();
            })
            ->addable(fn ($record): bool => ! in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
            ->deletable(fn ($record): bool => ! in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
            ->reorderable(false)
            ->table([
                TableColumn::make('account_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.account'))
                    ->resizable()
                    ->wrapHeader()
                    ->markAsRequired(),
                TableColumn::make('partner_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.partner'))
                    ->resizable()
                    ->toggleable(),
                TableColumn::make('name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.label'))
                    ->resizable()
                    ->toggleable(),
                TableColumn::make('amount_currency')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.amount-currency'))
                    ->resizable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('currency_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.currency'))
                    ->resizable()
                    ->markAsRequired()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('taxes')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.taxes'))
                    ->resizable()
                    ->toggleable(),
                TableColumn::make('debit')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.debit'))
                    ->resizable()
                    ->summarize(Sum::make())
                    ->markAsRequired(),
                TableColumn::make('credit')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.credit'))
                    ->resizable()
                    ->summarize(Sum::make())
                    ->markAsRequired(),
                TableColumn::make('discount_amount_currency')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.discount-amount-currency'))
                    ->resizable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->schema([
                Hidden::make('display_type'),
                Select::make('account_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.account'))
                    ->relationship(
                        'account',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('../../company_id'))),
                    )
                    ->searchable()
                    ->required()
                    ->preload()
                    ->live()
                    ->wrapOptionLabels(false)
                    ->selectablePlaceholder(false)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                Select::make('partner_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.partner'))
                    ->relationship('partner', 'name')
                    ->searchable()
                    ->wrapOptionLabels(false)
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
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::amountCurrencyUpdated($set, $get))
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                Select::make('currency_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.currency'))
                    ->relationship(
                        name: 'currency',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->active(),
                    )
                    ->default(current_company()?->currency_id)
                    ->required()
                    ->live()
                    ->selectablePlaceholder(false)
                    ->dehydrated()
                    ->wrapOptionLabels(false)
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::currencyUpdated($set, $get))
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                Select::make('taxes')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.taxes'))
                    ->relationship(
                        'taxes',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('../../company_id'))),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.' ('.$record->type_tax_use->getLabel().')';
                    })
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->dehydrated()
                    ->live()
                    ->wrapOptionLabels(false)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::taxesUpdated($set, $get))
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                TextInput::make('debit')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.debit'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::debitUpdated($set, $get)),
                TextInput::make('credit')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.credit'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
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

    private static function currencyUpdated(Set $set, Get $get): void {}

    private static function taxesUpdated(Set $set, Get $get): void
    {
        self::recalculateJournalTaxLines($get, $set);
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

        self::recalculateJournalTaxLines($get, $set);
    }

    private static function debitUpdated(Set $set, Get $get): void
    {
        $set('credit', 0);

        $set('amount_currency', $get('debit'));

        self::recalculateJournalTaxLines($get, $set);
    }

    private static function creditUpdated(Set $set, Get $get): void
    {
        $set('debit', 0);

        $set('amount_currency', -1 * $get('credit'));

        self::recalculateJournalTaxLines($get, $set);
    }

    private static function discountAmountCurrencyUpdated(Set $set, Get $get): void {}

    private static function recalculateJournalTaxLines(Get $get, Set $set): void
    {
        $lines = $get('../../lines') ?? [];

        if (empty($lines)) {
            return;
        }

        $currencyId = $get('currency_id');
        $companyId = $get('../../company_id');
        $journalId = $get('../../journal_id');

        if (! $currencyId || ! $companyId) {
            return;
        }

        $currency = Currency::find($currencyId);
        $company = Company::find($companyId);

        if (! $currency || ! $company) {
            return;
        }

        $journal = $journalId ? Journal::find($journalId) : null;
        $suspenseAccountId = $journal?->suspense_account_id ?? (new DefaultAccountSettings)->account_journal_suspense_account_id;

        $lines = collect($lines)
            ->reject(function ($line) {
                $name = $line['name'] ?? '';

                return str_starts_with($name, 'Tax:') || $name === 'Automatic Balancing';
            })
            ->values()
            ->all();

        if (empty($lines)) {
            $set('../../lines', []);

            return;
        }

        $mockMove = new AccountMove([
            'currency_id' => $currency->id,
            'company_id'  => $company->id,
            'journal_id'  => $journalId,
        ]);

        $mockMove->setRelation('currency', $currency);
        $mockMove->setRelation('company', $company);

        $totalDebit = collect($lines)->sum(fn ($lineData) => (float) ($lineData['debit'] ?? 0));
        $totalCredit = collect($lines)->sum(fn ($lineData) => (float) ($lineData['credit'] ?? 0));

        $baseLines = [];

        foreach ($lines as $lineData) {
            if (empty($lineData['taxes'])) {
                continue;
            }

            $mockLine = self::createMockMoveLine($lineData, $mockMove, $currency, $company);

            $baseLine = AccountFacade::productBaseLine($mockLine);

            $baseLine = TaxFacade::withTaxDetails($baseLine, $company);

            $baseLine = TaxFacade::withAccountingData($baseLine, $company);

            $baseLines[] = $baseLine;
        }

        $taxLinesMap = [];

        foreach ($baseLines as $baseLine) {
            $taxesData = $baseLine['tax_details']['taxes_data'] ?? [];

            foreach ($taxesData as $taxData) {
                if (! isset($taxData['tax_reps_data']) || empty($taxData['tax_reps_data'])) {
                    continue;
                }

                foreach ($taxData['tax_reps_data'] as $taxRepData) {
                    $accountId = $taxRepData['account']?->id ?? null;

                    if (! $accountId) {
                        continue;
                    }

                    $partnerId = $baseLine['partner']->id ?? null;

                    $key = $accountId.'_'.($partnerId ?? 'null');

                    if (! isset($taxLinesMap[$key])) {
                        $taxName = $taxData['tax']->name ?? 'Tax';

                        $taxLinesMap[$key] = [
                            'display_type'             => 'tax',
                            'account_id'               => $accountId,
                            'partner_id'               => $partnerId,
                            'name'                     => 'Tax: '.$taxName,
                            'amount_currency'          => 0,
                            'currency_id'              => $currency->id,
                            'taxes'                    => [],
                            'debit'                    => 0,
                            'credit'                   => 0,
                            'discount_amount_currency' => 0,
                        ];
                    }

                    $taxAmount = $taxRepData['tax_amount'] ?? $taxRepData['tax_amount_currency'] ?? 0;

                    $taxLinesMap[$key]['amount_currency'] += $taxAmount;

                    if ($taxAmount < 0) {
                        $taxLinesMap[$key]['credit'] += abs($taxAmount);
                    } else {
                        $taxLinesMap[$key]['debit'] += abs($taxAmount);
                    }
                }
            }
        }

        $lines = array_values($lines);

        foreach ($taxLinesMap as $key => $taxLine) {
            $taxLine['debit'] = round($taxLine['debit'], 2);
            $taxLine['credit'] = round($taxLine['credit'], 2);
            $taxLine['amount_currency'] = round($taxLine['amount_currency'], 2);

            $taxLinesMap[$key] = $taxLine;

            $lines[] = $taxLine;

            $totalDebit += $taxLine['debit'];
            $totalCredit += $taxLine['credit'];
        }

        $balancingLine = self::calculateBalancingLine($totalDebit, $totalCredit, $company, $suspenseAccountId);

        if ($balancingLine) {
            $lines[] = $balancingLine;
        }

        $set('../../lines', $lines);
    }

    private static function createMockMoveLine(array $lineData, AccountMove $mockMove, Currency $currency, Company $company): MoveLine
    {
        $partner = isset($lineData['partner_id']) ? Partner::find($lineData['partner_id']) : null;
        $account = isset($lineData['account_id']) ? Account::find($lineData['account_id']) : null;

        $amountCurrency = ($lineData['debit'] ?? 0) - ($lineData['credit'] ?? 0);

        $mockLine = new MoveLine([
            'amount_currency' => $amountCurrency,
            'balance'         => $amountCurrency,
            'name'            => $lineData['name'] ?? '',
        ]);

        $taxIds = $lineData['taxes'] ?? [];
        $mockLine->setRelation('taxes', Tax::whereIn('id', $taxIds)->get());
        $mockLine->setRelation('currency', $currency);
        $mockLine->setRelation('company', $company);
        $mockLine->setRelation('move', $mockMove);

        if ($partner) {
            $mockLine->setRelation('partner', $partner);
        }

        if ($account) {
            $mockLine->setRelation('account', $account);
        }

        return $mockLine;
    }

    private static function recalculateBalancingLineOnly(Get $get, Set $set): void
    {
        $lines = $get('../../lines') ?? [];

        if (empty($lines)) {
            return;
        }

        $companyId = $get('../../company_id');
        $journalId = $get('../../journal_id');

        if (! $companyId) {
            return;
        }

        $company = Company::find($companyId);

        if (! $company) {
            return;
        }

        $journal = $journalId ? Journal::find($journalId) : null;

        $suspenseAccountId = $journal?->suspense_account_id ?? (new DefaultAccountSettings)->account_journal_suspense_account_id;

        $linesWithoutBalancing = collect($lines)
            ->reject(fn ($line) => ($line['is_auto_generated'] ?? false) && ($line['auto_type'] ?? null) === 'balancing')
            ->values()
            ->all();

        $totalDebit = collect($linesWithoutBalancing)->sum(fn ($lineData) => data_get($lineData, 'debit', 0));
        $totalCredit = collect($linesWithoutBalancing)->sum(fn ($lineData) => data_get($lineData, 'credit', 0));

        $balancingLine = self::calculateBalancingLine($totalDebit, $totalCredit, $company, $suspenseAccountId);

        if ($balancingLine) {
            $linesWithoutBalancing[] = $balancingLine;
        }

        $set('../../lines', $linesWithoutBalancing);
    }

    private static function calculateBalancingLine(float $totalDebit, float $totalCredit, Company $company, ?int $accountId = null): ?array
    {
        $difference = round($totalDebit - $totalCredit, 2);

        if (abs($difference) < 0.01) {
            return null;
        }

        if (! $accountId) {
            return null;
        }

        return [
            'account_id'               => $accountId,
            'partner_id'               => null,
            'name'                     => 'Automatic Balancing',
            'amount_currency'          => -$difference,
            'currency_id'              => $company->currency_id,
            'taxes'                    => [],
            'debit'                    => $difference < 0 ? abs($difference) : 0,
            'credit'                   => $difference > 0 ? $difference : 0,
            'discount_amount_currency' => 0,
            'is_auto_generated'        => true,
            'auto_type'                => 'balancing',
        ];
    }
}
