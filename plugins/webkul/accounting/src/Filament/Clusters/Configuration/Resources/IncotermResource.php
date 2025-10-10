<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\IncotermResource\Pages\ManageIncoterms;
use Webkul\Account\Filament\Resources\IncotermResource as BaseIncotermResource;

class IncotermResource extends BaseIncotermResource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/incoterm.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/incoterm.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('accounting::filament/clusters/configurations/resources/incoterm.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageIncoterms::route('/'),
        ];
    }
}
