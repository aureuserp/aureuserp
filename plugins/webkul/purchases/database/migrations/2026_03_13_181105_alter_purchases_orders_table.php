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
            $table->dropForeign(['requisition_id']);

            $table->foreign('requisition_id')
                ->references('id')
                ->on('purchases_requisitions')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases_orders', function (Blueprint $table) {
            $table->dropForeign(['requisition_id']);

            $table->foreign('requisition_id')
                ->references('id')
                ->on('purchases_requisitions')
                ->nullOnDelete();
        });
    }
};
