<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Filament\Resources\AccountResource\Pages\ManageAccounts;
use Webkul\Account\Filament\Resources\AccountResource\Schemas\AccountForm;
use Webkul\Account\Filament\Resources\AccountResource\Schemas\AccountInfolist;
use Webkul\Account\Filament\Resources\AccountResource\Tables\AccountsTable;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Field\Filament\Traits\HasCustomFields;

class AccountResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Account::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/account.global-search.code') => $record->code ?? '—',
            __('accounts::filament/resources/account.global-search.type') => $record->account_type ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return AccountForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return AccountsTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return AccountInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAccounts::route('/'),
        ];
    }

    public static function suggestJournalIdsForAccountType(?string $accountType): array
    {
        if (! $accountType) {
            return [];
        }

        $journalType = match ($accountType) {
            AccountType::INCOME->value,
            AccountType::INCOME_OTHER->value,
            AccountType::ASSET_RECEIVABLE->value       => JournalType::SALE,
            AccountType::EXPENSE->value,
            AccountType::EXPENSE_DEPRECIATION->value,
            AccountType::EXPENSE_DIRECT_COST->value,
            AccountType::LIABILITY_PAYABLE->value      => JournalType::PURCHASE,
            AccountType::ASSET_CASH->value             => JournalType::CASH,
            AccountType::LIABILITY_CREDIT_CARD->value  => JournalType::CREDIT_CARD,
            default                                    => null,
        };

        if (! $journalType) {
            return [];
        }

        return Journal::query()
            ->where('type', $journalType->value)
            ->pluck('id')
            ->all();
    }
}
