<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lead_roles', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Contact role (job function) used by Visits to Leads rules');
            $table->unsignedInteger('sort')->default(0);
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_roles');
    }
};
