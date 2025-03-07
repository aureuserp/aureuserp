<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources;

use Webkul\Account\Filament\Resources\IncoTermResource as BaseIncoTermResource;
use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\IncoTermResource\Pages;
use Webkul\Invoice\Models\Incoterm;

class IncoTermResource extends BaseIncoTermResource
{
    protected static ?string $model = Incoterm::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/configurations/resources/incoterm.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/configurations/resources/incoterm.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('invoices::filament/clusters/configurations/resources/incoterm.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncoTerms::route('/'),
        ];
    }
}
