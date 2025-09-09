<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('products_products')
            ->whereNull('is_favorite')
            ->update(['is_favorite' => 0]);

        Schema::table('products_products', function (Blueprint $table) {
            $table->boolean('is_favorite')->default(0)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_products', function (Blueprint $table) {
            $table->boolean('is_favorite')->nullable()->default(null)->change();
        });
    }
};
