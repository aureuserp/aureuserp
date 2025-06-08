<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\BankAccountResource\Pages;
use Webkul\Partner\Filament\Resources\BankAccountResource as BaseBankAccountResource;

class BankAccountResource extends BaseBankAccountResource
{
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        $form = BaseBankAccountResource::form($form);

        $components = collect($form->getComponents())->forget(1)->all();

        $form->components($components);

        return $form;
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
            'index' => Pages\ListBankAccounts::route('/'),
        ];
    }
}
