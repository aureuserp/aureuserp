<?php

namespace Webkul\Account;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Foundation\AliasLoader;
use Livewire\Livewire;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Filament\Resources\PartnerResource\Schemas\AccountPartnerSchema;
use Webkul\Account\Filament\Resources\ProductResource\Schemas\AccountProductSchema;
use Webkul\Account\Livewire\InvoiceSummary;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\PaymentMethodLine;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Account\Models\Tax;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\Partner\Filament\Resources\PartnerResource\Support\PartnerSchemaRegistry;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry;
use Webkul\Product\Models\Product;

class AccountServiceProvider extends PackageServiceProvider
{
    public static string $name = 'accounts';

    public static string $viewNamespace = 'accounts';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasRoutes(['api'])
            ->hasMigrations([
                '2025_01_29_044430_create_accounts_payment_terms_table',
                '2025_01_29_064646_create_accounts_payment_due_terms_table',
                '2025_01_29_134156_create_accounts_incoterms_table',
                '2025_01_29_134157_create_accounts_tax_groups_table',
                '2025_01_30_054952_create_accounts_accounts_table',
                '2025_01_30_054955_create_accounts_account_companies_table',
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
                '2025_02_03_131860_create_accounts_fiscal_position_accounts_table',
                '2025_02_03_144139_create_accounts_cash_roundings_table',
                '2025_02_04_082243_alter_products_products_table',
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
                '2025_08_11_043945_alter_accounts_reconciles_table',
                '2025_08_11_044151_alter_accounts_payments_methods_table',
                '2025_08_11_044258_alter_accounts_bank_statements_table',
                '2025_08_11_044445_alter_accounts_account_payments_table',
                '2025_08_11_044603_alter_accounts_bank_statement_lines_table',
                '2025_08_11_044842_alter_accounts_account_move_lines_table',
                '2025_08_11_044931_alter_accounts_partial_reconciles_table',
                '2025_08_04_062050_alter_accounts_taxes_table',
                '2025_08_01_091957_alter_accounts_payment_terms_table',
                '2025_10_23_082243_alter_products_categories_table',
                '2025_11_19_081920_alter_accounts_account_move_lines_table',
                '2025_12_09_103848_alter_accounts_payment_method_lines_table',
                '2025_12_16_074557_add_journal_id_in_accounts_accounts_move_reversals_table',
                '2026_01_15_060822_backfill_customer_and_supplier_rank_in_partners_table',
                '2026_02_16_063000_alter_partners_partners_table',
                '2026_02_25_044931_alter_accounts_full_reconciles_table',
                '2026_03_03_120000_alter_accounts_journals_bank_account_foreign_key',
                '2026_04_17_000001_add_parent_id_to_accounts_accounts_table',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2025_12_02_094021_create_accounts_default_accounts_settings',
                '2025_12_02_094021_create_accounts_taxes_settings',
                '2025_12_02_094021_create_customer_invoice_settings',
            ])
            ->runsSettings()
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

        $this->registerCustomCss();

        $this->contributeProductSchema();

