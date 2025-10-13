<?php

namespace Webkul\Sale\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Webkul\Sale\Settings\QuotationAndOrderSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageQuotationAndOrder extends SettingsPage
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $slug = 'sale/manage-quotation-and-order';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static string $settings = QuotationAndOrderSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('sales::filament/clusters/settings/pages/manage-quotation-and-order.breadcrumb'),
        ];
    }

    public function getTitle(): string
    {
        return __('sales::filament/clusters/settings/pages/manage-quotation-and-order.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/settings/pages/manage-quotation-and-order.navigation.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('default_quotation_validity')
                    ->required()
                    ->minValue(0)
                    ->numeric()
                    ->suffix(__('sales::filament/clusters/settings/pages/manage-quotation-and-order.form.fields.validity-suffix'))
                    ->default(30)
                    ->label(__('sales::filament/clusters/settings/pages/manage-quotation-and-order.form.fields.validity'))
                    ->helperText(__('sales::filament/clusters/settings/pages/manage-quotation-and-order.form.fields.validity-help')),
                Toggle::make('enable_lock_confirm_sales')
                    ->label(__('sales::filament/clusters/settings/pages/manage-quotation-and-order.form.fields.lock-confirm-sales'))
                    ->helperText(__('sales::filament/clusters/settings/pages/manage-quotation-and-order.form.fields.lock-confirm-sales-help')),
            ]);
    }
}
