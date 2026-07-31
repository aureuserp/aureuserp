<?php

namespace Webkul\Manufacturing;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Models\Scrap;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Manufacturing\Facades\Manufacturing as ManufacturingFacade;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\BillOfMaterialLine;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Observers\MoveObserver;
use Webkul\Manufacturing\Observers\WarehouseObserver;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry;
use Webkul\Product\Models\Product;
use Webkul\TableViews\Filament\Components\PresetView;

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
                $operationTypeIds = [];
                $locationIds = [];
                $routeIds = [];

                $command->startWith(function (UninstallCommand $command) use (&$operationTypeIds, &$locationIds, &$routeIds) {
                    if (! Schema::hasColumn('inventories_warehouses', 'pbm_route_id')) {
                        return;
                    }

                    foreach (Models\Warehouse::withTrashed()->get() as $warehouse) {
                        $operationTypeIds = array_merge($operationTypeIds, array_filter([
                            $warehouse->manu_type_id,
                            $warehouse->pbm_type_id,
                            $warehouse->sam_type_id,
                        ]));

                        $locationIds = array_merge($locationIds, array_filter([
                            $warehouse->pbm_loc_id,
                            $warehouse->sam_loc_id,
                        ]));

                        $routeIds = array_merge($routeIds, array_filter([
                            $warehouse->pbm_route_id,
                        ]));

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
                    }
                });

                $command->endWith(function (UninstallCommand $command) use (&$operationTypeIds, &$locationIds, &$routeIds) {
                    $operationTypeIds = array_values(array_unique($operationTypeIds));
                    $locationIds = array_values(array_unique($locationIds));
                    $routeIds = array_values(array_unique($routeIds));

                    if (! empty($operationTypeIds) || ! empty($locationIds) || ! empty($routeIds)) {
                        DB::transaction(function () use ($operationTypeIds, $locationIds, $routeIds) {
                            if (! empty($routeIds)) {
                                Rule::withTrashed()
                                    ->whereIn('route_id', $routeIds)
                                    ->forceDelete();

                                Route::withTrashed()
                                    ->whereIn('id', $routeIds)
                                    ->forceDelete();
                            }

                            if (! empty($locationIds)) {
                                $moveIds = Move::query()
                                    ->where(function ($query) use ($locationIds) {
                                        $query->whereIn('source_location_id', $locationIds)
                                            ->orWhereIn('destination_location_id', $locationIds);
                                    })
                                    ->pluck('id');

                                MoveLine::query()
                                    ->where(function ($query) use ($locationIds, $moveIds) {
                                        $query->whereIn('source_location_id', $locationIds)
                                            ->orWhereIn('destination_location_id', $locationIds)
                                            ->orWhereIn('move_id', $moveIds);
                                    })
                                    ->delete();

                                Move::query()
                                    ->whereIn('id', $moveIds)
                                    ->delete();

                                Scrap::query()
                                    ->where(function ($query) use ($locationIds) {
                                        $query->whereIn('source_location_id', $locationIds)
                                            ->orWhereIn('destination_location_id', $locationIds);
                                    })
                                    ->delete();

                                ProductQuantity::query()
                                    ->whereIn('location_id', $locationIds)
                                    ->delete();
                            }

                            Operation::query()
                                ->where(function ($query) use ($operationTypeIds, $locationIds) {
                                    if (! empty($operationTypeIds)) {
                                        $query->whereIn('operation_type_id', $operationTypeIds);
                                    }

                                    if (! empty($locationIds)) {
                                        $query->orWhere(fn ($subQuery) => $subQuery
                                            ->whereIn('source_location_id', $locationIds)
                                            ->orWhereIn('destination_location_id', $locationIds));
                                    }
                                })
                                ->delete();

                            if (! empty($operationTypeIds)) {
                                OperationType::withTrashed()
                                    ->whereIn('id', $operationTypeIds)
                                    ->forceDelete();
                            }

                            if (! empty($locationIds)) {
                                Location::withTrashed()
                                    ->whereIn('id', $locationIds)
                                    ->forceDelete();
                            }
                        });
                    }

                    ChatterCleanupService::purgeForModels([Order::class]);
                });
            })
            ->icon('manufacturing');
    }

    public function packageBooted(): void
    {
        $this->registerCustomCss();

        $this->registerModelObservers();

        $this->contributeProductSchema();
    }

    protected function contributeProductSchema(): void
    {
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        Product::resolveRelationUsing('billsOfMaterials', fn (Product $product) => $product->hasMany(
            BillOfMaterial::class,
            'product_id',
        ));

        Product::resolveRelationUsing('billOfMaterialLines', fn (Product $product) => $product->hasMany(
            BillOfMaterialLine::class,
            'product_id',
        ));

        ProductSchemaRegistry::presetView(
            'components',
            fn () => PresetView::make(__('manufacturing::filament/clusters/products/resources/product/pages/list-products.tabs.components'))
                ->icon('heroicon-s-puzzle-piece')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('billOfMaterialLines')),
        );
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
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        Warehouse::observe(WarehouseObserver::class);

        Move::observe(MoveObserver::class);
    }
}
