<?php

namespace Webkul\Security\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Webkul\Security\Settings\CurrencySettings;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Support\Models\Currency;

class ManageCurrency extends SettingsPage
{
    use HasPageShield;

    protected static ?string $cluster = Settings::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $settings = CurrencySettings::class;

    public static function getNavigationGroup(): string
    {
        return __('security::filament/clusters/manage-currency.group');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('security::filament/clusters/manage-currency.breadcrumb'),
        ];
    }

    public function getTitle(): string
    {
        return __('security::filament/clusters/manage-currency.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/clusters/manage-currency.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('default_currency_id')
                    ->label(__('security::filament/clusters/manage-currency.form.default-currency.label'))
                    ->helperText(__('security::filament/clusters/manage-currency.form.default-currency.helper-text'))
                    ->options(Currency::all()->pluck('name', 'id'))
                    ->searchable(),
            ]);
    }
}
