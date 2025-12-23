<?php

namespace Webkul\Accounting\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Webkul\Accounting\Filament\Clusters\Configurations\Resources\PackagingResource;
use Webkul\Account\Settings\CustomerInvoiceSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageCustomerInvoice extends SettingsPage
{
    use HasPageShield;

    protected static ?string $slug = 'accounting/manage-customer-invoice';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?int $navigationSort = 10;

    protected static string $settings = CustomerInvoiceSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('accounting::filament/clusters/settings/pages/manage-customer-invoice.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('accounting::filament/clusters/settings/pages/manage-customer-invoice.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/settings/pages/manage-customer-invoice.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('group_cash_rounding')
                    ->label(__('accounting::filament/clusters/settings/pages/manage-customer-invoice.form.cash-rounding.label'))
                    ->helperText(__('accounting::filament/clusters/settings/pages/manage-customer-invoice.form.cash-rounding.helper-text')),
                Toggle::make('incoterm_id')
                    ->label(__('accounting::filament/clusters/settings/pages/manage-customer-invoice.form.incoterm.label')),
            ]);
    }
}
