<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
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

    protected array $warehouseRules = [
        'vendors-stock'         => null,
        'stock-customers'       => 'out_type_id',
        'vendors-customers'     => null,
        'input-output'          => null,
        'input-quality-control' => null,
        'quality-control-stock' => null,
        'stock-customers-pick'  => 'pick_type_id',
        'packing-zone-output'   => null,
        'output-customers'      => null,
        'input-stock'           => null,
        'buy-customers'         => null,
        'buy'                   => null,
    ];

    public function up(): void
    {
        foreach ($this->warehouseScopedTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->string('key')->nullable()->index();

                $table->unique(['company_id', 'warehouse_id', 'key'], $tableName.'_key_unique');
            });
        }

        Schema::table('inventories_routes', function (Blueprint $table) {
            $table->string('key')->nullable()->index();
        });

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

        foreach (['physical', 'partners', 'virtual', 'vendors', 'customers', 'inter-company-transit', 'inter-warehouse-transit', 'inventory-adjustment', 'production', 'scrap'] as $key) {
            $this->backfillByName('inventories_locations', "inventories::seeders.locations.{$key}", "location.{$key}");
        }

        $this->backfillByName('inventories_operation_types', 'inventories::seeders.operation-types.dropship', 'operation-type.dropship');

        foreach (['mto', 'buy', 'dropship'] as $key) {
            $this->backfillByName('inventories_routes', "inventories::seeders.routes.{$key}", "route.{$key}");
        }

        $this->backfillByName('inventories_rules', 'inventories::seeders.rules.dropship', 'rule.dropship');

        foreach ($this->warehouseRules as $key => $operationTypeColumn) {
            $this->backfillWarehouseRule($key, $operationTypeColumn);
        }
    }

    public function down(): void
    {
        foreach ($this->warehouseScopedTables as $tableName) {
            if (Schema::hasColumn($tableName, 'key')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->dropUnique($tableName.'_key_unique');

                    $table->dropIndex(['key']);

                    $table->dropColumn('key');
                });
            }
        }

        if (Schema::hasColumn('inventories_routes', 'key')) {
            Schema::table('inventories_routes', function (Blueprint $table) {
                $table->dropIndex(['key']);

                $table->dropColumn('key');
            });
        }
    }

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

    protected function backfillWarehouseRule(string $key, ?string $operationTypeColumn): void
    {
        $candidates = DB::table('inventories_rules')
            ->join('inventories_warehouses', 'inventories_warehouses.id', '=', 'inventories_rules.warehouse_id')
            ->where('inventories_rules.name', 'like', trans("inventories::seeders.rules.{$key}", ['code' => '%'], 'en'));

        if ($operationTypeColumn) {
            $candidates->whereColumn('inventories_rules.operation_type_id', "inventories_warehouses.{$operationTypeColumn}");
        }

        $this->backfillUnique($candidates, 'inventories_rules', "rule.{$key}");
    }

    protected function backfillUnique(Builder $candidates, string $table, string $seedKey): void
    {
        $ids = $candidates
            ->whereNull("{$table}.key")
            ->select("{$table}.id", "{$table}.company_id")
            ->when($this->isWarehouseScoped($table), fn (Builder $query) => $query->addSelect("{$table}.warehouse_id"))
            ->get()
            ->groupBy(fn ($row) => $row->company_id.'|'.($row->warehouse_id ?? ''))
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
