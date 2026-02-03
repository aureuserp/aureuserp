<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource;
use Webkul\Partner\Filament\Resources\BankAccountResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageBankAccounts extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = CustomerResource::class;

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
                        $data['creator_id'] = Auth::user()->id;

                        return $data;
                    }),
            ]);
    }
}
