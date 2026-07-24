<?php

namespace Webkul\Inventory;

use Filament\Panel;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Actions\UpdateQuantityAction;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Schemas\InventoryProductSchema;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Support\QuantityResolver;
use Webkul\Inventory\Filament\Widgets\OperationTypeCardWidget;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Scrap;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

class InventoryServiceProvider extends PackageServiceProvider
{
    public static string $name = 'inventories';

    public static string $viewNamespace = 'inventories';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasRoute('api')
            ->hasMigrations([
                '2025_01_06_072032_create_inventories_tags_table',
                '2025_01_06_072130_create_inventories_warehouses_table',
                '2025_01_06_072135_create_inventories_storage_categories_table',
                '2025_01_06_072224_create_inventories_locations_table',
                '2025_01_06_072349_create_inventories_operation_types_table',
                '2025_01_06_072353_create_inventories_routes_table',
                '2025_01_06_072356_create_inventories_rules_table',
                '2025_01_06_143103_create_inventories_route_warehouses_table',
                '2025_01_07_083342_add_relationship_to_inventories_warehouses_table',
                '2025_01_07_095737_create_inventories_warehouse_resupplies_table',
                '2025_01_07_145741_create_inventories_package_types_table',
                '2025_01_07_145741_create_inventories_packages_table',
                '2025_01_10_091035_alter_products_products_table',
                '2025_01_10_095946_create_inventories_category_routes_table',
                '2025_01_10_095946_create_inventories_product_routes_table',
                '2025_01_10_102716_add_package_type_id_column_in_products_packagings_table',
                '2025_01_10_111734_create_inventories_storage_category_capacities_table',
                '2025_01_13_061029_create_inventories_route_packagings_table',
                '2025_01_14_092601_create_inventories_lots_table',
                '2025_01_14_113233_create_inventories_product_quantities_table',
                '2025_01_14_113235_create_inventories_product_quantity_relocations_table',
                '2025_01_14_133233_create_inventories_operations_table',
                '2025_01_14_133245_create_inventories_package_levels_table',
                '2025_01_14_133246_create_inventories_package_destinations_table',
                '2025_01_14_133250_create_inventories_scraps_table',
                '2025_01_14_133255_create_inventories_scrap_tags_table',
                '2025_01_14_133260_create_inventories_moves_table',
                '2025_01_14_133266_create_inventories_move_destinations_table',
                '2025_01_15_095753_create_inventories_move_lines_table',
                '2025_03_13_074205_create_inventories_order_points_table',
                '2025_03_17_101755_add_inventories_columns_to_purchases_orders_table_from_inventories',
                '2025_03_17_101814_add_inventories_columns_to_purchases_order_lines_table_from_inventories',
                '2025_03_17_111610_add_purchases_columns_to_inventories_moves_table_from_inventories',
                '2025_03_17_115707_create_purchases_order_operations_table_from_inventories',
                '2025_03_19_100337_add_is_refund_column_in_inventories_moves_table',
                '2025_04_07_111609_add_sales_columns_to_inventories_operations_table_from_inventories',
                '2025_04_07_111610_add_sales_columns_to_inventories_moves_table_from_inventories',
                '2025_04_09_101755_add_inventories_columns_to_sales_orders_table_from_inventories',
                '2025_04_09_101814_add_inventories_columns_to_sales_order_lines_table_from_inventories',
                '2025_08_13_120000_alter_description_column_in_inventories_locations_table',
                '2026_03_17_055610_fix_corrupted_location_parent_paths',
                '2026_04_08_042911_create_procurement_groups_table',
                '2026_04_08_043248_add_procurement_group_id_inventories_operations_table',
                '2026_04_08_043311_add_procurement_group_id_inventories_moves_table',
                '2026_04_08_043411_add_procurement_group_id_column_in_sales_orders_table_from_inventories',
                '2026_04_08_043511_add_sale_order_id_column_in_inventories_procurement_groups_table_from_inventories',
                '2026_04_09_113843_add_procurement_group_id_column_in_inventories_rules_table',
                '2026_04_10_094203_add_price_unit_column_in_inventories_moves_table',
                '2026_04_16_074549_create_inventories_route_moves_table',
                '2026_04_22_115707_create_purchases_order_line_moves_table_from_inventories',
                '2026_04_23_043411_add_procurement_group_id_column_in_purchases_orders_table_from_inventories',
                '2026_04_23_043412_add_procurement_group_id_column_in_purchases_order_lines_table_from_inventories',
                '2026_05_14_092628_inventories_create_putaway_rules_table',
                '2026_05_15_103923_create_inventories_putaway_rule_package_types_table',
                '2026_06_22_104603_add_additional_column_in_inventories_moves_table',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2025_01_17_094021_create_inventories_operation_settings',
                '2025_01_17_094023_create_inventories_traceability_settings',
                '2025_01_17_094024_create_inventories_warehouse_settings',
                '2025_01_17_094051_create_inventories_logistic_settings',
            ])
            ->runsSettings()
            ->hasSeeder('Webkul\\Inventory\\Database\Seeders\\DatabaseSeeder')
            ->hasDependencies([
                'products',
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {
                $command->startWith(function (UninstallCommand $command) {
                    $tables = [
                        'inventories_rules',
                        'inventories_operations',
                        'inventories_product_quantities',
                        'inventories_scraps',
                        'inventories_moves',
                        'inventories_move_lines',
                        'inventories_operation_types',
                        'inventories_warehouses',
                        'inventories_routes',
                        'inventories_locations',
                    ];

                    foreach ($tables as $table) {
                        if (! Schema::hasTable($table)) {
                            continue;
                        }

                        DB::table($table)->delete();
                    }
                });

                $command->endWith(function () {
                    ChatterCleanupService::purgeForModels([Operation::class, Scrap::class]);
                });
            })
            ->icon('inventories');
    }

