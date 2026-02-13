<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Invoice\Filament\Clusters\Customers;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\CreateCustomer;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\EditCustomer;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\ListCustomers;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\ManageAddresses;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\ManageBankAccounts;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\ManageContacts;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\ViewCustomer;
use Webkul\Invoice\Models\Customer;
use Webkul\Account\Filament\Resources\CustomerResource as BaseCustomerResource;

class CustomerResource extends BaseCustomerResource
{
    protected static ?string $model = Customer::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = true;

    protected static ?int $navigationSort = 6;

    protected static ?string $cluster = Customers::class;

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
            'index'        => ListCustomers::route('/'),
            'create'       => CreateCustomer::route('/create'),
            'view'         => ViewCustomer::route('/{record}'),
            'edit'         => EditCustomer::route('/{record}/edit'),
            'contacts'     => ManageContacts::route('/{record}/contacts'),
            'addresses'    => ManageAddresses::route('/{record}/addresses'),
            'bank-account' => ManageBankAccounts::route('/{record}/bank-accounts'),
        ];
    }
}
