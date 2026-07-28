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
        Schema::table('purchases_orders', function (Blueprint $table) {
            $table->foreignId('destination_address_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases_orders', function (Blueprint $table) {
            $table->dropForeign(['destination_address_id']);

            $table->dropColumn('destination_address_id');
        });
    }
};
