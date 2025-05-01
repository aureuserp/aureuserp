<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Filament\Resources\AccountResource\Pages;
use Webkul\Account\Models\Account;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->label(__('accounts::filament/resources/account.form.sections.fields.code'))
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label(__('accounts::filament/resources/account.form.sections.fields.account-name'))
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\Fieldset::make(__('accounts::filament/resources/account.form.sections.fields.accounting'))
                            ->schema([
                                Forms\Components\Select::make('account_type')
                                    ->options(AccountType::options())
                                    ->preload()
                                    ->required()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.account-type'))
                                    ->live()
                                    ->searchable(),
                                Forms\Components\Select::make('invoices_account_tax')
                                    ->relationship('taxes', 'name')
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.default-taxes'))
                                    ->hidden(fn (Get $get) => $get('account_type') === AccountType::OFF_BALANCE->value)
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                                Forms\Components\Select::make('invoices_account_account_tags')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.tags'))
                                    ->searchable(),
                                Forms\Components\Select::make('invoices_account_journals')
                                    ->relationship('journals', 'name')
                                    ->multiple()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.journals'))
                                    ->preload()
                                    ->searchable(),
                                Forms\Components\Select::make('currency_id')
                                    ->relationship('currency', 'name')
                                    ->preload()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.currency'))
                                    ->searchable(),
                                Forms\Components\Toggle::make('deprecated')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.deprecated')),
                                Forms\Components\Toggle::make('reconcile')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.reconcile')),
                                Forms\Components\Toggle::make('non_trade')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.non-trade')),
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->label(__('accounts::filament/resources/account.table.columns.code')),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('accounts::filament/resources/account.table.columns.account-name')),
                Tables\Columns\TextColumn::make('account_type')
                    ->searchable()
                    ->label(__('accounts::filament/resources/account.table.columns.account-type')),
                Tables\Columns\TextColumn::make('currency.name')
                    ->searchable()
                    ->label(__('accounts::filament/resources/account.table.columns.currency')),
                Tables\Columns\IconColumn::make('deprecated')
                    ->boolean()
                    ->label(__('accounts::filament/resources/account.table.columns.deprecated')),
                Tables\Columns\IconColumn::make('reconcile')
                    ->boolean()
                    ->label(__('accounts::filament/resources/account.table.columns.reconcile')),
                Tables\Columns\IconColumn::make('non_trade')
                    ->boolean()
                    ->label(__('accounts::filament/resources/account.table.columns.non-trade')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/account.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/account.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/account.table.bulk-options.delete.notification.title'))
                                ->body(__('accounts::filament/resources/account.table.bulk-options.delete.notification.body'))
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
                        Infolists\Components\TextEntry::make('code')
                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.code'))
                            ->icon('heroicon-o-identification')
                            ->placeholder('-')
                            ->columnSpan(1),
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.account-name'))
                            ->icon('heroicon-o-document-text')
                            ->placeholder('-')
                            ->columnSpan(1),
                        Infolists\Components\Section::make(__('accounts::filament/resources/account.infolist.sections.entries.accounting'))
                            ->schema([
                                Infolists\Components\TextEntry::make('account_type')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.account-type'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-tag'),
                                Infolists\Components\TextEntry::make('taxes.name')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.default-taxes'))
                                    ->visible(fn ($record) => $record->account_type !== AccountType::OFF_BALANCE->value)
                                    ->listWithLineBreaks()
                                    ->placeholder('-')
                                    ->icon('heroicon-o-calculator'),
                                Infolists\Components\TextEntry::make('tags.name')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.tags'))
                                    ->listWithLineBreaks()
                                    ->placeholder('-')
                                    ->icon('heroicon-o-tag'),
                                Infolists\Components\TextEntry::make('journals.name')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.journals'))
                                    ->listWithLineBreaks()
                                    ->placeholder('-')
                                    ->icon('heroicon-o-book-open'),
                                Infolists\Components\TextEntry::make('currency.name')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.currency'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-currency-dollar'),
                                Infolists\Components\Grid::make(['default' => 3])
                                    ->schema([
                                        Infolists\Components\IconEntry::make('deprecated')
                                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.deprecated'))
                                            ->placeholder('-'),
                                        Infolists\Components\IconEntry::make('reconcile')
                                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.reconcile'))
                                            ->placeholder('-'),
                                        Infolists\Components\IconEntry::make('non_trade')
                                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.non-trade'))
                                            ->placeholder('-'),
                                    ]),
                            ])
                            ->columns(2),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'view'   => Pages\ViewAccount::route('/{record}'),
            'edit'   => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
