<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Filament\Exports\PaymentExporter;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\CreatePayment;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\EditPayment;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\ListPayments;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\ViewPayment;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\PaymentMethodLine;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'partner.name', 'amount'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/payment.global-search.partner') => $record->partner?->name ?? '—',
            __('accounts::filament/resources/payment.global-search.amount')  => $record->amount ? money($record->amount) : '—',
            __('accounts::filament/resources/payment.global-search.date')    => $record->date ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn ($record) => $record && $record->state !== PaymentStatus::DRAFT)
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = PaymentStatus::options();

                        if (
                            $record
                            && $record->state != PaymentStatus::NOT_PAID
                        ) {
                            unset($options[PaymentStatus::NOT_PAID->value]);
                        }

                        if (
                            $record
                            && $record->state != PaymentStatus::CANCELED
                        ) {
                            unset($options[PaymentStatus::CANCELED->value]);
                        }

                        if (
                            $record
                            && $record->state != PaymentStatus::REJECTED
                        ) {
                            unset($options[PaymentStatus::REJECTED->value]);
                        }

                        if ($record == null) {
                            unset($options[PaymentStatus::CANCELED->value]);
                        }

                        return $options;
                    })
                    ->default(PaymentStatus::DRAFT->value)
                    ->columnSpan('full')
                    ->disabled()
                    ->live()
                    ->reactive(),

                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                ToggleButtons::make('payment_type')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.payment-type'))
                                    ->options(PaymentType::class)
                                    ->inline(true)
                                    ->live(),
                            ]),

                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Select::make('partner_id')
                                            ->label(
                                                fn (Get $get) => $get('payment_type') === PaymentType::RECEIVE
                                                    ? __('accounts::filament/resources/payment.form.sections.fields.customer')
                                                    : __('accounts::filament/resources/payment.form.sections.fields.vendor')
                                            )
                                            ->relationship(
                                                'partner',
                                                'name',
                                                fn (Builder $query, Get $get) => $query->orderBy('id'),
                                            )
                                            ->reactive()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $partner = $state ? Partner::find($state) : null;

                                                $set('partner_bank_id', $partner?->bankAccounts->first()?->id);
                                                $set('payment_method_line_id', $partner?->propertyInboundPaymentMethodLine?->id);
                                            })
                                            ->searchable()
                                            ->preload(),

                                        Group::make()
                                            ->schema([
                                                TextInput::make('amount')
                                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.amount'))
                                                    ->default(0)
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(99999999999)
                                                    ->required(),
                                                Select::make('currency_id')
                                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.currency'))
                                                    ->relationship(
                                                        'currency',
                                                        'name',
                                                        modifyQueryUsing: fn (Builder $query) => $query->where('active', 1),
                                                    )
                                                    ->required()
                                                    ->searchable()
                                                    ->preload()
                                                    ->default(fn () => Auth::user()->defaultCompany?->currency_id),
                                            ])
                                            ->columns(2),

                                        DatePicker::make('date')
                                            ->label(__('accounts::filament/resources/payment.form.sections.fields.date'))
                                            ->native(false)
                                            ->default(now())
                                            ->required(),
                                        TextInput::make('memo')
                                            ->label(__('accounts::filament/resources/payment.form.sections.fields.memo'))
                                            ->maxLength(255),
                                    ])
                                    ->columns(1),

                                Group::make()
                                    ->schema([
                                        Select::make('journal_id')
                                            ->label(__('accounts::filament/resources/payment.form.sections.fields.journal'))
                                            ->relationship(
                                                'journal',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->whereIn('type', [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD]),
                                            )
                                            ->default(function () {
                                                $journal = Journal::whereIn('type', [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD])->first();

                                                return $journal?->id;
                                            })
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                $payment = new Payment;
                                                $payment->payment_type = $get('payment_type');
                                                $payment->journal_id = $get('journal_id');
                                                $payment->journal = Journal::find($get('journal_id'));
                                                $payment->computePaymentMethodLineId();

                                                $set('payment_method_line_id', $payment->payment_method_line_id);
                                                $set('partner_bank_id', null);
                                            })
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live(),
                                        Select::make('payment_method_line_id')
                                            ->label(__('accounts::filament/resources/payment.form.sections.fields.payment-method'))
                                            ->relationship(
                                                name: 'paymentMethodLine',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: function (Builder $query, Get $get) {
                                                    $journal = Journal::find($get('journal_id'));

                                                    if (! $journal) {
                                                        return $query->whereRaw('1 = 0');
                                                    }

                                                    $paymentMethodLineIds = $journal->getAvailablePaymentMethodLines($get('payment_type'))->pluck('id');

                                                    $query->whereIn('id', $paymentMethodLineIds);
                                                }
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live(),
                                        Select::make('partner_bank_id')
                                            ->label(
                                                fn (Get $get) => $get('payment_type') === PaymentType::RECEIVE
                                                    ? __('accounts::filament/resources/payment.form.sections.fields.customer-bank-account')
                                                    : __('accounts::filament/resources/payment.form.sections.fields.vendor-bank-account')
                                            )
                                            ->relationship(
                                                'partnerBank',
                                                'account_number',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->withTrashed()->where('partner_id', $get('partner_id')),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->account_number.' - '.$record->bank->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->required(function (Get $get) {
                                                return static::computePayment($get)->require_partner_bank_account
                                                    && static::computePayment($get)->show_partner_bank_account;
                                            })
                                            ->visible(function (Get $get) {
                                                return static::computePayment($get)->show_partner_bank_account;
                                            })
                                            ->disabled(function (Get $get) {
                                                return static::computePayment($get)->require_partner_bank_account;
                                            })
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->columns(1),
                            ])
                            ->columns(2),
                    ])
                    ->columns(1),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('date')
                    ->label(__('accounts::filament/resources/payment.table.columns.date'))
                    ->placeholder('-')
                    ->date()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/payment.table.columns.name'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('journal.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.journal'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('paymentMethod.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.payment-method'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.partner'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('amount_company_currency_signed')
                    ->label(__('accounts::filament/resources/payment.table.columns.amount-currency'))
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn (Payment $record) => $record->company?->currency_code, true),
                TextColumn::make('amount')
                    ->label(__('accounts::filament/resources/payment.table.columns.amount'))
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn (Payment $record) => $record->company?->currency_code, true),
                TextColumn::make('state')
                    ->label(__('accounts::filament/resources/payment.table.columns.state'))
                    ->placeholder('-')
                    ->sortable()
                    ->badge(),
                TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.company'))
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/payment.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('journal.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.journal'))
                    ->collapsible(),
                Tables\Grouping\Group::make('paymentMethodLine.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.payment-method-line'))
                    ->collapsible(),
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.partner'))
                    ->collapsible(),
                Tables\Grouping\Group::make('paymentMethod.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.payment-method'))
                    ->collapsible(),
                Tables\Grouping\Group::make('partnerBank.account_holder_name')
                    ->label(__('accounts::filament/resources/payment.table.groups.partner-bank-account'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('accounts::filament/resources/payment.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('accounts::filament/resources/payment.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('journal')
                            ->label(__('accounts::filament/resources/payment.table.filters.journal'))
                            ->icon('heroicon-o-book-open')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.journal'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('accounts::filament/resources/payment.table.filters.company'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.company'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partnerBank')
                            ->label(__('accounts::filament/resources/payment.table.filters.customer-bank-account'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('account_number')
                                    ->label(__('accounts::filament/resources/payment.table.filters.customer-bank-account'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('paymentMethodLine')
                            ->label(__('accounts::filament/resources/payment.table.filters.payment-method-line'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.payment-method-line'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('paymentMethod')
                            ->label(__('accounts::filament/resources/payment.table.filters.payment-method'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.payment-method'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('currency')
                            ->label(__('accounts::filament/resources/payment.table.filters.currency'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.currency'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partner')
                            ->label(__('accounts::filament/resources/payment.table.filters.partner'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.partner'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('accounts::filament/resources/payment.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('accounts::filament/resources/payment.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/payment.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/payment.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
                ExportAction::make()
                    ->label(__('accounts::filament/resources/payment.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(PaymentExporter::class),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make()
                    ->schema([
                        InfolistProgressStepper::make('state')
                            ->hiddenLabel()
                            ->inline()
                            ->options(function ($record) {
                                $options = PaymentStatus::options();

                                if (
                                    $record
                                    && $record->state != PaymentStatus::NOT_PAID
                                ) {
                                    unset($options[PaymentStatus::NOT_PAID->value]);
                                }

                                if (
                                    $record
                                    && $record->state != PaymentStatus::CANCELED
                                ) {
                                    unset($options[PaymentStatus::CANCELED->value]);
                                }

                                if (
                                    $record
                                    && $record->state != PaymentStatus::REJECTED
                                ) {
                                    unset($options[PaymentStatus::REJECTED->value]);
                                }

                                if ($record == null) {
                                    unset($options[PaymentStatus::CANCELED->value]);
                                }

                                return $options;
                            })
                            ->columnSpan('full'),
                    ])->columns(2),

                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('payment_type')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.payment-type'))
                                    ->badge(),
                                TextEntry::make('partner.name')
                                    ->label(
                                        fn ($record) => $record->payment_type === PaymentType::RECEIVE
                                            ? __('accounts::filament/resources/payment.infolist.sections.payment-information.entries.customer')
                                            : __('accounts::filament/resources/payment.infolist.sections.payment-information.entries.vendor')
                                    )
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),
                                TextEntry::make('amount')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.amount'))
                                    ->money(fn (Payment $record) => $record->currency->name, true)
                                    ->placeholder('—'),
                                TextEntry::make('date')
                                    ->icon('heroicon-o-calendar')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.date'))
                                    ->placeholder('—')
                                    ->date(),
                                TextEntry::make('memo')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.memo'))
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('—'),
                            ])
                            ->columns(1),

                        Group::make()
                            ->schema([
                                TextEntry::make('journal.name')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.journal'))
                                    ->icon('heroicon-o-building-library')
                                    ->placeholder('—'),
                                TextEntry::make('paymentMethodLine.name')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.payment-method'))
                                    ->icon('heroicon-o-credit-card')
                                    ->placeholder('—'),
                                TextEntry::make('partnerBank.account_number')
                                    ->label(
                                        fn ($record) => $record->payment_type === PaymentType::RECEIVE
                                            ? __('accounts::filament/resources/payment.infolist.sections.payment-information.entries.customer-bank-account')
                                            : __('accounts::filament/resources/payment.infolist.sections.payment-information.entries.vendor-bank-account')
                                    )
                                    ->icon('heroicon-o-building-library')
                                    ->placeholder('—'),
                            ])
                            ->columns(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function computePayment($get)
    {
        $journal = Journal::find($get('journal_id'));

        $payment = new Payment;

        if (! $journal) {
            return $payment;
        }

        $payment->payment_type = $get('payment_type');
        $payment->journal = $journal;
        $payment->payment_method_line_id = $get('payment_method_line_id');
        $payment->paymentMethodLine = PaymentMethodLine::find($get('payment_method_line_id'));
        $payment->partner_id = $get('partner_id');
        $payment->partner = Partner::find($get('partner_id'));

        if (! $payment->paymentMethodLine) {
            $payment->computePaymentMethodLineId();

            return $payment;
        }

        if (! $payment->paymentMethodLine) {
            return $payment;
        }

        $payment->computeShowRequirePartnerBank();

        return $payment;
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'view'   => ViewPayment::route('/{record}'),
            'edit'   => EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
