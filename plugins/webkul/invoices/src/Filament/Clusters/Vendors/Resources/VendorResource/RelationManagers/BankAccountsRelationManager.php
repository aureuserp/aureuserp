<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Partner\Filament\Resources\BankAccountResource;

class BankAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'bankAccounts';

    public function form(Schema $schema): Schema
    {
        return BankAccountResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return BankAccountResource::table($table)
            ->headerActions([
                CreateAction::make()
                    ->label(__('invoices::filament/clusters/vendors/resources/vendor/relation-manager/bank-account-relation-manager.create-bank-account'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        return $data;
                    }),
            ]);
    }
}
