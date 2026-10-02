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
        Schema::create('attendance_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->comment('Employee');
            $table->dateTime('punched_at')->comment('Punch Instant (UTC)');
            $table->tinyInteger('direction')->default(0)->comment('Punch Direction: 1=In, -1=Out, 0=Unknown');
            $table->string('source', 64)->comment('Writer Key: <writer>[:<id>]');
            $table->string('source_label', 100)->nullable()->comment('Human Snapshot Of The Writer (Display Only)');
            $table->string('source_ref', 64)->comment('Writer Record Reference');
            $table->unsignedBigInteger('attendance_id')->nullable()->comment('Paired Attendance Row');
            $table->unsignedBigInteger('company_id')->nullable()->comment('Company');

            $table->foreign('employee_id')->references('id')->on('employees_employees')->onDelete('cascade');
            $table->foreign('attendance_id')->references('id')->on('attendance_attendances')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');

            $table->unique(['source', 'source_ref']);
            $table->index(['employee_id', 'punched_at']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_events');
    }
};
