<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->string('floating_name', 64)
                ->nullable()
                ->after('reference');

            $table->boolean('is_booked')
                ->default(0)
                ->after('is_takeaway');
        });
    }

    public function down(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            foreach (['floating_name', 'is_booked'] as $column) {
                if (Schema::hasColumn('pos_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
