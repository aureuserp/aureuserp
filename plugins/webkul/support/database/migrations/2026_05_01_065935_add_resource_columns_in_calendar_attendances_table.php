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
        Schema::table('calendar_attendances', function (Blueprint $table) {
            $table->nullableMorphs('resource');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_attendances', function (Blueprint $table) {
            $table->dropMorphs('resource');
        });
    }
};
