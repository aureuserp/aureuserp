<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $warehouseScopedTables = [
        'inventories_locations',
        'inventories_operation_types',
        'inventories_rules',
    ];

    public function up(): void
    {
        foreach ($this->warehouseScopedTables as $tableName) {
            if (Schema::hasColumn($tableName, 'key')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->string('key')->nullable()->index();

                $table->unique(['company_id', 'warehouse_id', 'key'], $tableName.'_key_unique');
            });
        }

        if (Schema::hasColumn('inventories_routes', 'key')) {
            return;
        }

        Schema::table('inventories_routes', function (Blueprint $table) {
            $table->string('key')->nullable()->index();
        });
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
};
