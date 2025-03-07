<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Webkul\Product\Filament\Resources\PackagingResource as BasePackagingResource;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\PackagingResource\Pages;
use Webkul\Sale\Settings\ProductSettings;
use Webkul\Sale\Models\Packaging;

class PackagingResource extends BasePackagingResource
{
    protected static ?string $model = Packaging::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Configuration::class;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(ProductSettings::class)->enable_packagings;
    }

    public static function getNavigationGroup(): string
    {
        return __('Packagings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Products');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePackagings::route('/'),
        ];
    }
}
