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
        Schema::table('products_product_suppliers', function (Blueprint $table) {
            $table->decimal('price_discounted', 15, 4)->default(0);

            $table->foreignId('uom_id')
                ->nullable()
                ->constrained('unit_of_measures')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_product_suppliers', function (Blueprint $table) {
            $table->dropColumn('price_discounted');

            $table->dropForeign(['uom_id']);

            $table->dropColumn('uom_id');
        });
    }
};
