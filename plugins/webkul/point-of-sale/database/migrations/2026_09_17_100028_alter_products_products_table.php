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
        Schema::table('products_products', function (Blueprint $table) {
            $table->boolean('available_in_pos')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_products', function (Blueprint $table) {
            if (Schema::hasColumn('products_products', 'available_in_pos')) {
                $table->dropColumn('available_in_pos');
            }
        });
    }
};
