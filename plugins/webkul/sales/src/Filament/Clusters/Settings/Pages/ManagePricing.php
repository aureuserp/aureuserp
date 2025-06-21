<?php

namespace Webkul\Sale\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Webkul\Sale\Settings\PriceSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManagePricing extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $slug = 'sale/manage-pricing';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static string $settings = PriceSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('sales::filament/clusters/settings/pages/manage-pricing.breadcrumb'),
        ];
    }

    public function getTitle(): string
    {
        return __('sales::filament/clusters/settings/pages/manage-pricing.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/settings/pages/manage-pricing.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('sales::filament/clusters/settings/pages/manage-pricing.navigation.group');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('enable_discount')
                    ->label(__('sales::filament/clusters/settings/pages/manage-pricing.form.fields.discount'))
                    ->helperText(__('sales::filament/clusters/settings/pages/manage-pricing.form.fields.discount-help')),
                Forms\Components\Toggle::make('enable_margin')
                    ->label(__('sales::filament/clusters/settings/pages/manage-pricing.form.fields.margins'))
                    ->helperText(__('sales::filament/clusters/settings/pages/manage-pricing.form.fields.margins-help')),
            ]);
    }
}
