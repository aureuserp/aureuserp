<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Several leads converted at once from the leads list: the choices, the leads selected and
     * the salespeople the opportunities were shared between.
     */
    public function up(): void
    {
        Schema::create('crm_lead_mass_conversions', function (Blueprint $table) {
            $table->id();

            $table->string('action')->comment('convert or merge');
            $table->string('customer')->comment('create, link or none');
            $table->boolean('deduplicate')->default(true)->comment('Merge each lead with its similar records');
            $table->boolean('force_assignment')->default(false)->comment('Also reassign leads that already have a salesperson');
            $table->foreignId('team_id')->nullable()->constrained('sales_teams')->nullOnDelete();
            $table->unsignedInteger('converted_count')->default(0);
            $table->unsignedInteger('merged_count')->default(0);
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('crm_lead_mass_conversion_leads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mass_conversion_id')->constrained('crm_lead_mass_conversions')->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->comment('Empty once merged away')->constrained('crm_leads')->nullOnDelete();
            $table->unsignedBigInteger('original_lead_id')->comment('The record id, kept after it is merged away');
            $table->string('lead_name')->nullable();
            $table->foreignId('opportunity_id')->nullable()->comment('The resulting opportunity')->constrained('crm_leads')->nullOnDelete();

            $table->unique(['mass_conversion_id', 'original_lead_id'], 'crm_lead_mass_conv_leads_unique');
        });

        Schema::create('crm_lead_mass_conversion_salespeople', function (Blueprint $table) {
            $table->foreignId('mass_conversion_id')->constrained('crm_lead_mass_conversions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->primary(['mass_conversion_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_mass_conversion_salespeople');
        Schema::dropIfExists('crm_lead_mass_conversion_leads');
        Schema::dropIfExists('crm_lead_mass_conversions');
    }
};
