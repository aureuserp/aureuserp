<?php

namespace Webkul\Accounting\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Tax;
use Webkul\Account\Settings\TaxesSettings;
use Webkul\Accounting\Models\Invoice;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Support\Models\Country;

class ManageTaxes extends SettingsPage
{
    use HasPageShield;

    protected static ?string $slug = 'accounting/manage-taxes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?int $navigationSort = 9;

    protected static string $settings = TaxesSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_manage_taxes';
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('accounting::filament/clusters/settings/pages/manage-taxes.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('accounting::filament/clusters/settings/pages/manage-taxes.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/settings/pages/manage-taxes.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Text::make(new HtmlString('<strong>'.__('accounting::filament/clusters/settings/pages/manage-taxes.form.default-taxes.label').'</strong><br/>'.__('accounting::filament/clusters/settings/pages/manage-taxes.form.default-taxes.helper-text'))),
                        Select::make('account_sale_tax_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-taxes.form.sales-tax.label'))
                            ->options(Tax::where('type_tax_use', TypeTaxUse::SALE)->get()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                        Select::make('account_purchase_tax_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-taxes.form.purchase-tax.label'))
                            ->options(Tax::where('type_tax_use', TypeTaxUse::PURCHASE)->get()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                    ]),

                Radio::make('tax_calculation_rounding_method')
                    ->label(__('accounting::filament/clusters/settings/pages/manage-taxes.form.rounding-method.label'))
                    ->helperText(__('accounting::filament/clusters/settings/pages/manage-taxes.form.rounding-method.helper-text'))
                    ->options([
                        'round_per_line' => __('accounting::filament/clusters/settings/pages/manage-taxes.form.rounding-method.options.round-per-line'),
                        'round_globally' => __('accounting::filament/clusters/settings/pages/manage-taxes.form.rounding-method.options.round-globally'),
                    ]),

                Select::make('account_price_include')
                    ->label(__('accounting::filament/clusters/settings/pages/manage-taxes.form.prices.label'))
                    ->options(TaxIncludeOverride::class)
                    ->disabled(fn () => Invoice::count() > 0),

                Select::make('account_fiscal_country_id')
                    ->label(__('accounting::filament/clusters/settings/pages/manage-taxes.form.fiscal-country.label'))
                    ->options(Country::all()->pluck('name', 'id'))
                    ->searchable(),
            ]);
    }
}
