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
        $hasLegacyCalendars = Schema::hasTable('employees_calendars');
        $hasLegacyAttendances = Schema::hasTable('employees_calendar_attendances');
        $hasLegacyLeaves = Schema::hasTable('employees_calendar_leaves');

        if ($hasLegacyCalendars && ! Schema::hasTable('calendars')) {
            Schema::rename('employees_calendars', 'calendars');
        }

        if ($hasLegacyAttendances && ! Schema::hasTable('calendar_attendances')) {
            Schema::rename('employees_calendar_attendances', 'calendar_attendances');
        }

        if ($hasLegacyLeaves && ! Schema::hasTable('calendar_leaves')) {
            Schema::rename('employees_calendar_leaves', 'calendar_leaves');
        }

        $hasCalendars = Schema::hasTable('calendars');

        if ($hasCalendars && Schema::hasTable('calendar_attendances')) {
            try {
                Schema::table('calendar_attendances', function (Blueprint $table) {
                    $table->dropForeign(['calendar_id']);
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }

            try {
                Schema::table('calendar_attendances', function (Blueprint $table) {
                    $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('cascade');
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }
        }

        if ($hasCalendars && Schema::hasTable('calendar_leaves')) {
            try {
                Schema::table('calendar_leaves', function (Blueprint $table) {
                    $table->dropForeign(['calendar_id']);
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }

            try {
                Schema::table('calendar_leaves', function (Blueprint $table) {
                    $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('set null');
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }
        }

        if ($hasCalendars && Schema::hasTable('employees_employees')) {
            try {
                Schema::table('employees_employees', function (Blueprint $table) {
                    $table->dropForeign(['calendar_id']);
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }

            try {
                Schema::table('employees_employees', function (Blueprint $table) {
                    $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('set null');
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }
        }

        if ($hasCalendars && Schema::hasTable('time_off_leaves')) {
            try {
                Schema::table('time_off_leaves', function (Blueprint $table) {
                    $table->dropForeign(['calendar_id']);
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }

            try {
                Schema::table('time_off_leaves', function (Blueprint $table) {
                    $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('set null');
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('calendar_attendances') && ! Schema::hasTable('employees_calendar_attendances')) {
            Schema::rename('calendar_attendances', 'employees_calendar_attendances');
        }

        if (Schema::hasTable('calendar_leaves') && ! Schema::hasTable('employees_calendar_leaves')) {
            Schema::rename('calendar_leaves', 'employees_calendar_leaves');
        }

        if (Schema::hasTable('calendars') && ! Schema::hasTable('employees_calendars')) {
            Schema::rename('calendars', 'employees_calendars');
        }

        if (Schema::hasTable('employees_calendar_attendances') && Schema::hasTable('employees_calendars')) {
            try {
                Schema::table('employees_calendar_attendances', function (Blueprint $table) {
                    $table->dropForeign(['calendar_id']);
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }

            try {
                Schema::table('employees_calendar_attendances', function (Blueprint $table) {
                    $table->foreign('calendar_id')->references('id')->on('employees_calendars')->onDelete('cascade');
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }
        }

        if (Schema::hasTable('employees_calendar_leaves') && Schema::hasTable('employees_calendars')) {
            try {
                Schema::table('employees_calendar_leaves', function (Blueprint $table) {
                    $table->dropForeign(['calendar_id']);
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }

            try {
                Schema::table('employees_calendar_leaves', function (Blueprint $table) {
                    $table->foreign('calendar_id')->references('id')->on('employees_calendars')->onDelete('set null');
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }
        }

        if (Schema::hasTable('employees_employees') && Schema::hasTable('employees_calendars')) {
            try {
                Schema::table('employees_employees', function (Blueprint $table) {
                    $table->dropForeign(['calendar_id']);
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }

            try {
                Schema::table('employees_employees', function (Blueprint $table) {
                    $table->foreign('calendar_id')->references('id')->on('employees_calendars')->onDelete('set null');
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }
        }

        if (Schema::hasTable('time_off_leaves') && Schema::hasTable('employees_calendars')) {
            try {
                Schema::table('time_off_leaves', function (Blueprint $table) {
                    $table->dropForeign(['calendar_id']);
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }

            try {
                Schema::table('time_off_leaves', function (Blueprint $table) {
                    $table->foreign('calendar_id')->references('id')->on('employees_calendars')->onDelete('set null');
                });
            } catch (Throwable $exception) {
                // Intentionally ignored to keep migration idempotent.
            }
        }
    }
};
