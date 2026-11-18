<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $warehouseColumns = [
        'pbm_loc_id'   => ['inventories_locations', 'location.pre-production'],
        'sam_loc_id'   => ['inventories_locations', 'location.post-production'],
        'pbm_type_id'  => ['inventories_operation_types', 'operation-type.pick-components'],
        'sam_type_id'  => ['inventories_operation_types', 'operation-type.store-finished-product'],
        'manu_type_id' => ['inventories_operation_types', 'operation-type.manufacturing'],
        'pbm_route_id' => ['inventories_routes', 'route.manufacture'],
    ];

    protected array $warehouseRules = [
        'stock-pre-production'      => ['lot_stock_location_id', 'pbm_loc_id', 'pbm_type_id'],
        'pre-production-production' => ['pbm_loc_id', 'location.production', 'manu_type_id'],
        'post-production-stock'     => ['sam_loc_id', 'lot_stock_location_id', 'sam_type_id'],
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('inventories_rules', 'key')) {
            return;
        }

        $this->backfill();
    }

    public function backfill(): void
    {
        foreach ($this->warehouseColumns as $column => [$table, $seedKey]) {
            DB::table($table)
                ->whereIn('id', DB::table('inventories_warehouses')->whereNotNull($column)->select($column))
                ->whereNull('key')
                ->update(['key' => $seedKey]);
        }

        $productionLocationIds = DB::table('inventories_locations')->where('key', 'location.production')->pluck('id');

        foreach (DB::table('inventories_warehouses')->get() as $warehouse) {
            foreach ($this->warehouseRules as $key => [$source, $destination, $operationType]) {
                $references = [
                    'source_location_id'      => $warehouse->{$source} ?? null,
                    'destination_location_id' => $destination === 'location.production'
                        ? ($productionLocationIds->count() === 1 ? $productionLocationIds->first() : null)
                        : ($warehouse->{$destination} ?? null),
                    'route_id'                => $warehouse->pbm_route_id ?? null,
                    'operation_type_id'       => $warehouse->{$operationType} ?? null,
                ];

                if (in_array(null, $references, true)) {
                    continue;
                }

                $this->assignKey(DB::table('inventories_rules')->where('warehouse_id', $warehouse->id)->where($references), $warehouse->id, "rule.{$key}");
            }

            foreach ($this->warehouseRules as $key => $structure) {
                if (! isset($warehouse->{$structure[2]})) {
                    continue;
                }

                $this->assignKey(
                    DB::table('inventories_rules')
                        ->where('warehouse_id', $warehouse->id)
                        ->where('operation_type_id', $warehouse->{$structure[2]})
                        ->where('name', 'like', trans("manufacturing::seeders.rules.{$key}", ['code' => '%'], 'en')),
                    $warehouse->id,
                    "rule.{$key}",
                );
            }
        }
    }

    public function down(): void {}

    protected function assignKey(Builder $candidates, int $warehouseId, string $seedKey): void
    {
        if (DB::table('inventories_rules')->where('warehouse_id', $warehouseId)->where('key', $seedKey)->exists()) {
            return;
        }

        $ids = $candidates->whereNull('key')->pluck('id');

        if ($ids->count() === 1) {
            DB::table('inventories_rules')->where('id', $ids->first())->update(['key' => $seedKey]);
        }
    }
};
