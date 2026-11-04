<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Contact\Filament\Resources\PartnerResource as BasePartnerResource;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\CreateCustomer;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\EditCustomer;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\ListCustomers;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\ManageAddresses;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\ManageBankAccounts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\ManageContacts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\ViewCustomer;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Schemas\CustomerForm;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Schemas\CustomerInfolist;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Tables\CustomersTable;
use Webkul\Crm\Filament\Concerns\HasCRMAuthorization;
use Webkul\Crm\Policies\CustomerPolicy;
use Webkul\PluginManager\Package;

class CustomerResource extends BasePartnerResource
{
    use HasCRMAuthorization;

    protected static ?string $slug = 'customers';

    protected static ?string $cluster = Sales::class;

    protected static ?int $navigationSort = 5;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static bool $isGloballySearchable = false;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function getCRMPolicy(): string
    {
        return CustomerPolicy::class;
    }

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/customer.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/customer.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/customer.navigation.title');
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure(parent::table($table));
    }

    public static function form(Schema $schema): Schema
    {
        return CustomerForm::configure(parent::form($schema));
    }

    public static function infolist(Schema $schema): Schema
    {
        return CustomerInfolist::configure(parent::infolist($schema));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationItems(): array
    {
        return array_map(
            fn ($item) => $item->group(null),
            parent::getNavigationItems(),
        );
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = [
            ViewCustomer::class,
            EditCustomer::class,
            ManageContacts::class,
            ManageAddresses::class,
        ];

        if (Package::isPluginInstalled('accounts')) {
            $items[] = ManageBankAccounts::class;
        }

        return $page->generateNavigationItems($items);
    }

    public static function getPages(): array
    {
        $pages = [
            'index'     => ListCustomers::route('/'),
            'create'    => CreateCustomer::route('/create'),
            'view'      => ViewCustomer::route('/{record}'),
            'edit'      => EditCustomer::route('/{record}/edit'),
            'contacts'  => ManageContacts::route('/{record}/contacts'),
            'addresses' => ManageAddresses::route('/{record}/addresses'),
        ];

        if (Package::isPluginInstalled('accounts')) {
            $pages['bank-accounts'] = ManageBankAccounts::route('/{record}/bank-accounts');
        }

        return $pages;
    }
}
