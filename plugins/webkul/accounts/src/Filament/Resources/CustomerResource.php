<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\CustomerResource\Pages\CreateCustomer;
use Webkul\Account\Filament\Resources\CustomerResource\Pages\EditCustomer;
use Webkul\Account\Filament\Resources\CustomerResource\Pages\ViewCustomer;
use Webkul\Account\Filament\Resources\CustomerResource\Pages\ListCustomers;
use Webkul\Account\Filament\Resources\CustomerResource\Pages\ManageAddresses;
use Webkul\Account\Filament\Resources\CustomerResource\Pages\ManageBankAccounts;
use Webkul\Account\Filament\Resources\CustomerResource\Pages\ManageContacts;
use Webkul\Account\Models\Customer;
use Webkul\Account\Filament\Resources\PartnerResource;

class CustomerResource extends PartnerResource
{
    protected static ?string $model = Customer::class;

    public static function table(Table $table): Table
    {
        $table = PartnerResource::table($table);

        $table->modifyQueryUsing(fn ($query) => $query->where('customer_rank', '>', 0));

        return $table;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewCustomer::class,
            EditCustomer::class,
            ManageContacts::class,
            ManageAddresses::class,
            ManageBankAccounts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'         => ListCustomers::route('/'),
            'create'        => CreateCustomer::route('/create'),
            'edit'          => EditCustomer::route('/{record}/edit'),
            'view'          => ViewCustomer::route('/{record}'),
            'contacts'      => ManageContacts::route('/{record}/contacts'),
            'addresses'     => ManageAddresses::route('/{record}/addresses'),
            'bank-accounts' => ManageBankAccounts::route('/{record}/bank-accounts'),
        ];
    }
}
