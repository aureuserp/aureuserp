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
        if (
            Schema::hasTable('employees_calendars')
            && ! Schema::hasTable('calendars')
        ) {
            Schema::rename('employees_calendars', 'calendars');
        } elseif (! Schema::hasTable('calendars')) {
            Schema::create('calendars', function (Blueprint $table) {
                $table->id();

                $table->string('name')->comment('Name');
                $table->string('timezone')->comment('Timezone');
                $table->float('hours_per_day')->nullable()->comment('Average Hour per Day');
                $table->boolean('is_active')->default(false)->comment('Status');
                $table->boolean('two_weeks_calendar')->nullable()->default(false)->comment('Calendar in 2 weeks mode');
                $table->boolean('flexible_hours')->nullable()->default(false)->comment('Flexible Hours');
                $table->float('full_time_required_hours')->nullable()->comment('Company Full Time');

                $table->unsignedBigInteger('creator_id')->nullable()->comment('Created By');
                $table->unsignedBigInteger('company_id')->nullable()->comment('Company');

                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');

                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (
            Schema::hasTable('employees_calendar_attendances')
            && ! Schema::hasTable('calendar_attendances')
        ) {
            Schema::rename('employees_calendar_attendances', 'calendar_attendances');
        } elseif (! Schema::hasTable('calendar_attendances')) {
            Schema::create('calendar_attendances', function (Blueprint $table) {
                $table->id();

                $table->integer('sort')->nullable()->comment('Sort Order');
                $table->string('name')->comment('Name');
                $table->string('day_of_week')->comment('Day of Week');
                $table->string('day_period')->comment('Day Period');
                $table->string('week_type')->nullable()->comment('Week Type');
                $table->string('display_type')->nullable()->comment('Display Type');
                $table->string('date_from')->nullable()->comment('Date From');
                $table->string('date_to')->nullable()->comment('Date To');
                $table->string('duration_days')->nullable()->comment('Durations Days');
                $table->string('hour_from')->comment('Hour From');
                $table->string('hour_to')->comment('Hour To');

                $table->unsignedBigInteger('calendar_id')->comment('Calendar ID');
                $table->unsignedBigInteger('creator_id')->nullable()->comment('Created By');

                $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');

                $table->timestamps();
            });
        }

        if (
            Schema::hasTable('employees_calendar_leaves')
            && ! Schema::hasTable('calendar_leaves')
        ) {
            Schema::rename('employees_calendar_leaves', 'calendar_leaves');
        } elseif (! Schema::hasTable('calendar_leaves')) {
            Schema::create('calendar_leaves', function (Blueprint $table) {
                $table->id();

                $table->string('name');
                $table->string('time_type');
                $table->string('date_from');
                $table->string('date_to');

                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('calendar_id')->nullable();
                $table->unsignedBigInteger('creator_id')->nullable();

                $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
                $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('set null');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('calendar_leaves') && ! Schema::hasTable('employees_calendar_leaves')) {
            Schema::rename('calendar_leaves', 'employees_calendar_leaves');
        }

        if (Schema::hasTable('calendar_attendances') && ! Schema::hasTable('employees_calendar_attendances')) {
            Schema::rename('calendar_attendances', 'employees_calendar_attendances');
        }

        if (Schema::hasTable('calendars') && ! Schema::hasTable('employees_calendars')) {
            Schema::rename('calendars', 'employees_calendars');
        }
    }
};
