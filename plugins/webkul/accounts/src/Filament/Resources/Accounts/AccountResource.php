<?php

namespace Webkul\Account\Filament\Resources\Accounts;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\Accounts\Pages\CreateAccount;
use Webkul\Account\Filament\Resources\Accounts\Pages\EditAccount;
use Webkul\Account\Filament\Resources\Accounts\Pages\ListAccounts;
use Webkul\Account\Filament\Resources\Accounts\Pages\ViewAccount;
use Webkul\Account\Filament\Resources\Accounts\Schemas\AccountForm;
use Webkul\Account\Filament\Resources\Accounts\Schemas\AccountInfolist;
use Webkul\Account\Filament\Resources\Accounts\Tables\AccountsTable;
use Webkul\Account\Models\Account;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Schema $schema): Schema
    {
        return AccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccountsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AccountInfolist::configure($schema);
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
