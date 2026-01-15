<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources;

use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Webkul\Accounting\Filament\Clusters\Customer;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource\Pages\CreateCustomer;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource\Pages\EditCustomer;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource\Pages\ListCustomers;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource\Pages\ManageAddresses;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource\Pages\ManageBankAccounts;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource\Pages\ManageContacts;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource\Pages\ViewCustomer;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource as BasePartnerResource;
use Webkul\Accounting\Models\Customer as CustomerModel;
use Webkul\Partner\Filament\Resources\PartnerResource as BaseVendorResource;

class CustomerResource extends BasePartnerResource
{
    protected static ?string $model = CustomerModel::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 5;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $cluster = Customer::class;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/customers/resources/partners.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/customers/resources/partners.navigation.title');
    }

    public static function table(Table $table): Table
    {
        $table = BaseVendorResource::table($table);

        $table->contentGrid([
            'sm'  => 1,
            'md'  => 2,
            'xl'  => 3,
            '2xl' => 3,
        ]);

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
