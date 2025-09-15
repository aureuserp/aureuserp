<?php

namespace Webkul\Sale\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Webkul\Sale\Settings\ProductSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageProducts extends SettingsPage
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $slug = 'sale/manage-products';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    protected static string $settings = ProductSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('sales::filament/clusters/settings/pages/manage-products.breadcrumb'),
        ];
    }

    public function getTitle(): string
    {
        return __('sales::filament/clusters/settings/pages/manage-products.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/settings/pages/manage-products.navigation.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('enable_variants')
                    ->label(__('sales::filament/clusters/settings/pages/manage-products.form.fields.variants'))
                    ->helperText(__('sales::filament/clusters/settings/pages/manage-products.form.fields.variants-help')),
                Toggle::make('enable_uom')
                    ->label(__('sales::filament/clusters/settings/pages/manage-products.form.fields.uom'))
                    ->helperText(__('sales::filament/clusters/settings/pages/manage-products.form.fields.uom-help')),
                Toggle::make('enable_packagings')
                    ->label(__('sales::filament/clusters/settings/pages/manage-products.form.fields.packagings'))
                    ->helperText(__('sales::filament/clusters/settings/pages/manage-products.form.fields.packagings-help')),
                // Forms\Components\Toggle::make('enable_deliver_content_by_email')
                //     ->label(__('sales::filament/clusters/settings/pages/manage-products.form.fields.deliver-content-by-email'))
                //     ->helperText(__('sales::filament/clusters/settings/pages/manage-products.form.fields.deliver-content-by-email-help')),
            ]);
    }
}
