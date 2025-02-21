<?php

namespace Webkul\Sale\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Route;
use Webkul\Sale\Settings\ProductSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageProducts extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static string $settings = ProductSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('Product Catalog'),
        ];
    }

    public function getTitle(): string
    {
        return __('Product Catalog');
    }

    public static function getNavigationLabel(): string
    {
        return __('Product Catalog');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('unit_of_measurement')
                    ->label(__('Units of Measure'))
                    ->helperText(function () {
                        return 'Sell and purchase products in different units of measure';
                    }),
            ]);
    }
}
