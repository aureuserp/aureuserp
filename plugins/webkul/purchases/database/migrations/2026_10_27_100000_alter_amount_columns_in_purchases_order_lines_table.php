<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases_order_lines', function (Blueprint $table) {
            $table->decimal('price_unit', 15, 4)->default(0)->change();
            $table->decimal('price_subtotal', 15, 4)->default(0)->change();
            $table->decimal('price_tax', 15, 4)->nullable()->default(0)->change();
            $table->decimal('price_total', 15, 4)->default(0)->change();
            $table->decimal('price_total_cc', 15, 4)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchases_order_lines', function (Blueprint $table) {
            $table->decimal('price_unit', 15, 2)->default(0)->change();
            $table->decimal('price_subtotal', 15, 2)->default(0)->change();
            $table->double('price_tax')->nullable()->default(null)->change();
            $table->decimal('price_total', 15, 2)->default(0)->change();
            $table->decimal('price_total_cc', 15, 2)->default(0)->change();
        });
    }
};
