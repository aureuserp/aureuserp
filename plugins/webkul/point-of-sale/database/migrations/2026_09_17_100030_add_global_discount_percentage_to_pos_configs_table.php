<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_configs', function (Blueprint $table) {
            $table->decimal('global_discount_percentage', 5, 2)
                ->default(10)
                ->after('enable_global_discount');
        });
    }

    public function down(): void
    {
        Schema::table('pos_configs', function (Blueprint $table) {
            if (Schema::hasColumn('pos_configs', 'global_discount_percentage')) {
                $table->dropColumn('global_discount_percentage');
            }
        });
    }
};
