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
        Schema::create('employees_employee_resume_attachments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_resume_id');
            $table->unsignedBigInteger('creator_id')->nullable()->comment('Created by');
            $table->string('name')->nullable()->comment('Name');
            $table->string('file_path')->comment('File Path');
            $table->string('original_file_name')->nullable()->comment('Original File Name');
            $table->string('mime_type')->nullable()->comment('Mime Type');
            $table->string('file_size')->nullable()->comment('File Size');

            $table->foreign('employee_resume_id')->references('id')->on('employees_employee_resumes')->cascadeOnDelete();
            $table->foreign('creator_id')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_employee_resume_attachments');
    }
};
