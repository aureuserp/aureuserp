<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manufacturing_operations', function (Blueprint $table) {
            $table->string('worksheet')->nullable()->after('worksheet_type');
        });
    }

    public function down(): void
    {
        Schema::table('manufacturing_operations', function (Blueprint $table) {
            if (Schema::hasColumn('manufacturing_operations', 'worksheet')) {
                $table->dropColumn('worksheet');
            }
        });
    }
};
