<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameTableIfExists('employees_calendars', 'calendars');
        $this->renameTableIfExists('employees_calendar_attendances', 'calendar_attendances');
        $this->renameTableIfExists('employees_calendar_leaves', 'calendar_leaves');

        if (! Schema::hasTable('calendars')) {
            return;
        }

        $this->updateForeignKey('calendar_attendances', 'calendar_id', 'calendars', 'cascade');
        $this->updateForeignKey('calendar_leaves', 'calendar_id', 'calendars', 'set null');
        $this->updateForeignKey('employees_employees', 'calendar_id', 'calendars', 'set null');
        $this->updateForeignKey('time_off_leaves', 'calendar_id', 'calendars', 'set null');
    }

    public function down(): void
    {
        $this->renameTableIfExists('calendar_attendances', 'employees_calendar_attendances');
        $this->renameTableIfExists('calendar_leaves', 'employees_calendar_leaves');
        $this->renameTableIfExists('calendars', 'employees_calendars');

        if (! Schema::hasTable('employees_calendars')) {
            return;
        }

        $this->updateForeignKey('employees_calendar_attendances', 'calendar_id', 'employees_calendars', 'cascade');
        $this->updateForeignKey('employees_calendar_leaves', 'calendar_id', 'employees_calendars', 'set null');
        $this->updateForeignKey('employees_employees', 'calendar_id', 'employees_calendars', 'set null');
        $this->updateForeignKey('time_off_leaves', 'calendar_id', 'employees_calendars', 'set null');
    }

    /**
     * Rename table safely (idempotent)
     */
    private function renameTableIfExists(string $from, string $to): void
    {
        if (Schema::hasTable($from) && ! Schema::hasTable($to)) {
            Schema::rename($from, $to);
        }
    }

    /**
     * Drop & recreate foreign key safely
     */
    private function updateForeignKey(
        string $table,
        string $column,
        string $referenceTable,
        string $onDelete = 'cascade'
    ): void {
        if (! Schema::hasTable($table) || ! Schema::hasTable($referenceTable)) {
            return;
        }

        try {
            Schema::table($table, function (Blueprint $table) use ($column) {
                $table->dropForeign([$column]);
            });
        } catch (Throwable $e) {
        }

        try {
            Schema::table($table, function (Blueprint $table) use ($column, $referenceTable, $onDelete) {
                $table->foreign($column)
                    ->references('id')
                    ->on($referenceTable)
                    ->onDelete($onDelete);
            });
        } catch (Throwable $e) {
        }
    }
};