        $this->contributePartnerSchema();
    }

    protected function contributePartnerSchema(): void
    {
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        PartnerSchemaRegistry::form('sales.fields', fn () => AccountPartnerSchema::salesFields());
        PartnerSchemaRegistry::form('salesPurchase.append', fn () => AccountPartnerSchema::salesPurchaseAppend());
        PartnerSchemaRegistry::form('tabs.append', fn () => AccountPartnerSchema::invoicingTab(), 10);
        PartnerSchemaRegistry::form('tabs.append', fn () => AccountPartnerSchema::internalNotesTab(), 20);

        PartnerSchemaRegistry::infolist('sales.fields', fn () => AccountPartnerSchema::salesEntries());
        PartnerSchemaRegistry::infolist('salesPurchase.append', fn () => AccountPartnerSchema::salesPurchaseAppendInfolist());
        PartnerSchemaRegistry::infolist('tabs.append', fn () => AccountPartnerSchema::invoicingTabInfolist(), 10);
        PartnerSchemaRegistry::infolist('tabs.append', fn () => AccountPartnerSchema::internalNotesTabInfolist(), 20);

        Partner::contributeFillable([
            'message_bounce',
            'supplier_rank',
            'customer_rank',
            'invoice_warning',
            'autopost_bills',
            'credit_limit',
            'ignore_abnormal_invoice_date',
            'ignore_abnormal_invoice_amount',
            'invoice_sending_method',
            'invoice_edi_format_store',
            'trust',
            'invoice_warn_msg',
            'debit_limit',
            'peppol_endpoint',
            'peppol_eas',
            'sale_warn',
            'comment',
            'sale_warn_msg',
            'property_account_payable_id',
            'property_account_receivable_id',
            'property_account_position_id',
            'property_payment_term_id',
            'property_supplier_payment_term_id',
            'property_outbound_payment_method_line_id',
            'property_inbound_payment_method_line_id',
        ]);

        Partner::resolveRelationUsing('propertyAccountPayable', fn (Partner $partner) => $partner->belongsTo(Account::class, 'property_account_payable_id'));
        Partner::resolveRelationUsing('propertyAccountReceivable', fn (Partner $partner) => $partner->belongsTo(Account::class, 'property_account_receivable_id'));
        Partner::resolveRelationUsing('propertyAccountPosition', fn (Partner $partner) => $partner->belongsTo(FiscalPosition::class, 'property_account_position_id'));
        Partner::resolveRelationUsing('propertyPaymentTerm', fn (Partner $partner) => $partner->belongsTo(PaymentTerm::class, 'property_payment_term_id'));
        Partner::resolveRelationUsing('propertySupplierPaymentTerm', fn (Partner $partner) => $partner->belongsTo(PaymentTerm::class, 'property_supplier_payment_term_id'));
        Partner::resolveRelationUsing('propertyOutboundPaymentMethodLine', fn (Partner $partner) => $partner->belongsTo(PaymentMethodLine::class, 'property_outbound_payment_method_line_id'));
        Partner::resolveRelationUsing('propertyInboundPaymentMethodLine', fn (Partner $partner) => $partner->belongsTo(PaymentMethodLine::class, 'property_inbound_payment_method_line_id'));
    }

    protected function contributeProductSchema(): void
    {
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        ProductSchemaRegistry::form('right.pricing.fields', fn () => AccountProductSchema::taxFields());
        ProductSchemaRegistry::form('left.append', fn () => AccountProductSchema::policySection());
        ProductSchemaRegistry::form('hidden', fn () => AccountProductSchema::hiddenFields());
        ProductSchemaRegistry::eagerLoad(['productTaxes', 'supplierTaxes']);

        Product::contributeFillable([
            'property_account_income_id',
            'property_account_expense_id',
            'image',
            'service_type',
            'sale_line_warn',
            'expense_policy',
            'invoice_policy',
            'sale_line_warn_msg',
            'sales_ok',
            'purchase_ok',
        ]);

        Product::resolveRelationUsing('productTaxes', fn (Product $product) => $product->belongsToMany(
            Tax::class,
            'accounts_product_taxes',
            'product_id',
            'tax_id',
        ));

        Product::resolveRelationUsing('supplierTaxes', fn (Product $product) => $product->belongsToMany(
            Tax::class,
            'accounts_product_supplier_taxes',
            'product_id',
            'tax_id',
        ));

        Product::resolveRelationUsing('propertyAccountIncome', fn (Product $product) => $product->belongsTo(
            Account::class,
            'property_account_income_id',
        )
            ->where('deprecated', false)
            ->whereNotIn('account_type', [
                AccountType::ASSET_RECEIVABLE,
                AccountType::LIABILITY_PAYABLE,
                AccountType::ASSET_CASH,
                AccountType::LIABILITY_CREDIT_CARD,
                AccountType::OFF_BALANCE,
            ]));

        Product::resolveRelationUsing('propertyAccountExpense', fn (Product $product) => $product->belongsTo(
            Account::class,
            'property_account_expense_id',
        )
            ->where('deprecated', false)
            ->whereNotIn('account_type', [
                AccountType::ASSET_RECEIVABLE,
                AccountType::LIABILITY_PAYABLE,
                AccountType::ASSET_CASH,
                AccountType::LIABILITY_CREDIT_CARD,
                AccountType::OFF_BALANCE,
            ]));
    }

    public function registerCustomCss(): void
    {
        FilamentAsset::register([
            Css::make('accounts', __DIR__.'/../resources/dist/accounts.css'),
        ], 'accounts');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(AccountPlugin::make());
        });

        $loader = AliasLoader::getInstance();

        $loader->alias('tax', TaxFacade::class);
        $loader->alias('account', AccountFacade::class);

        $this->app->singleton('tax', TaxManager::class);
        $this->app->singleton('account', AccountManager::class);
    }
}
