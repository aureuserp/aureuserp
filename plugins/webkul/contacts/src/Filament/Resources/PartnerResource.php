<?php

namespace Webkul\Contact\Filament\Resources;

use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages;
use Webkul\Partner\Filament\Resources\PartnerResource as BasePartnerResource;
use Webkul\Partner\Filament\Resources\PartnerResource\RelationManagers;
use Webkul\Partner\Models\Partner;

class PartnerResource extends BasePartnerResource
{
    protected static ?string $model = Partner::class;

    protected static ?string $slug = 'contact/contacts';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('contacts::filament/resources/partner.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('contacts::filament/resources/partner.navigation.group');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPartner::class,
            Pages\EditPartner::class,
            Pages\ManageContacts::class,
            Pages\ManageAddresses::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Contacts', [
                RelationManagers\ContactsRelationManager::class,
            ])
                ->icon('heroicon-o-users'),

            RelationGroup::make('Addresses', [
                RelationManagers\AddressesRelationManager::class,
            ])
                ->icon('heroicon-o-map-pin'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'     => Pages\ListPartners::route('/'),
            'create'    => Pages\CreatePartner::route('/create'),
            'view'      => Pages\ViewPartner::route('/{record}'),
            'edit'      => Pages\EditPartner::route('/{record}/edit'),
            'contacts'  => Pages\ManageContacts::route('/{record}/contacts'),
            'addresses' => Pages\ManageAddresses::route('/{record}/addresses'),
        ];
    }
}
