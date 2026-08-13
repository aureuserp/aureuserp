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
        Schema::create('inventories_putaway_rule_package_types', function (Blueprint $table) {
            $table->foreignId('putaway_rule_id')
                ->constrained('inventories_putaway_rules')
                ->cascadeOnDelete();

            $table->foreignId('package_type_id')
                ->constrained('inventories_package_types')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories_putaway_rule_package_types');
    }
};
