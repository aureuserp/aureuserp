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

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $slug = 'sale/manage-pricing';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static string $settings = PriceSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('Manage Pricing'),
        ];
    }

    public function getTitle(): string
    {
        return __('Manage Pricing');
    }

    public static function getNavigationLabel(): string
    {
        return __('Manage Pricing');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('enable_discount')
                    ->label(__('Discounts'))
                    ->helperText(__('Apply discounts to sales order line items.')),
                Forms\Components\Toggle::make('enable_margin')
                    ->label(__('Margins'))
                    ->helperText(__('Show margins on orders.')),
            ]);
    }
}
