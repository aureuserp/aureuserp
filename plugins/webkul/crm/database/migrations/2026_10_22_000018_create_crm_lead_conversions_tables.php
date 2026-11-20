<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A lead converted to an opportunity (the "Convert" dialog): what was chosen, and the similar
     * leads/opportunities it was merged with.
     */
    public function up(): void
    {
        Schema::create('crm_lead_conversions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')->nullable()->comment('The converted lead; empty once merged away')->constrained('crm_leads')->nullOnDelete();
            $table->unsignedBigInteger('original_lead_id')->comment('The converted lead id, kept after it is merged away');
            $table->string('lead_name')->nullable();
            $table->foreignId('opportunity_id')->nullable()->comment('The resulting opportunity')->constrained('crm_leads')->nullOnDelete();
            $table->string('action')->comment('convert or merge');
            $table->string('customer')->comment('create, link or none');
            $table->foreignId('partner_id')->nullable()->constrained('partners_partners')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->comment('Salesperson')->constrained('users')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('sales_teams')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('crm_lead_conversion_duplicates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversion_id')->constrained('crm_lead_conversions')->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->comment('Empty once merged away')->constrained('crm_leads')->nullOnDelete();
            $table->unsignedBigInteger('original_lead_id')->comment('The record id, kept after it is merged away');
            $table->string('lead_name')->nullable();

            $table->unique(['conversion_id', 'original_lead_id'], 'crm_lead_conv_duplicates_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_conversion_duplicates');
        Schema::dropIfExists('crm_lead_conversions');
    }
};
