<?php

namespace Webkul\PointOfSale;

use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Warehouse as InventoryWarehouse;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\PointOfSale\Facades\PointOfSale as PointOfSaleFacade;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Schemas\PosProductSchema;
use Webkul\PointOfSale\Livewire\RegisterControls;
use Webkul\PointOfSale\Models\Category;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Models\Warehouse;
use Webkul\PointOfSale\Observers\WarehouseObserver;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry;
use Webkul\Product\Models\Product;
use Webkul\Product\Support\ProductUsageRegistry;
use Webkul\Support\Services\SequenceService;
use Webkul\TableViews\Filament\Components\PresetView;

class PointOfSaleServiceProvider extends PackageServiceProvider
{
    public static string $name = 'point-of-sale';

    public static string $viewNamespace = 'point-of-sale';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasRoute('api')
            ->hasRoute('web')
            ->hasMigrations([
                '2026_09_17_100001_create_pos_payment_methods_table',
                '2026_09_17_100002_create_pos_configs_table',
                '2026_09_17_100003_create_pos_config_payment_methods_table',
                '2026_09_17_100004_create_pos_config_price_lists_table',
                '2026_09_17_100005_create_pos_config_fiscal_positions_table',
                '2026_09_17_100006_alter_inventories_warehouses_table',
                '2026_09_17_100007_create_pos_sessions_table',
                '2026_09_17_100008_create_pos_cash_movements_table',
                '2026_09_17_100009_create_pos_categories_table',
                '2026_09_17_100010_create_pos_config_categories_table',
                '2026_09_17_100011_create_pos_orders_table',
                '2026_09_17_100012_create_pos_order_lines_table',
                '2026_09_17_100013_create_pos_order_line_taxes_table',
                '2026_09_17_100014_create_pos_order_line_attribute_values_table',
                '2026_09_17_100015_create_pos_payments_table',
                '2026_09_17_100016_create_pos_bills_table',
                '2026_09_17_100017_create_pos_config_bills_table',
                '2026_09_17_100018_create_pos_order_line_lots_table',
                '2026_09_17_100019_create_pos_floors_table',
                '2026_09_17_100020_create_pos_tables_table',
                '2026_09_17_100021_create_pos_config_floors_table',
                '2026_09_17_100022_create_pos_printers_table',
                '2026_09_17_100023_create_pos_printer_categories_table',
                '2026_09_17_100024_create_pos_config_printers_table',
                '2026_09_17_100025_create_pos_category_products_table',
                '2026_09_17_100026_add_table_id_to_pos_orders_table',
                '2026_09_17_100027_create_pos_notes_table',
                '2026_09_17_100028_alter_products_products_table',
                '2026_09_17_100029_alter_pos_bills_and_pos_notes_tables',
                '2026_09_17_100030_add_global_discount_percentage_to_pos_configs_table',
                '2026_09_17_100031_add_floating_name_and_is_booked_to_pos_orders_table',
                '2026_09_17_100032_add_show_margins_and_costs_to_pos_configs_table',
                '2026_09_17_100033_add_preparation_state_to_pos_orders_table',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2026_09_17_110002_create_point_of_sale_account_settings',
                '2026_09_17_110003_create_point_of_sale_inventory_settings',
            ])
            ->runsSettings()
            ->hasDependencies([
                'products',
                'inventories',
                'invoices',
            ])
            ->hasSeeder('Webkul\\PointOfSale\\Database\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {
                $operationTypeIds = [];

                $configIds = [];

                $command->startWith(function (UninstallCommand $command) use (&$operationTypeIds, &$configIds) {
                    if (! Schema::hasColumn('inventories_warehouses', 'pos_type_id')) {
                        return;
                    }

                    foreach (Warehouse::withTrashed()->get() as $warehouse) {
                        $operationTypeIds = array_merge($operationTypeIds, array_filter([
                            $warehouse->pos_type_id,
                            $warehouse->pos_return_type_id,
                        ]));

                        $warehouse->updateQuietly([
                            'pos_type_id'        => null,
                            'pos_return_type_id' => null,
                        ]);
                    }

                    $configIds = Config::withTrashed()->pluck('id')->all();
                });

                $command->endWith(function (UninstallCommand $command) use (&$operationTypeIds, &$configIds) {
                    $operationTypeIds = array_values(array_unique($operationTypeIds));

                    SequenceService::purgeScoped(Config::class, $configIds);

                    SequenceService::purgeScoped(OperationType::class, $operationTypeIds);

                    ChatterCleanupService::purgeForModels([Order::class, Session::class]);

                    if (empty($operationTypeIds)) {
                        return;
                    }

                    DB::transaction(function () use ($operationTypeIds) {
                        $operationIds = DB::table('inventories_operations')
                            ->whereIn('operation_type_id', $operationTypeIds)
                            ->pluck('id')
                            ->all();

                        if (! empty($operationIds)) {
                            DB::table('inventories_move_lines')
                                ->whereIn('operation_id', $operationIds)
                                ->delete();

                            DB::table('inventories_moves')
                                ->whereIn('operation_id', $operationIds)
                                ->delete();

                            DB::table('inventories_operations')
                                ->whereIn('id', $operationIds)
                                ->delete();
                        }

                        OperationType::withTrashed()
                            ->whereIn('id', $operationTypeIds)
                            ->forceDelete();
                    });
                });
            })
            ->icon('point-of-sale');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(PointOfSalePlugin::make());
        });

        $loader = AliasLoader::getInstance();

        $loader->alias('point-of-sale', PointOfSaleFacade::class);

        $this->app->singleton('point-of-sale', PointOfSaleManager::class);
    }

    public function packageBooted(): void
    {
        Livewire::component('point-of-sale-register-controls', RegisterControls::class);

        $this->registerModelObservers();

        $this->contributeProductSchema();

        $this->contributeProductUsage();
    }

    protected function contributeProductSchema(): void
    {
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        Product::resolveRelationUsing('posCategories', fn (Product $product) => $product->belongsToMany(
            Category::class,
            'pos_category_products',
            'product_id',
            'category_id',
        ));

        Product::resolveRelationUsing('posOrderLines', fn (Product $product) => $product->hasMany(
            OrderLine::class,
            'product_id',
        ));

        ProductSchemaRegistry::form('left.append', fn () => PosProductSchema::formSection());

        ProductSchemaRegistry::infolist('left.append', fn () => PosProductSchema::infolistSection());

        ProductSchemaRegistry::presetView(
            'pos_products',
            fn () => PresetView::make(__('point-of-sale::filament/admin/clusters/products/resources/product.tabs.pos'))
                ->icon('heroicon-s-shopping-bag')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('available_in_pos', true)),
        );

        ProductSchemaRegistry::eagerLoad(['posCategories']);

        Product::contributeFillable([
            'available_in_pos',
        ]);

        Product::contributeCasts([
            'available_in_pos' => 'boolean',
        ]);
    }

    protected function contributeProductUsage(): void
    {
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        ProductUsageRegistry::register(
            OrderLine::class,
            Config::class,
        );
    }

    protected function registerModelObservers(): void
    {
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        InventoryWarehouse::observe(WarehouseObserver::class);
    }
}
