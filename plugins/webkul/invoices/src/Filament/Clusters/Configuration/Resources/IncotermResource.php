<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources;

use Webkul\Account\Filament\Resources\IncotermResource as BaseIncotermResource;
use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\IncotermResource\Pages\ManageIncoterms;
use Webkul\Invoice\Models\Incoterm;

class IncotermResource extends BaseIncotermResource
{
    protected static ?string $model = Incoterm::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/configurations/resources/incoterm.model-label');
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
            'index' => ManageIncoterms::route('/'),
        ];
    }
}
