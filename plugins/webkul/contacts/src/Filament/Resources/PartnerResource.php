<?php

namespace Webkul\Contact\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\CreatePartner;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\EditPartner;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ListPartners;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManageAddresses;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManageBankAccounts;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManageContacts;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ViewPartner;
use Webkul\Contact\Models\Partner;
use Webkul\Partner\Filament\Resources\PartnerResource as BasePartnerResource;
use Webkul\Partner\Filament\Resources\PartnerResource\RelationManagers\AddressesRelationManager;
use Webkul\Partner\Filament\Resources\PartnerResource\RelationManagers\ContactsRelationManager;
use Webkul\PluginManager\Package;
use Webkul\Support\Enums\NavigationGroup;

class PartnerResource extends BasePartnerResource
{
    protected static ?string $model = Partner::class;

    protected static ?string $slug = 'contact/contacts';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('contacts::filament/resources/partner.navigation.title');
    }

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Contact;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = [
            ViewPartner::class,
            EditPartner::class,
            ManageContacts::class,
            ManageAddresses::class,
        ];

        if (Package::isPluginInstalled('accounts')) {
            $items[] = ManageBankAccounts::class;
        }

        return $page->generateNavigationItems($items);
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
        $pages = [
            'index'     => ListPartners::route('/'),
            'create'    => CreatePartner::route('/create'),
            'view'      => ViewPartner::route('/{record}'),
            'edit'      => EditPartner::route('/{record}/edit'),
            'contacts'  => ManageContacts::route('/{record}/contacts'),
            'addresses' => ManageAddresses::route('/{record}/addresses'),
        ];

        if (Package::isPluginInstalled('accounts')) {
            $pages['bank-accounts'] = ManageBankAccounts::route('/{record}/bank-accounts');
        }

        return $pages;
    }
}
