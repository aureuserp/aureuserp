<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource;
use Webkul\Partner\Filament\Resources\BankAccountResource;

class ManageBankAccounts extends ManageRelatedRecords
{
    protected static string $resource = PartnerResource::class;

    protected static string $relationship = 'bankAccounts';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationLabel(): string
    {
        return __('Bank Accounts');
    }

    public function form(Schema $schema): Schema
    {
        return BankAccountResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return BankAccountResource::table($table)
            ->headerActions([
                CreateAction::make()
                    ->label(__('New Bank Account'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['creator_id'] = filament()->auth()->user()->id;

                        return $data;
                    }),
            ]);
    }
}
