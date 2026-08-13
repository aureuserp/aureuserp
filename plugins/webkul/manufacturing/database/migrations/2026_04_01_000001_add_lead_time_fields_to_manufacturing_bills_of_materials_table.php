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
        Schema::table('manufacturing_bills_of_materials', function (Blueprint $table) {
            $table->integer('produce_delay')->default(0)->after('allow_operation_dependencies');
            $table->integer('days_to_prepare_mo')->default(0)->after('produce_delay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manufacturing_bills_of_materials', function (Blueprint $table) {
            foreach (['produce_delay', 'days_to_prepare_mo'] as $column) {
                if (Schema::hasColumn('manufacturing_bills_of_materials', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
