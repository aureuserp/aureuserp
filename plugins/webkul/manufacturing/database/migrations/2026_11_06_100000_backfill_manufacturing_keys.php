<?php

use Illuminate\Database\Migrations\Migration;
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

    public function up(): void
    {
        if (! Schema::hasColumn('inventories_rules', 'key')) {
            return;
        }

        foreach ($this->warehouseColumns as $column => [$table, $seedKey]) {
            DB::table($table)
                ->whereIn('id', DB::table('inventories_warehouses')->whereNotNull($column)->select($column))
                ->whereNull('key')
                ->update(['key' => $seedKey]);
        }

        foreach (['stock-pre-production', 'pre-production-production', 'post-production-stock'] as $key) {
            $ids = DB::table('inventories_rules')
                ->whereNotNull('warehouse_id')
                ->whereNull('key')
                ->where('name', 'like', trans("manufacturing::seeders.rules.{$key}", ['code' => '%'], 'en'))
                ->get(['id', 'warehouse_id'])
                ->groupBy('warehouse_id')
                ->filter(fn ($rows) => $rows->count() === 1)
                ->map(fn ($rows) => $rows->first()->id)
                ->values();

            DB::table('inventories_rules')->whereIn('id', $ids)->update(['key' => "rule.{$key}"]);
        }
    }

    public function down(): void {}
};
