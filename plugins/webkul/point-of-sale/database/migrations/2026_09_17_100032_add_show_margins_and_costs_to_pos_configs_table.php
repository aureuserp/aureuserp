<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_configs', function (Blueprint $table) {
            $table->boolean('show_margins_and_costs')
                ->default(1)
                ->after('show_category_images');
        });
    }

    public function down(): void
    {
        Schema::table('pos_configs', function (Blueprint $table) {
            if (Schema::hasColumn('pos_configs', 'show_margins_and_costs')) {
                $table->dropColumn('show_margins_and_costs');
            }
        });
    }
};
