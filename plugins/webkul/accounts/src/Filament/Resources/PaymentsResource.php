<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Filament\Resources\PaymentsResource\Pages\CreatePayments;
use Webkul\Account\Filament\Resources\PaymentsResource\Pages\EditPayments;
use Webkul\Account\Filament\Resources\PaymentsResource\Pages\ListPayments;
use Webkul\Account\Filament\Resources\PaymentsResource\Pages\ViewPayments;
use Webkul\Account\Models\Payment;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;

class PaymentsResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        ProgressStepper::make('state')
                            ->hiddenLabel()
                            ->inline()
                            ->options(PaymentStatus::class)
                            ->default(PaymentStatus::DRAFT->value)
                            ->columnSpan('full')
                            ->disabled()
                            ->live()
                            ->reactive(),
                    ])->columns(2),
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                ToggleButtons::make('payment_type')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.payment-type'))
                                    ->options(PaymentType::class)
                                    ->default(PaymentType::SEND->value)
                                    ->inline(true),
                                Select::make('partner_bank_id')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.customer-bank-account'))
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
                                    ->required(),
                                Select::make('partner_id')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.customer'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                    )
                                    ->searchable()
                                    ->preload(),
                                Select::make('payment_method_line_id')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.payment-method'))
                                    ->relationship(
                                        'paymentMethodLine',
                                        'name',
                                    )
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('amount')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.amount'))
                                    ->default(0)
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required(),
                                DatePicker::make('date')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.date'))
                                    ->native(false)
                                    ->default(now())
                                    ->required(),
                                TextInput::make('memo')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.memo'))
                                    ->maxLength(255),
                            ])->columns(2),
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
                    ->label(__('accounts::filament/resources/payment.table.columns.name'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.company'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('partnerBank.account_holder_name')
                    ->label(__('accounts::filament/resources/payment.table.columns.bank-account-holder'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('pairedInternalTransferPayment.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.paired-internal-transfer-payment'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('paymentMethodLine.name')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/payment.table.columns.payment-method-line'))
                    ->sortable(),
                TextColumn::make('paymentMethod.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.payment-method'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('currency.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.currency'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.partner'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('outstandingAccount.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.outstanding-amount'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('destinationAccount.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.destination-account'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.created-by'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paymentTransaction.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.payment-transaction'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/payment.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.company'))
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
                Tables\Grouping\Group::make('pairedInternalTransferPayment.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.paired-internal-transfer-payment'))
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
                        RelationshipConstraint::make('pairedInternalTransferPayment')
                            ->label(__('accounts::filament/resources/payment.table.filters.paired-internal-transfer-payment'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.paired-internal-transfer-payment'))
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
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('state')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        PaymentStatus::DRAFT->value      => 'gray',
                                        PaymentStatus::IN_PROCESS->value => 'warning',
                                        PaymentStatus::PAID->value       => 'success',
                                        PaymentStatus::CANCELED->value   => 'danger',
                                        default                          => 'gray',
                                    })
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.state'))
                                    ->formatStateUsing(fn (string $state): string => PaymentStatus::options()[$state]),
                                TextEntry::make('payment_type')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.payment-type'))
                                    ->badge()
                                    ->icon(fn (string $state): string => PaymentType::from($state)->getIcon())
                                    ->color(fn (string $state): string => PaymentType::from($state)->getColor())
                                    ->formatStateUsing(fn (string $state): string => PaymentType::from($state)->getLabel()),
                                TextEntry::make('partnerBank.account_number')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.customer-bank-account'))
                                    ->icon('heroicon-o-building-library')
                                    ->placeholder('—'),
                                TextEntry::make('partner.name')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.customer'))
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),
                                TextEntry::make('paymentMethodLine.name')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-method.entries.payment-method'))
                                    ->icon('heroicon-o-credit-card')
                                    ->placeholder('—'),
                                TextEntry::make('amount')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-details.entries.amount'))
                                    ->placeholder('—'),
                                TextEntry::make('date')
                                    ->icon('heroicon-o-calendar')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-details.entries.date'))
                                    ->placeholder('—')
                                    ->date(),
                                TextEntry::make('memo')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-details.entries.memo'))
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('—'),
                            ])->columns(2),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPayments::route('/'),
            'create' => CreatePayments::route('/create'),
            'view'   => ViewPayments::route('/{record}'),
            'edit'   => EditPayments::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
