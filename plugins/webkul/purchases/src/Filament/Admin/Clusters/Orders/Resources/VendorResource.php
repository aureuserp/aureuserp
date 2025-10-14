<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource as BaseVendorResource;
use Webkul\Partner\Filament\Resources\PartnerResource\RelationManagers\AddressesRelationManager;
use Webkul\Partner\Filament\Resources\PartnerResource\RelationManagers\ContactsRelationManager;
use Webkul\Purchase\Filament\Admin\Clusters\Orders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\CreateVendor;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\EditVendor;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ListVendors;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ManageAddresses;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ManageBills;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ManageContacts;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ManagePurchases;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ViewVendor;
use Webkul\Purchase\Models\Partner;

class VendorResource extends BaseVendorResource
{
    use HasCustomFields;

    protected static ?string $model = Partner::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Orders::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/vendor.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewVendor::class,
            EditVendor::class,
            ManageContacts::class,
            ManageAddresses::class,
            ManageBills::class,
            ManagePurchases::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Contacts', [
                ContactsRelationManager::class,
            ])
                ->icon('heroicon-o-users'),

            RelationGroup::make('Addresses', [
                AddressesRelationManager::class,
            ])
                ->icon('heroicon-o-map-pin'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'     => ListVendors::route('/'),
            'create'    => CreateVendor::route('/create'),
            'view'      => ViewVendor::route('/{record}'),
            'edit'      => EditVendor::route('/{record}/edit'),
            'contacts'  => ManageContacts::route('/{record}/contacts'),
            'addresses' => ManageAddresses::route('/{record}/addresses'),
            'bills'     => ManageBills::route('/{record}/bills'),
            'purchases' => ManagePurchases::route('/{record}/purchases'),
        ];
    }
}
