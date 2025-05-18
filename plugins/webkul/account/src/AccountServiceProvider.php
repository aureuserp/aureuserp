<?php

namespace Webkul\Account;

use Illuminate\Foundation\AliasLoader;
use Livewire\Livewire;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Livewire\InvoiceSummary;
use Webkul\Support\Console\Commands\InstallCommand;
use Webkul\Support\Console\Commands\UninstallCommand;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class AccountServiceProvider extends PackageServiceProvider
{
    public static string $name = 'accounts';

    public static string $viewNamespace = 'accounts';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2025_01_29_044430_create_accounts_payment_terms_table',
                '2025_01_29_064646_create_accounts_payment_due_terms_table',
                '2025_01_29_134156_create_accounts_incoterms_table',
                '2025_01_29_134157_create_accounts_tax_groups_table',
                '2025_01_30_054952_create_accounts_accounts_table',
                '2025_01_30_061945_create_accounts_account_tags_table',
                '2025_01_30_083208_create_accounts_taxes_table',
                '2025_01_30_123324_create_accounts_tax_partition_lines_table',
                '2025_01_31_073645_create_accounts_journals_table',
                '2025_01_31_095921_create_accounts_journal_accounts_table',
                '2025_01_31_125419_create_accounts_tax_tax_relations_table',
                '2025_02_03_054613_create_accounts_account_taxes_table',
                '2025_02_03_055117_create_accounts_account_account_tags_table',
                '2025_02_03_055709_create_accounts_account_journals_table',
                '2025_02_03_121847_create_accounts_fiscal_positions_table',
                '2025_02_03_131858_create_accounts_fiscal_position_taxes_table',
                '2025_02_03_144139_create_accounts_cash_roundings_table',
                '2025_02_04_104958_create_accounts_product_taxes_table',
                '2025_02_04_111337_create_accounts_product_supplier_taxes_table',
                '2025_02_10_073440_create_accounts_reconciles_table',
                '2025_02_10_075022_create_accounts_payment_methods_table',
                '2025_02_10_075607_create_accounts_payment_method_lines_table',
                '2025_02_11_041318_create_accounts_bank_statements_table',
                '2025_02_11_055302_create_accounts_bank_statement_lines_table',
                '2025_02_11_055302_create_accounts_account_payments_table',
                '2025_02_11_055303_create_accounts_account_moves_table',
                '2025_02_11_071210_create_accounts_account_move_lines_table',
                '2025_02_11_100912_add_move_id_column_to_accounts_bank_statement_lines_table',
                '2025_02_11_115401_create_accounts_full_reconciles_table',
                '2025_02_11_120712_create_accounts_partial_reconciles_table',
                '2025_02_11_121630_add_columns_to_accounts_moves_table',
                '2025_02_11_121635_add_columns_to_accounts_account_payments_table',
                '2025_02_11_121635_add_columns_to_accounts_moves_lines_table',
                '2025_02_17_064828_create_accounts_payment_registers_table',
                '2025_02_17_070121_create_accounts_account_payment_register_move_lines_table',
                '2025_02_24_123300_add_additional_columns_to_partners_partners_table',
                '2025_02_24_124300_create_accounts_accounts_move_line_taxes_table',
                '2025_02_27_112520_create_accounts_accounts_move_reversals_table',
                '2025_02_27_132520_create_accounts_accounts_move_reversal_move_table',
                '2025_02_27_142520_create_accounts_accounts_move_reversal_new_move_table',
                '2025_02_28_142520_create_accounts_accounts_move_payment_table',
                '2025_04_10_053345_alter_accounts_account_moves_table',
                '2025_04_10_053349_alter_accounts_account_move_lines_table',
            ])
            ->runsMigrations()
            ->hasDependencies([
                'products',
            ])
            ->hasSeeder('Webkul\\Account\\Database\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {});
    }

    public function packageBooted(): void
    {
        Livewire::component('invoice-summary', InvoiceSummary::class);
    }

    public function packageRegistered(): void
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('tax', TaxFacade::class);
        $loader->alias('account', AccountFacade::class);

        $this->app->singleton('tax', TaxManager::class);
        $this->app->singleton('account', AccountManager::class);
    }
}
