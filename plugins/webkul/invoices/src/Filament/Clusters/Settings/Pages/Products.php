<?php

namespace Webkul\Invoice\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Webkul\Invoice\Settings\ProductSettings;
use Webkul\Support\Filament\Clusters\Settings;

class Products extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Invoices';

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
                Forms\Components\Toggle::make('enable_uom')
                    ->label(__('Unit of Measure'))
                    ->helperText(__('Sell and purchase products in different units of measure')),
            ]);
    }
}
