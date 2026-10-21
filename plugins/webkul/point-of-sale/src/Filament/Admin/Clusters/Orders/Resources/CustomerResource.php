<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources;

use BackedEnum;
use Webkul\Account\Filament\Resources\CustomerResource as BaseCustomerResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages\CreateCustomer;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages\EditCustomer;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages\ListCustomers;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages\ManageAddresses;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages\ManageBankAccounts;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages\ManageContacts;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages\ViewCustomer;
use Webkul\PointOfSale\Models\Customer;

class CustomerResource extends BaseCustomerResource
{
    protected static ?string $model = Customer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = Orders::class;

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/orders/resources/customer.navigation.title');
    }

    public static function getPages(): array
    {
        return [
            'index'         => ListCustomers::route('/'),
            'create'        => CreateCustomer::route('/create'),
            'view'          => ViewCustomer::route('/{record}'),
            'edit'          => EditCustomer::route('/{record}/edit'),
            'contacts'      => ManageContacts::route('/{record}/contacts'),
            'addresses'     => ManageAddresses::route('/{record}/addresses'),
            'bank-accounts' => ManageBankAccounts::route('/{record}/bank-accounts'),
        ];
    }
}
