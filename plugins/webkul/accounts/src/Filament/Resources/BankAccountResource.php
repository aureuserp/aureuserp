<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\BankAccountResource\Pages\ListBankAccounts;
use Webkul\Partner\Filament\Resources\BankAccountResource as BaseBankAccountResource;

class BankAccountResource extends BaseBankAccountResource
{
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        $schema = BaseBankAccountResource::form($schema);

        $components = collect($schema->getComponents())->forget(1)->all();

        $schema->components($components);

        return $schema;
    }

    public static function table(Table $table): Table
    {
        $table = BaseBankAccountResource::table($table);

        $components = collect($table->getColumns())->forget('can_send_money')->all();

        $table->columns($components);

        return $table;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankAccounts::route('/'),
        ];
    }
}
