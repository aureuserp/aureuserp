<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Settings\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use UnitEnum;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageProducts extends SettingsPage
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $slug = 'point-of-sale/manage-products';

    protected static string|UnitEnum|null $navigationGroup = 'Point of Sale';

    protected static ?int $navigationSort = 4;

    protected static string $settings = ProductSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_point_of_sale_manage_products';
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('point-of-sale::filament/admin/clusters/settings/pages/manage-products.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('point-of-sale::filament/admin/clusters/settings/pages/manage-products.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/settings/pages/manage-products.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('enable_price_lists')
                    ->label(__('point-of-sale::filament/admin/clusters/settings/pages/manage-products.form.fields.enable-price-lists'))
                    ->helperText(__('point-of-sale::filament/admin/clusters/settings/pages/manage-products.form.fields.enable-price-lists-helper-text')),
            ]);
    }
}
