<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lead_visitor_rel', function (Blueprint $table) {
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->unsignedBigInteger('visitor_id')->index()->comment('website_visitors.id; no foreign key because the website plugin is optional');

            $table->primary(['lead_id', 'visitor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_visitor_rel');
    }
};
