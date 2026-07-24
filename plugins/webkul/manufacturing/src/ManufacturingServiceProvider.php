<?php

namespace Webkul\Manufacturing;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Schema;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Manufacturing\Facades\Manufacturing as ManufacturingFacade;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Observers\MoveObserver;
use Webkul\Manufacturing\Observers\WarehouseObserver;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class ManufacturingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'manufacturing';

    public static string $viewNamespace = 'manufacturing';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2026_03_31_064242_create_manufacturing_bills_of_materials_table',
                '2026_03_31_064243_create_manufacturing_work_centers_table',
                '2026_03_31_064244_create_manufacturing_operations_table',
                '2026_03_31_064245_create_manufacturing_bill_of_material_lines_table',
                '2026_03_31_064246_create_manufacturing_bill_of_material_byproducts_table',
                '2026_03_31_064247_create_manufacturing_orders_table',
                '2026_03_31_064248_create_manufacturing_work_orders_table',
                '2026_03_31_064249_create_manufacturing_unbuild_orders_table',
                '2026_03_31_064250_create_manufacturing_batch_productions_table',
                '2026_03_31_064251_create_manufacturing_consumption_warnings_table',
                '2026_03_31_064252_create_manufacturing_consumption_warning_lines_table',
                '2026_03_31_064253_create_manufacturing_order_backorders_table',
                '2026_03_31_064254_create_manufacturing_order_backorder_lines_table',
                '2026_03_31_064255_create_manufacturing_order_split_batches_table',
                '2026_03_31_064256_create_manufacturing_order_splits_table',
                '2026_03_31_064257_create_manufacturing_order_split_lines_table',
                '2026_03_31_064258_create_manufacturing_work_center_capacities_table',
                '2026_03_31_064259_create_manufacturing_work_center_loss_types_table',
                '2026_03_31_064260_create_manufacturing_work_center_productivity_losses_table',
                '2026_03_31_064261_create_manufacturing_work_center_productivity_logs_table',
                '2026_03_31_064262_create_manufacturing_work_center_tags_table',
                '2026_03_31_064263_create_manufacturing_bill_of_material_byproduct_attribute_values_table',
                '2026_03_31_064264_create_manufacturing_bill_of_material_line_attribute_values_table',
                '2026_03_31_064265_create_manufacturing_operation_dependencies_table',
                '2026_03_31_064266_create_manufacturing_operation_attribute_values_table',
                '2026_03_31_064267_create_manufacturing_consumption_warning_order_table',
                '2026_03_31_064268_create_manufacturing_order_backorder_order_table',
                '2026_03_31_064269_create_manufacturing_order_label_types_table',
                '2026_03_31_064270_create_manufacturing_work_center_alternatives_table',
                '2026_03_31_064271_create_manufacturing_work_center_tag_table',
                '2026_03_31_064272_create_manufacturing_work_order_dependencies_table',
                '2026_03_31_180000_add_worksheet_to_manufacturing_operations_table',
                '2026_04_01_000001_add_lead_time_fields_to_manufacturing_bills_of_materials_table',
                '2026_04_02_000002_alter_inventories_warehouses_table',
                '2026_04_02_000003_alter_inventories_moves_table',
                '2026_04_02_000004_alter_inventories_move_lines_table',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2026_05_08_094021_create_manufacturing_operation_settings',
                '2026_05_08_094031_create_manufacturing_planning_settings',
            ])
            ->runsSettings()
            ->hasDependencies([
                'products',
                'inventories',
            ])
            ->hasSeeder('Webkul\\Manufacturing\\Database\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {
                $command->startWith(function (UninstallCommand $command) {
                    if (! Schema::hasColumn('inventories_warehouses', 'pbm_route_id')) {
                        return;
                    }

                    $warehouses = Models\Warehouse::all();

                    foreach ($warehouses as $warehouse) {
                        $pbmRouteId = $warehouse->pbm_route_id;

                        $operationTypeIds = array_filter([
                            $warehouse->manu_type_id,
                            $warehouse->pbm_type_id,
                            $warehouse->sam_type_id,
                        ]);

                        $locationIds = array_filter([
                            $warehouse->pbm_loc_id,
                            $warehouse->sam_loc_id,
                        ]);

                        $warehouse->updateQuietly([
                            'manufacture_pull_id'     => null,
                            'manufacture_mto_pull_id' => null,
                            'pbm_mto_pull_id'         => null,
                            'sam_rule_id'             => null,
                            'manu_type_id'            => null,
                            'pbm_type_id'             => null,
                            'sam_type_id'             => null,
                            'pbm_route_id'            => null,
                            'pbm_loc_id'              => null,
                            'sam_loc_id'              => null,
                        ]);

                        if ($pbmRouteId) {
                            Rule::withTrashed()
                                ->where('route_id', $pbmRouteId)
                                ->forceDelete();

                            $warehouse->routes()->detach($pbmRouteId);

                            Route::withTrashed()
                                ->where('id', $pbmRouteId)
                                ->forceDelete();
                        }

                        // Delete operation types created by manufacturing
                        if (! empty($operationTypeIds)) {
                            OperationType::withTrashed()
                                ->whereIn('id', $operationTypeIds)
                                ->forceDelete();
                        }

                        // Delete locations created by manufacturing
                        if (! empty($locationIds)) {
                            Location::withTrashed()
                                ->whereIn('id', $locationIds)
                                ->forceDelete();
                        }
                    }
                });

                $command->endWith(function () {
                    ChatterCleanupService::purgeForModels([Order::class]);
                });
            })
            ->icon('manufacturing');
    }

    public function packageBooted(): void
    {
        $this->registerCustomCss();

        $this->registerModelObservers();
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(ManufacturingPlugin::make());
        });

        $loader = AliasLoader::getInstance();

        $loader->alias('manufacturing', ManufacturingFacade::class);

        $this->app->singleton('manufacturing', ManufacturingManager::class);
    }

    public function registerCustomCss(): void
    {
        FilamentAsset::register([
            Css::make('manufacturing', __DIR__.'/../resources/dist/manufacturing.css'),
        ], 'manufacturing');
    }

    protected function registerModelObservers(): void
    {
        Warehouse::observe(WarehouseObserver::class);

        Move::observe(MoveObserver::class);
    }
}
