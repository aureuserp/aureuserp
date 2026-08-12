<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventories_operations', function (Blueprint $table) {
            $table->foreignId('procurement_group_id')
                ->nullable()
                ->constrained('inventories_procurement_groups')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories_operations', function (Blueprint $table) {
            $table->dropForeign(['procurement_group_id']);
            $table->dropColumn('procurement_group_id');
        });
    }
};
