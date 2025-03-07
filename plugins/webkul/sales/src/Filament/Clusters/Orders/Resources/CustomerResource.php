<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource as BaseCustomerResource;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;
use Webkul\Sale\Models\Partner;

class CustomerResource extends BaseCustomerResource
{
    protected static ?string $model = Partner::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

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
            Pages\ViewCustomer::class,
            Pages\EditCustomer::class,
            Pages\ManageContacts::class,
            Pages\ManageAddresses::class,
            Pages\ManageBankAccounts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'        => Pages\ListCustomers::route('/'),
            'create'       => Pages\CreateCustomer::route('/create'),
            'view'         => Pages\ViewCustomer::route('/{record}'),
            'edit'         => Pages\EditCustomer::route('/{record}/edit'),
            'contacts'     => Pages\ManageContacts::route('/{record}/contacts'),
            'addresses'    => Pages\ManageAddresses::route('/{record}/addresses'),
            'bank-account' => Pages\ManageBankAccounts::route('/{record}/bank-accounts'),
        ];
    }
}
