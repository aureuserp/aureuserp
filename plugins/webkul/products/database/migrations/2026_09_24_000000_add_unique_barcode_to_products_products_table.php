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
            ->where('barcode', '')
            ->update(['barcode' => null]);

        $duplicateBarcodes = DB::table('products_products')
            ->whereNotNull('barcode')
            ->groupBy('barcode')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('barcode');

        foreach ($duplicateBarcodes as $barcode) {
            $keepId = DB::table('products_products')
                ->where('barcode', $barcode)
                ->min('id');

            DB::table('products_products')
                ->where('barcode', $barcode)
                ->where('id', '<>', $keepId)
                ->update(['barcode' => null]);
        }

        Schema::table('products_products', function (Blueprint $table) {
            $table->unique('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_products', function (Blueprint $table) {
            $table->dropUnique(['barcode']);
        });
    }
};
