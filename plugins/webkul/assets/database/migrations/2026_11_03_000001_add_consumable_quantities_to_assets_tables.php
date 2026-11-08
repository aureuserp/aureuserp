<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_products', function (Blueprint $table) {
            $table->string('type')->default('asset')->after('category_id')->index();
            $table->index(['company_id', 'type', 'is_active']);
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('initial_quantity', 15, 4)->default(1)->after('condition');
            $table->decimal('quantity', 15, 4)->default(1)->after('initial_quantity');
        });

        Schema::table('asset_movements', function (Blueprint $table) {
            $table->decimal('quantity', 15, 4)->default(1)->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('asset_movements', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['initial_quantity', 'quantity']);
        });

        Schema::table('asset_products', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'type', 'is_active']);
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};
