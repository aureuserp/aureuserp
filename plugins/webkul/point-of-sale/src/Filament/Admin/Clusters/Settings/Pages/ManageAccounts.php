<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Settings\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use UnitEnum;
use Webkul\Account\Models\Account;
use Webkul\PointOfSale\Settings\AccountSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageAccounts extends SettingsPage
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $slug = 'point-of-sale/manage-accounts';

    protected static string|UnitEnum|null $navigationGroup = 'Point of Sale';

    protected static ?int $navigationSort = 2;

    protected static string $settings = AccountSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_point_of_sale_manage_accounts';
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('point-of-sale::filament/admin/clusters/settings/pages/manage-accounts.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('point-of-sale::filament/admin/clusters/settings/pages/manage-accounts.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/settings/pages/manage-accounts.title');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach (['receivable_account_id', 'stock_output_account_id', 'balancing_account_id'] as $field) {
            $data[$field] = Account::resolveForCompany($data[$field] ?? null, current_company_id());
        }

        return $data;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('receivable_account_id')
                    ->label(__('point-of-sale::filament/admin/clusters/settings/pages/manage-accounts.form.fields.receivable-account'))
                    ->options(fn (): array => Account::query()->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->native(false),

                Select::make('stock_output_account_id')
                    ->label(__('point-of-sale::filament/admin/clusters/settings/pages/manage-accounts.form.fields.stock-output-account'))
                    ->options(fn (): array => Account::query()->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->native(false),

                Select::make('balancing_account_id')
                    ->label(__('point-of-sale::filament/admin/clusters/settings/pages/manage-accounts.form.fields.balancing-account'))
                    ->options(fn (): array => Account::query()->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->native(false),

                Toggle::make('enable_cogs')
                    ->label(__('point-of-sale::filament/admin/clusters/settings/pages/manage-accounts.form.fields.enable-cogs'))
                    ->helperText(__('point-of-sale::filament/admin/clusters/settings/pages/manage-accounts.form.fields.enable-cogs-helper-text')),

                Toggle::make('allow_balancing_line')
                    ->label(__('point-of-sale::filament/admin/clusters/settings/pages/manage-accounts.form.fields.allow-balancing-line')),
            ]);
    }
}
