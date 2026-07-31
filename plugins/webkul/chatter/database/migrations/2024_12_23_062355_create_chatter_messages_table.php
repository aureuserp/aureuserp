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
        Schema::create('chatter_messages', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id')->nullable()->comment('Company');
            $table->unsignedBigInteger('activity_type_id')->nullable()->comment('Activity Type');
            $table->unsignedBigInteger('assigned_to')->nullable()->comment('Assigned To');
            $table->morphs('messageable');
            $table->string('type')->nullable()->comment('Message Type');
            $table->string('name')->nullable()->comment('Name');
            $table->string('subject')->nullable()->comment('Subject');
            $table->text('body')->nullable()->comment('Body');
            $table->text('summary')->nullable()->comment('Summary');
            $table->boolean('is_internal')->nullable()->comment('Is Internal');
            $table->date('date_deadline')->nullable()->comment('Date');
            $table->date('pinned_at')->nullable()->comment('Pinned At');
            $table->string('log_name')->nullable();
            $table->nullableMorphs('causer');
            $table->string('event')->nullable();
            $table->json('properties')->nullable();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('activity_type_id')->references('id')->on('activity_types')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatter_messages');
    }
};
