<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $warehouseScopedTables = [
        'inventories_locations',
        'inventories_operation_types',
        'inventories_rules',
    ];

    protected array $warehouseLocations = [
        'view_location_id'         => 'view',
        'lot_stock_location_id'    => 'stock',
        'input_stock_location_id'  => 'input',
        'qc_stock_location_id'     => 'quality-control',
        'output_stock_location_id' => 'output',
        'pack_stock_location_id'   => 'packing-zone',
    ];

    protected array $warehouseOperationTypes = [
        'in_type_id'       => 'receipts',
        'out_type_id'      => 'delivery-orders',
        'pick_type_id'     => 'pick',
        'pack_type_id'     => 'pack',
        'qc_type_id'       => 'quality-control',
        'store_type_id'    => 'storage',
        'internal_type_id' => 'internal-transfers',
        'xdock_type_id'    => 'cross-dock',
    ];

    protected array $warehouseRoutes = [
        'reception_route_id' => 'reception',
        'delivery_route_id'  => 'delivery',
        'crossdock_route_id' => 'crossdock',
    ];

    protected array $seededLocations = [
        'physical'                => [1, 'view'],
        'partners'                => [2, 'view'],
        'virtual'                 => [3, 'view'],
        'vendors'                 => [4, 'supplier'],
        'customers'               => [5, 'customer'],
        'inter-company-transit'   => [6, 'transit'],
        'inter-warehouse-transit' => [7, 'transit'],
        'inventory-adjustment'    => [8, 'inventory'],
        'production'              => [9, 'production'],
        'scrap'                   => [10, 'inventory'],
    ];

    protected array $companyLocations = [
        'inventory-adjustment' => ['inventory', false],
        'production'           => ['production', false],
        'scrap'                => ['inventory', true],
    ];

    protected array $seededRoutes = [
        'mto'      => 1,
        'buy'      => 5,
        'dropship' => 6,
    ];

    protected array $warehouseRules = [
        'vendors-stock'         => ['location.vendors', 'lot_stock_location_id', 'reception_route_id', 'in_type_id'],
        'stock-customers'       => ['lot_stock_location_id', 'location.customers', 'delivery_route_id', 'out_type_id'],
        'vendors-customers'     => ['location.vendors', 'location.customers', 'crossdock_route_id', 'in_type_id'],
        'input-output'          => ['input_stock_location_id', 'output_stock_location_id', 'crossdock_route_id', 'xdock_type_id'],
        'input-quality-control' => ['input_stock_location_id', 'qc_stock_location_id', 'reception_route_id', 'qc_type_id'],
        'quality-control-stock' => ['qc_stock_location_id', 'lot_stock_location_id', 'reception_route_id', 'store_type_id'],
        'stock-customers-pick'  => ['lot_stock_location_id', 'location.customers', 'delivery_route_id', 'pick_type_id'],
        'packing-zone-output'   => ['pack_stock_location_id', 'output_stock_location_id', 'delivery_route_id', 'pack_type_id'],
        'output-customers'      => ['output_stock_location_id', 'location.customers', 'delivery_route_id', 'out_type_id'],
        'input-stock'           => ['input_stock_location_id', 'lot_stock_location_id', 'reception_route_id', 'store_type_id'],
        'buy-customers'         => [null, 'location.customers', 'crossdock_route_id', 'in_type_id'],
        'buy'                   => [null, 'lot_stock_location_id', 'route.buy', 'in_type_id'],
    ];

    public function up(): void
    {
        $this->backfill();
    }

    public function backfill(): void
    {
        foreach ($this->warehouseLocations as $column => $key) {
            $this->backfillFromWarehouses('inventories_locations', $column, "location.{$key}");
        }

        foreach ($this->warehouseOperationTypes as $column => $key) {
            $this->backfillFromWarehouses('inventories_operation_types', $column, "operation-type.{$key}");
        }

        foreach ($this->warehouseRoutes as $column => $key) {
            $this->backfillFromWarehouses('inventories_routes', $column, "route.{$key}");
        }

        $this->backfillFromWarehouses('inventories_rules', 'mto_pull_id', 'rule.stock-customers-mto');

        foreach ($this->seededLocations as $key => [$id, $type]) {
            $this->backfillUnique(
                DB::table('inventories_locations')->where('id', $id)->where('type', $type)->whereNull('warehouse_id'),
                'inventories_locations',
                "location.{$key}",
            );
        }

        foreach ($this->companyLocations as $key => [$type, $isScrap]) {
            $this->backfillUnique(
                DB::table('inventories_locations')
                    ->where('type', $type)
                    ->where('is_scrap', $isScrap)
                    ->whereNotNull('company_id')
                    ->whereNull('warehouse_id'),
                'inventories_locations',
                "location.{$key}",
            );
        }

        $this->backfillUnique(
            DB::table('inventories_operation_types')->where('id', 9)->where('type', 'dropship')->whereNull('warehouse_id'),
            'inventories_operation_types',
            'operation-type.dropship',
        );

        foreach ($this->seededRoutes as $key => $id) {
            $this->backfillUnique(DB::table('inventories_routes')->where('id', $id), 'inventories_routes', "route.{$key}");
        }

        $this->backfillUnique(
            DB::table('inventories_routes')->whereIn('id', DB::table('inventories_rules')->where('key', 'rule.stock-customers-mto')->select('route_id')),
            'inventories_routes',
            'route.mto',
        );

        $this->backfillUnique(
            DB::table('inventories_rules')->where('id', 14)->where('action', 'buy')->whereNull('warehouse_id'),
            'inventories_rules',
            'rule.dropship',
        );

        foreach ($this->warehouseRules as $key => $structure) {
            $this->backfillWarehouseRuleByStructure($key, $structure);
        }

        foreach (['physical', 'partners', 'virtual', 'vendors', 'customers', 'inter-company-transit', 'inter-warehouse-transit', 'inventory-adjustment', 'production', 'scrap'] as $key) {
            $this->backfillByName('inventories_locations', "inventories::seeders.locations.{$key}", "location.{$key}");
        }

        $this->backfillByName('inventories_operation_types', 'inventories::seeders.operation-types.dropship', 'operation-type.dropship');

        foreach (array_keys($this->seededRoutes) as $key) {
            $this->backfillByName('inventories_routes', "inventories::seeders.routes.{$key}", "route.{$key}");
        }

        $this->backfillByName('inventories_rules', 'inventories::seeders.rules.dropship', 'rule.dropship');

        foreach ($this->warehouseRules as $key => $structure) {
            $this->backfillWarehouseRuleByName($key, $structure[3]);
        }
    }

    public function down(): void {}

    protected function backfillFromWarehouses(string $table, string $column, string $seedKey): void
    {
        if (! Schema::hasColumn('inventories_warehouses', $column)) {
            return;
        }

        DB::table($table)
            ->whereIn('id', DB::table('inventories_warehouses')->whereNotNull($column)->select($column))
            ->whereNull('key')
            ->update(['key' => $seedKey]);
    }

    protected function backfillByName(string $table, string $translationKey, string $seedKey): void
    {
        $this->backfillUnique(
            DB::table($table)
                ->where('name', trans($translationKey, [], 'en'))
                ->when($this->isWarehouseScoped($table), fn (Builder $query) => $query->whereNull('warehouse_id')),
            $table,
            $seedKey,
        );
    }

    protected function backfillWarehouseRuleByStructure(string $key, array $structure): void
    {
        $columns = ['source_location_id', 'destination_location_id', 'route_id', 'operation_type_id'];

        foreach (DB::table('inventories_warehouses')->get() as $warehouse) {
            $candidates = DB::table('inventories_rules')->where('warehouse_id', $warehouse->id);

            foreach (array_combine($columns, $structure) as $column => $reference) {
                $id = $this->resolveReference($warehouse, $reference);

                if ($id === false) {
                    continue 2;
                }

                $candidates->where($column, $id);
            }

            $this->backfillUnique($candidates, 'inventories_rules', "rule.{$key}");
        }
    }

    protected function backfillWarehouseRuleByName(string $key, string $operationTypeColumn): void
    {
        $this->backfillUnique(
            DB::table('inventories_rules')
                ->join('inventories_warehouses', 'inventories_warehouses.id', '=', 'inventories_rules.warehouse_id')
                ->whereColumn('inventories_rules.operation_type_id', "inventories_warehouses.{$operationTypeColumn}")
                ->where('inventories_rules.name', 'like', trans("inventories::seeders.rules.{$key}", ['code' => '%'], 'en')),
            'inventories_rules',
            "rule.{$key}",
        );
    }

    protected function resolveReference(object $warehouse, ?string $reference): int|false|null
    {
        if ($reference === null) {
            return null;
        }

        if (str_contains($reference, '.')) {
            $table = str_starts_with($reference, 'route.') ? 'inventories_routes' : 'inventories_locations';

            $ids = DB::table($table)->where('key', $reference)->pluck('id');

            return $ids->count() === 1 ? $ids->first() : false;
        }

        return $warehouse->{$reference} ?? false;
    }

    protected function backfillUnique(Builder $candidates, string $table, string $seedKey): void
    {
        $scopeOf = fn ($row) => $row->company_id.'|'.($row->warehouse_id ?? '');

        $taken = DB::table($table)
            ->where('key', $seedKey)
            ->get()
            ->map($scopeOf);

        $ids = $candidates
            ->whereNull("{$table}.key")
            ->select("{$table}.id", "{$table}.company_id")
            ->when($this->isWarehouseScoped($table), fn (Builder $query) => $query->addSelect("{$table}.warehouse_id"))
            ->get()
            ->reject(fn ($row) => $taken->contains($scopeOf($row)))
            ->groupBy($scopeOf)
            ->filter(fn ($rows) => $rows->count() === 1)
            ->map(fn ($rows) => $rows->first()->id)
            ->values();

        DB::table($table)->whereIn('id', $ids)->update(['key' => $seedKey]);
    }

    protected function isWarehouseScoped(string $table): bool
    {
        return in_array($table, $this->warehouseScopedTables, true);
    }
};
