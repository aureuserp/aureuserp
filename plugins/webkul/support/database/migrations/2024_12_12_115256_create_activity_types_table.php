<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\Support\Enums\ActivityChainingType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_types', function (Blueprint $table) {
            $table->id();

            $table->integer('sort')->nullable()->comment('Sort order');
            $table->integer('delay_count')->nullable()->comment('Delay count');
            $table->string('delay_unit')->comment('Delay unit');
            $table->string('delay_from')->comment('Delay from');
            $table->string('icon')->nullable()->comment('Icon');
            $table->string('decoration_type')->nullable()->comment('Decoration type');
            $table->string('chaining_type')->nullable()->default(ActivityChainingType::SUGGEST)->comment('Chaining type');
            $table->string('plugin')->nullable()->comment('Plugin name');
            $table->string('category')->nullable()->comment('Category');
            $table->string('name')->comment('Name');
            $table->text('summary')->nullable()->comment('Summary');
            $table->text('default_note')->nullable()->comment('Default Note');
            $table->boolean('is_active')->default(true)->comment('Status');
            $table->boolean('keep_done')->default(false)->comment('Keep Done');

            $table->unsignedBigInteger('creator_id')->nullable()->comment('Created By');
            $table->unsignedBigInteger('default_user_id')->nullable()->comment('Default User');
            $table->unsignedBigInteger('activity_plan_id')->nullable()->comment('Activity Plan');
            $table->unsignedBigInteger('triggered_next_type_id')->nullable()->comment('Triggered Next Type');

            $table->foreign('activity_plan_id')->references('id')->on('activity_plans')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('default_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('triggered_next_type_id')->references('id')->on('activity_types')->onDelete('restrict');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_types');
    }
};