    public function packageBooted(): void
    {
        $this->contributeProductSchema();

        $this->registerLivewireComponents();
    }

    public function registerLivewireComponents(): void
    {
        Livewire::component('inventories-operation-type-card', OperationTypeCardWidget::class);
    }

    protected function contributeProductSchema(): void
    {
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        ProductSchemaRegistry::form('left.inventory', fn () => InventoryProductSchema::formSection());

        ProductSchemaRegistry::infolist('left.inventory', fn () => InventoryProductSchema::infolistSection());

        ProductSchemaRegistry::table('columns', fn () => InventoryProductSchema::onHandColumn());

        ProductSchemaRegistry::table('columns', fn () => InventoryProductSchema::forecastedColumn());

        ProductSchemaRegistry::actions('header', fn () => UpdateQuantityAction::make());

        ProductSchemaRegistry::eagerLoad(['routes', 'responsible']);

        Product::contributeFillable([
            'sale_delay',
            'tracking',
            'description_picking',
            'description_pickingout',
            'description_pickingin',
            'is_storable',
            'expiration_time',
            'use_time',
            'removal_time',
            'alert_time',
            'use_expiration_date',
            'responsible_id',
        ]);

        Product::contributeCasts([
            'tracking'            => ProductTracking::class,
            'use_expiration_date' => 'boolean',
            'is_storable'         => 'boolean',
        ]);

        Product::resolveRelationUsing('routes', fn (Product $product) => $product->belongsToMany(
            Route::class,
            'inventories_product_routes',
            'product_id',
            'route_id',
        ));

        Product::resolveRelationUsing('responsible', fn (Product $product) => $product->belongsTo(
            User::class,
            'responsible_id',
        ));

        Product::resolveRelationUsing('moveLines', fn (Product $product) => $product->is_configurable
            ? $product->hasMany(MoveLine::class)->orWhereIn('product_id', $product->variants()->pluck('id'))
            : $product->hasMany(MoveLine::class)
        );

        Product::resolveRelationUsing('moves', fn (Product $product) => $product->is_configurable
            ? $product->hasMany(Move::class)->orWhereIn('product_id', $product->variants()->pluck('id'))
            : $product->hasMany(Move::class)
        );

        Product::resolveRelationUsing('quantities', fn (Product $product) => $product->is_configurable
            ? $product->hasMany(ProductQuantity::class)->orWhereIn('product_id', $product->variants()->pluck('id'))
            : $product->hasMany(ProductQuantity::class)
        );
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(InventoryPlugin::make());
        });

        $loader = AliasLoader::getInstance();

        $loader->alias('inventory', InventoryFacade::class);

        $this->app->singleton('inventory', InventoryManager::class);

        $this->app->scoped(QuantityResolver::class);
    }
}
