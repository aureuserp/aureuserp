<?php

namespace Webkul\Purchase;

use Filament\Panel;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Webkul\Account\Events\MoveCancelled;
use Webkul\Account\Events\MoveConfirmed;
use Webkul\Account\Events\MoveDrafted;
use Webkul\Account\Events\MoveReversed;
use Webkul\Inventory\Events\OperationBackOrdered;
use Webkul\Inventory\Events\OperationDone;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductSupplier;
use Webkul\Purchase\Facades\PurchaseOrder as PurchaseOrderFacade;
use Webkul\Purchase\Listeners\ComputePurchaseOrderFromMoveListener;
use Webkul\Purchase\Listeners\ComputePurchaseOrderListener;
use Webkul\Purchase\Livewire\Customer\ListProducts;
use Webkul\Purchase\Livewire\OrderSummary;

class PurchaseServiceProvider extends PackageServiceProvider
{
    public static string $name = 'purchases';

    public static string $viewNamespace = 'purchases';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasRoute('web')
            ->hasRoute('api')
            ->hasTranslations()
            ->hasMigrations([
                '2025_02_11_101100_create_purchases_order_groups_table',
                '2025_02_11_101101_create_purchases_requisitions_table',
                '2025_02_11_101105_create_purchases_requisition_lines_table',
                '2025_02_11_101110_create_purchases_orders_table',
                '2025_02_11_101118_create_purchases_order_lines_table',
                '2025_02_11_135617_create_purchases_order_line_taxes_table',
                '2025_02_11_142937_create_purchases_order_account_moves_table',
                '2025_02_11_143351_alter_accounts_account_move_lines_table',
                '2025_03_17_101755_add_inventories_columns_to_purchases_orders_table_from_purchases',
                '2025_03_17_101814_add_inventories_columns_to_purchases_order_lines_table_from_purchases',
                '2025_03_17_111610_add_purchases_columns_to_inventories_moves_table_from_purchases',
                '2025_03_17_115707_create_purchases_order_operations_table_from_purchases',
                '2026_03_11_103115_alter_purchases_order_lines_table',
                '2026_03_13_181105_alter_purchases_orders_table',
                '2026_04_15_044345_add_destination_address_id_in_purchases_orders_table',
                '2026_04_22_115707_create_purchases_order_line_moves_table_from_purchases',
                '2026_04_23_043411_add_procurement_group_id_column_in_purchases_orders_table_from_purchases',
                '2026_04_23_043412_add_procurement_group_id_column_in_purchases_order_lines_table_from_purchases',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2025_01_11_094022_create_purchases_order_settings',
                '2025_01_11_094022_create_purchases_product_settings',
            ])
            ->runsSettings()
            ->hasDependencies([
                'invoices',
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('purchases');
    }

    public function packageBooted(): void
    {
        Livewire::component('order-summary', OrderSummary::class);

        Livewire::component('list-products', ListProducts::class);

        Event::listen([OperationDone::class, OperationBackOrdered::class], ComputePurchaseOrderListener::class);

        Event::listen(
            [MoveConfirmed::class, MoveCancelled::class, MoveDrafted::class, MoveReversed::class],
            ComputePurchaseOrderFromMoveListener::class,
        );

        $this->contributeProductSchema();
    }

    protected function contributeProductSchema(): void
    {
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        Product::resolveRelationUsing('sellers', fn (Product $product) => $product->is_configurable
            ? $product->hasMany(ProductSupplier::class)->orWhereIn('product_id', $product->variants()->pluck('id'))
            : $product->hasMany(ProductSupplier::class));
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(PurchasePlugin::make());
        });

        $loader = AliasLoader::getInstance();

        $loader->alias('purchase_order', PurchaseOrderFacade::class);

        $this->app->singleton('purchase_order', PurchaseOrder::class);
    }
}
