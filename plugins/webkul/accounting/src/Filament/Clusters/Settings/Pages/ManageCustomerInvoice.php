<?php

namespace Webkul\Accounting\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Webkul\Account\Settings\CustomerInvoiceSettings;
use Webkul\Accounting\Models\Incoterm;
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

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_manage_customer_invoice';
    }

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
                Select::make('incoterm_id')
                    ->label(__('accounting::filament/clusters/settings/pages/manage-customer-invoice.form.incoterm.label'))
                    ->options(Incoterm::all()->pluck('name', 'id'))
                    ->inlineLabel()
                    ->searchable(),
            ]);
    }
}
