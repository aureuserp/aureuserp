<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Filament\Resources\AccountResource\Pages\CreateAccount;
use Webkul\Account\Filament\Resources\AccountResource\Pages\EditAccount;
use Webkul\Account\Filament\Resources\AccountResource\Pages\ListAccounts;
use Webkul\Account\Filament\Resources\AccountResource\Pages\ViewAccount;
use Webkul\Account\Models\Account;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->label(__('accounts::filament/resources/account.form.sections.fields.code'))
                            ->maxLength(255)
                            ->columnSpan(1),
                        TextInput::make('name')
                            ->required()
                            ->label(__('accounts::filament/resources/account.form.sections.fields.account-name'))
                            ->maxLength(255)
                            ->columnSpan(1),
                        Fieldset::make(__('accounts::filament/resources/account.form.sections.fields.accounting'))
                            ->schema([
                                Select::make('account_type')
                                    ->options(AccountType::options())
                                    ->preload()
                                    ->required()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.account-type'))
                                    ->live()
                                    ->searchable(),
                                Select::make('invoices_account_tax')
                                    ->relationship('taxes', 'name')
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.default-taxes'))
                                    ->hidden(fn (Get $get) => $get('account_type') === AccountType::OFF_BALANCE->value)
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                                Select::make('invoices_account_account_tags')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.tags'))
                                    ->searchable(),
                                Select::make('invoices_account_journals')
                                    ->relationship('journals', 'name')
                                    ->multiple()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.journals'))
                                    ->preload()
                                    ->searchable(),
                                Select::make('currency_id')
                                    ->relationship('currency', 'name')
                                    ->preload()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.currency'))
                                    ->searchable(),
                                Toggle::make('deprecated')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.deprecated')),
                                Toggle::make('reconcile')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.reconcile')),
                                Toggle::make('non_trade')
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
                TextColumn::make('code')
                    ->searchable()
                    ->label(__('accounts::filament/resources/account.table.columns.code')),
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('accounts::filament/resources/account.table.columns.account-name')),
                TextColumn::make('account_type')
                    ->searchable()
                    ->label(__('accounts::filament/resources/account.table.columns.account-type')),
                TextColumn::make('currency.name')
                    ->searchable()
                    ->label(__('accounts::filament/resources/account.table.columns.currency')),
                IconColumn::make('deprecated')
                    ->boolean()
                    ->label(__('accounts::filament/resources/account.table.columns.deprecated')),
                IconColumn::make('reconcile')
                    ->boolean()
                    ->label(__('accounts::filament/resources/account.table.columns.reconcile')),
                IconColumn::make('non_trade')
                    ->boolean()
                    ->label(__('accounts::filament/resources/account.table.columns.non-trade')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/account.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/account.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/account.table.bulk-options.delete.notification.title'))
                                ->body(__('accounts::filament/resources/account.table.bulk-options.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('code')
                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.code'))
                            ->icon('heroicon-o-identification')
                            ->placeholder('-')
                            ->columnSpan(1),
                        TextEntry::make('name')
                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.account-name'))
                            ->icon('heroicon-o-document-text')
                            ->placeholder('-')
                            ->columnSpan(1),
                        Section::make(__('accounts::filament/resources/account.infolist.sections.entries.accounting'))
                            ->schema([
                                TextEntry::make('account_type')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.account-type'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-tag'),
                                TextEntry::make('taxes.name')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.default-taxes'))
                                    ->visible(fn ($record) => $record->account_type !== AccountType::OFF_BALANCE->value)
                                    ->listWithLineBreaks()
                                    ->placeholder('-')
                                    ->icon('heroicon-o-calculator'),
                                TextEntry::make('tags.name')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.tags'))
                                    ->listWithLineBreaks()
                                    ->placeholder('-')
                                    ->icon('heroicon-o-tag'),
                                TextEntry::make('journals.name')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.journals'))
                                    ->listWithLineBreaks()
                                    ->placeholder('-')
                                    ->icon('heroicon-o-book-open'),
                                TextEntry::make('currency.name')
                                    ->label(__('accounts::filament/resources/account.infolist.sections.entries.currency'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-currency-dollar'),
                                Grid::make(['default' => 3])
                                    ->schema([
                                        IconEntry::make('deprecated')
                                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.deprecated'))
                                            ->placeholder('-'),
                                        IconEntry::make('reconcile')
                                            ->label(__('accounts::filament/resources/account.infolist.sections.entries.reconcile'))
                                            ->placeholder('-'),
                                        IconEntry::make('non_trade')
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
            'index'  => ListAccounts::route('/'),
            'create' => CreateAccount::route('/create'),
            'view'   => ViewAccount::route('/{record}'),
            'edit'   => EditAccount::route('/{record}/edit'),
        ];
    }
}
