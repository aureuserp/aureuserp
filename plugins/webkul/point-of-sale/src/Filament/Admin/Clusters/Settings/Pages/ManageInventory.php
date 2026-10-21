<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Settings\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Radio;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use UnitEnum;
use Webkul\PointOfSale\Enums\StockUpdateMode;
use Webkul\PointOfSale\Settings\InventorySettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageInventory extends SettingsPage
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $slug = 'point-of-sale/manage-inventory';

    protected static string|UnitEnum|null $navigationGroup = 'Point of Sale';

    protected static ?int $navigationSort = 3;

    protected static string $settings = InventorySettings::class;

    protected static ?string $cluster = Settings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_point_of_sale_manage_inventory';
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('point-of-sale::filament/admin/clusters/settings/pages/manage-inventory.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('point-of-sale::filament/admin/clusters/settings/pages/manage-inventory.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/settings/pages/manage-inventory.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Radio::make('stock_update_mode')
                    ->label(__('point-of-sale::filament/admin/clusters/settings/pages/manage-inventory.form.fields.stock-update-mode'))
                    ->helperText(__('point-of-sale::filament/admin/clusters/settings/pages/manage-inventory.form.fields.stock-update-mode-helper-text'))
                    ->options(StockUpdateMode::class)
                    ->default(StockUpdateMode::REAL_TIME->value)
                    ->enum(StockUpdateMode::class)
                    ->required(),
            ]);
    }
}
