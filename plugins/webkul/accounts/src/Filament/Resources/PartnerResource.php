<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\CreatePartner;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\EditPartner;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ListPartners;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ManageAddresses;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ManageBankAccounts;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ManageContacts;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ViewPartner;
use Webkul\Account\Filament\Resources\PartnerResource\RelationManagers\BankAccountsRelationManager;
use Webkul\Account\Models\Partner;
use Webkul\Partner\Filament\Resources\PartnerResource as BasePartnerResource;

class PartnerResource extends BasePartnerResource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $model = Partner::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function table(Table $table): Table
    {
        $table = parent::table($table);

        $table->contentGrid([
            'sm'  => 1,
            'md'  => 2,
            'xl'  => 3,
            '2xl' => 3,
        ]);

        return $table;
    }

    public static function getRelations(): array
    {
        return [
            ...parent::getRelations(),
            RelationGroup::make('Bank Accounts', [
                BankAccountsRelationManager::class,
            ])
                ->icon('heroicon-o-banknotes'),
        ];
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
            'index'         => ListPartners::route('/'),
            'create'        => CreatePartner::route('/create'),
            'edit'          => EditPartner::route('/{record}/edit'),
            'view'          => ViewPartner::route('/{record}'),
            'contacts'      => ManageContacts::route('/{record}/contacts'),
            'addresses'     => ManageAddresses::route('/{record}/addresses'),
            'bank-accounts' => ManageBankAccounts::route('/{record}/bank-accounts'),
        ];
    }
}
