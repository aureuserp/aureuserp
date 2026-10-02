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
        Schema::table('attendance_attendances', function (Blueprint $table) {
            $table->string('source_label', 100)->nullable()->after('source')->comment('Human Snapshot Of The Writer (Display Only)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_attendances', function (Blueprint $table) {
            $table->dropColumn('source_label');
        });
    }
};
