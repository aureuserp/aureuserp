<?php

namespace Webkul\Sale\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Webkul\Sale\Settings\ProductSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageProducts extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $slug = 'sale/manage-products';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    protected static string $settings = ProductSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('Manage Products'),
        ];
    }

    public function getTitle(): string
    {
        return __('Manage Products');
    }

    public static function getNavigationLabel(): string
    {
        return __('Manage Products');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('enable_variants')
                    ->label(__('Variants'))
                    ->helperText(__('Set product attributes (e.g. color, size) to manage variants')),
                Forms\Components\Toggle::make('enable_uom')
                    ->label(__('Unit of Measure'))
                    ->helperText(__('Sell and purchase products in different units of measure')),
                Forms\Components\Toggle::make('enable_packagings')
                    ->label(__('Packagings')),
                Forms\Components\Toggle::make('enable_deliver_content_by_email')
                    ->label(__('Deliver Content by Email'))
                    ->helperText('Send a product-specific email once the invoice is validated'),
            ]);
    }
}
