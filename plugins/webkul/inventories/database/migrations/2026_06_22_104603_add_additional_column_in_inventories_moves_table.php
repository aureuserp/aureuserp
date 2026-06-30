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
        Schema::table('inventories_moves', function (Blueprint $table) {
            $table->boolean('additional')->default(0)->after('is_inventory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories_moves', function (Blueprint $table) {
            $table->dropColumn('additional');
        });
    }
};
