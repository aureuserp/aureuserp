<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources;

use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Webkul\Invoice\Filament\Clusters\Customer;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource as BasePartnerResource;
use Webkul\Invoice\Models\Partner;
use Webkul\Partner\Filament\Resources\PartnerResource as BaseVendorResource;

class PartnerResource extends BasePartnerResource
{
    protected static ?string $model = Partner::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $cluster = Customer::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
            Pages\ViewPartner::class,
            Pages\EditPartner::class,
            Pages\ManageContacts::class,
            Pages\ManageAddresses::class,
            Pages\ManageBankAccounts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'        => Pages\ListPartners::route('/'),
            'create'       => Pages\CreatePartner::route('/create'),
            'view'         => Pages\ViewPartner::route('/{record}'),
            'edit'         => Pages\EditPartner::route('/{record}/edit'),
            'contacts'     => Pages\ManageContacts::route('/{record}/contacts'),
            'addresses'    => Pages\ManageAddresses::route('/{record}/addresses'),
            'bank-account' => Pages\ManageBankAccounts::route('/{record}/bank-accounts'),
        ];
    }
}
