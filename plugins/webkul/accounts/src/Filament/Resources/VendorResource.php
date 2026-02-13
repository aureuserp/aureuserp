<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\VendorResource\Pages\CreateVendor;
use Webkul\Account\Filament\Resources\VendorResource\Pages\EditVendor;
use Webkul\Account\Filament\Resources\VendorResource\Pages\ViewVendor;
use Webkul\Account\Filament\Resources\VendorResource\Pages\ListVendors;
use Webkul\Account\Filament\Resources\VendorResource\Pages\ManageAddresses;
use Webkul\Account\Filament\Resources\VendorResource\Pages\ManageBankAccounts;
use Webkul\Account\Filament\Resources\VendorResource\Pages\ManageContacts;
use Webkul\Account\Models\Vendor;
use Webkul\Account\Filament\Resources\PartnerResource;

class VendorResource extends PartnerResource
{
    protected static ?string $model = Vendor::class;

    public static function table(Table $table): Table
    {
        $table = PartnerResource::table($table);

        $table->modifyQueryUsing(fn ($query) => $query->where('supplier_rank', '>', 0));

        return $table;
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
            'index'         => ListVendors::route('/'),
            'create'        => CreateVendor::route('/create'),
            'edit'          => EditVendor::route('/{record}/edit'),
            'view'          => ViewVendor::route('/{record}'),
            'contacts'      => ManageContacts::route('/{record}/contacts'),
            'addresses'     => ManageAddresses::route('/{record}/addresses'),
            'bank-accounts' => ManageBankAccounts::route('/{record}/bank-accounts'),
        ];
    }
}
