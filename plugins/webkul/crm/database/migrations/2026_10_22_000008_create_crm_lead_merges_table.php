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
        Schema::create('crm_lead_merges', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')->comment('The lead or opportunity that was kept')->constrained('crm_leads')->cascadeOnDelete();
            $table->json('merged_records')->comment('Snapshot of the merged (deleted) records');
            $table->foreignId('merged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_lead_merges');
    }
};
