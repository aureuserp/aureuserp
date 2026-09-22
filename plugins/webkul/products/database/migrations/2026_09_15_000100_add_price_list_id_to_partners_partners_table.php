<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('partners_partners', 'price_list_id')) {
            return;
        }

        Schema::table('partners_partners', function (Blueprint $table) {
            $table->foreignId('price_list_id')
                ->nullable()
                ->comment('Default Price List')
                ->constrained('products_product_price_lists')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('partners_partners', 'price_list_id')) {
            return;
        }

        Schema::table('partners_partners', function (Blueprint $table) {
            $table->dropForeign(['price_list_id']);

            $table->dropColumn('price_list_id');
        });
    }
};
