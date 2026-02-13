<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Webkul\Accounting\Filament\Clusters\Vendors;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\CreateVendor;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\EditVendor;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ListVendors;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageAddresses;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageBankAccounts;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageContacts;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ViewVendor;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\RelationManagers\BankAccountsRelationManager;
use Webkul\Accounting\Models\Vendor;
use Webkul\Account\Filament\Resources\VendorResource as BaseVendorResource;

class VendorResource extends BaseVendorResource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $slug = '';

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = true;

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Vendors::class;

    public static function getRelations(): array
    {
        $table = parent::getRelations();

        return [
            ...$table,
            RelationGroup::make('Bank Accounts', [
                BankAccountsRelationManager::class,
            ])
                ->icon('heroicon-o-banknotes'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewVendor::class,
            EditVendor::class,
            ManageContacts::class,
            ManageAddresses::class,
            ManageBankAccounts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'        => ListVendors::route('/'),
            'create'       => CreateVendor::route('/create'),
            'edit'         => EditVendor::route('/{record}/edit'),
            'view'         => ViewVendor::route('/{record}'),
            'contacts'     => ManageContacts::route('/{record}/contacts'),
            'addresses'    => ManageAddresses::route('/{record}/addresses'),
            'bank-account' => ManageBankAccounts::route('/{record}/bank-accounts'),
        ];
    }
}
