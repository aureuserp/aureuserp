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
            $table->index(['employee_id', 'work_date'], 'attendance_attendances_employee_work_date_index');
        });

        Schema::table('attendance_attendances', function (Blueprint $table) {
            $table->dropUnique('attendance_attendances_employee_work_date_source_unique');
        });

        if (! Schema::hasIndex('attendance_events', ['attendance_id'])) {
            Schema::table('attendance_events', function (Blueprint $table) {
                $table->index('attendance_id', 'attendance_events_attendance_id_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('attendance_events', 'attendance_events_attendance_id_index')) {
            Schema::table('attendance_events', function (Blueprint $table) {
                $table->dropIndex('attendance_events_attendance_id_index');
            });
        }

        Schema::table('attendance_attendances', function (Blueprint $table) {
            $table->unique(['employee_id', 'work_date', 'source'], 'attendance_attendances_employee_work_date_source_unique');
        });

        Schema::table('attendance_attendances', function (Blueprint $table) {
            $table->dropIndex('attendance_attendances_employee_work_date_index');
        });
    }
};
