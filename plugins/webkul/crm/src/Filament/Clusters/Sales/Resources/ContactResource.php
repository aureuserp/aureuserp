<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Webkul\Contact\Filament\Resources\PartnerResource as BasePartnerResource;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\CreateContact;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\EditContact;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\ListContacts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\ManageAddresses;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\ManageBankAccounts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\ManageContacts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages\ViewContact;
use Webkul\Crm\Filament\Concerns\HasCrmAuthorization;
use Webkul\Crm\Policies\ContactPolicy;
use Webkul\PluginManager\Package;

class ContactResource extends BasePartnerResource
{
    use HasCrmAuthorization;

    protected static ?string $slug = 'contacts';

    protected static ?string $cluster = Sales::class;

    protected static ?int $navigationSort = 4;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static bool $isGloballySearchable = false;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function getCrmPolicy(): string
    {
        return ContactPolicy::class;
    }

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/contact.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/contact.navigation.title');
    }

    /**
     * The Contacts plugin pins its resource to the global Contact navigation group,
     * so the group is cleared here to list Contacts directly under CRM → Sales.
     */
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
            ViewContact::class,
            EditContact::class,
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
            'index'     => ListContacts::route('/'),
            'create'    => CreateContact::route('/create'),
            'view'      => ViewContact::route('/{record}'),
            'edit'      => EditContact::route('/{record}/edit'),
            'contacts'  => ManageContacts::route('/{record}/contacts'),
            'addresses' => ManageAddresses::route('/{record}/addresses'),
        ];

        if (Package::isPluginInstalled('accounts')) {
            $pages['bank-accounts'] = ManageBankAccounts::route('/{record}/bank-accounts');
        }

        return $pages;
    }
}
