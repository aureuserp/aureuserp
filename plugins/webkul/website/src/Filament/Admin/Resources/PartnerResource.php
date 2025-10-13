<?php

namespace Webkul\Website\Filament\Admin\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Webkul\Partner\Filament\Resources\PartnerResource as BasePartnerResource;
use Webkul\Partner\Filament\Resources\PartnerResource\RelationManagers\AddressesRelationManager;
use Webkul\Partner\Filament\Resources\PartnerResource\RelationManagers\ContactsRelationManager;
use Webkul\Website\Filament\Admin\Resources\PartnerResource\Pages\CreatePartner;
use Webkul\Website\Filament\Admin\Resources\PartnerResource\Pages\EditPartner;
use Webkul\Website\Filament\Admin\Resources\PartnerResource\Pages\ListPartners;
use Webkul\Website\Filament\Admin\Resources\PartnerResource\Pages\ManageAddresses;
use Webkul\Website\Filament\Admin\Resources\PartnerResource\Pages\ManageContacts;
use Webkul\Website\Filament\Admin\Resources\PartnerResource\Pages\ViewPartner;
use Webkul\Website\Models\Partner;

class PartnerResource extends BasePartnerResource
{
    protected static ?string $model = Partner::class;

    protected static ?string $slug = 'website/contacts';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('website::filament/admin/resources/partner.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('website::filament/admin/resources/partner.navigation.group');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPartner::class,
            EditPartner::class,
            ManageContacts::class,
            ManageAddresses::class,
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
            'index'     => ListPartners::route('/'),
            'create'    => CreatePartner::route('/create'),
            'view'      => ViewPartner::route('/{record}'),
            'edit'      => EditPartner::route('/{record}/edit'),
            'contacts'  => ManageContacts::route('/{record}/contacts'),
            'addresses' => ManageAddresses::route('/{record}/addresses'),
        ];
    }
}
