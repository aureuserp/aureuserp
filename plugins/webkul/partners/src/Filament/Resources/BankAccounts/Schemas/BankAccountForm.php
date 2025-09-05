<?php

namespace Webkul\Partner\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Partner\Filament\Resources\Banks\BankResource;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_number')
                    ->label(__('partners::filament/resources/bank-account.form.account-number'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Toggle::make('can_send_money')
                    ->label(__('partners::filament/resources/bank-account.form.can-send-money'))
                    ->inline(false),
                Select::make('bank_id')
                    ->label(__('partners::filament/resources/bank-account.form.bank'))
                    ->relationship(
                        'bank',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($label) {
                        return str_contains($label, ' (Deleted)');
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm(fn (Schema $schema) => BankResource::form($schema)),
                Select::make('partner_id')
                    ->label(__('partners::filament/resources/bank-account.form.account-holder'))
                    ->relationship('partner', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }
}
