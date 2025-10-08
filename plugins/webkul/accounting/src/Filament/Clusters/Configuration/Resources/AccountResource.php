<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\AccountResource\Pages\ManageAccounts;
use Webkul\Account\Filament\Resources\AccountResource as BaseAccountResource;

class AccountResource extends BaseAccountResource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/account.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/account.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('accounting::filament/clusters/configurations/resources/account.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAccounts::route('/'),
        ];
    }
}
