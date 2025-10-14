<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources;

use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Webkul\Invoice\Filament\Clusters\Customer;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages\CreatePartner;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages\EditPartner;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages\ListPartners;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages\ManageAddresses;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages\ManageBankAccounts;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages\ManageContacts;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages\ViewPartner;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource as BasePartnerResource;
use Webkul\Invoice\Models\Partner;
use Webkul\Partner\Filament\Resources\PartnerResource as BaseVendorResource;

class PartnerResource extends BasePartnerResource
{
    protected static ?string $model = Partner::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 6;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $cluster = Customer::class;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/partners.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/partners.navigation.title');
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

        $table->modifyQueryUsing(fn ($query) => $query->where('sub_type', 'customer'));

        return $table;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPartner::class,
            EditPartner::class,
            ManageContacts::class,
            ManageAddresses::class,
            ManageBankAccounts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'        => ListPartners::route('/'),
            'create'       => CreatePartner::route('/create'),
            'view'         => ViewPartner::route('/{record}'),
            'edit'         => EditPartner::route('/{record}/edit'),
            'contacts'     => ManageContacts::route('/{record}/contacts'),
            'addresses'    => ManageAddresses::route('/{record}/addresses'),
            'bank-account' => ManageBankAccounts::route('/{record}/bank-accounts'),
        ];
    }
}
