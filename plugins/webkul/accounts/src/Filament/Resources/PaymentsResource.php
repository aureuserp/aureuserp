<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Filament\Resources\PaymentsResource\Pages;
use Webkul\Account\Models\Payment;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;

class PaymentsResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
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
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\ToggleButtons::make('payment_type')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.payment-type'))
                                    ->options(PaymentType::class)
                                    ->default(PaymentType::SEND->value)
                                    ->inline(true),
                                Forms\Components\Select::make('partner_bank_id')
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
                                Forms\Components\Select::make('partner_id')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.customer'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                    )
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Select::make('payment_method_line_id')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.payment-method'))
                                    ->relationship(
                                        'paymentMethodLine',
                                        'name',
                                    )
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('amount')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.amount'))
                                    ->default(0)
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required(),
                                Forms\Components\DatePicker::make('date')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.date'))
                                    ->native(false)
                                    ->default(now())
                                    ->required(),
                                Forms\Components\TextInput::make('memo')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.memo'))
                                    ->maxLength(255),
                            ])->columns(2),
                    ]),
            ])
            ->columns('full');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('accounts::filament/resources/payment.table.columns.name'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.company'))
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('partnerBank.account_holder_name')
                    ->label(__('accounts::filament/resources/payment.table.columns.bank-account-holder'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pairedInternalTransferPayment.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.paired-internal-transfer-payment'))
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethodLine.name')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/payment.table.columns.payment-method-line'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.payment-method'))
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.currency'))
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('partner.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.partner'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('outstandingAccount.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.outstanding-amount'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('destinationAccount.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.destination-account'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.created-by'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('paymentTransaction.name')
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
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company.name')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('partnerBank.account_holder_name')
                            ->label(__('accounts::filament/resources/payment.table.filters.customer-bank-account'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.customer-bank-account'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('pairedInternalTransferPayment.name')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('paymentMethodLine.name')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('paymentMethod.name')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('currency.name')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('partner.name')
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
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('accounts::filament/resources/payment.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('accounts::filament/resources/payment.table.filters.updated-at')),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/payment.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/payment.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('state')
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
                                Infolists\Components\TextEntry::make('payment_type')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.payment-type'))
                                    ->badge()
                                    ->icon(fn (string $state): string => PaymentType::from($state)->getIcon())
                                    ->color(fn (string $state): string => PaymentType::from($state)->getColor())
                                    ->formatStateUsing(fn (string $state): string => PaymentType::from($state)->getLabel()),
                                Infolists\Components\TextEntry::make('partnerBank.account_number')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.customer-bank-account'))
                                    ->icon('heroicon-o-building-library')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('partner.name')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.customer'))
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('paymentMethodLine.name')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-method.entries.payment-method'))
                                    ->icon('heroicon-o-credit-card')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('amount')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-details.entries.amount'))
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('date')
                                    ->icon('heroicon-o-calendar')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-details.entries.date'))
                                    ->placeholder('—')
                                    ->date(),
                                Infolists\Components\TextEntry::make('memo')
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
            'index'  => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayments::route('/create'),
            'view'   => Pages\ViewPayments::route('/{record}'),
            'edit'   => Pages\EditPayments::route('/{record}/edit'),
        ];
    }
}
