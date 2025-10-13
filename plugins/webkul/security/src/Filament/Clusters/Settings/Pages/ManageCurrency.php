<?php

namespace Webkul\Security\Filament\Clusters\Settings\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Webkul\Security\Settings\CurrencySettings;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Support\Models\Currency;

class ManageCurrency extends SettingsPage
{
    use HasPageShield;

    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('default_currency_id')
                    ->label(__('security::filament/clusters/manage-currency.form.default-currency.label'))
                    ->helperText(__('security::filament/clusters/manage-currency.form.default-currency.helper-text'))
                    ->options(Currency::all()->pluck('name', 'id'))
                    ->searchable(),
            ]);
    }
}
