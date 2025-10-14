<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource as BaseCustomerResource;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\CreateCustomer;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\EditCustomer;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ListCustomers;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ManageAddresses;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ManageBankAccounts;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ManageContacts;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ViewCustomer;
use Webkul\Sale\Models\Partner;

class CustomerResource extends BaseCustomerResource
{
    protected static ?string $model = Partner::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Orders::class;

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/customer.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/customer.navigation.title');
    }

    public static function table(Table $table): Table
    {
        return BaseCustomerResource::table($table)
            ->contentGrid([
                'sm'  => 1,
                'md'  => 2,
                'xl'  => 3,
                '2xl' => 3,
            ]);
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
