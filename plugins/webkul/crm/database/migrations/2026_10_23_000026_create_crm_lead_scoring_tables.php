<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lead_scoring_frequencies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')->nullable()->comment('Empty for records without a sales team')->constrained('sales_teams')->cascadeOnDelete();
            $table->string('variable')->comment('stage, country_id, state_id, utm_source_id, tags, phone, email or __total');
            $table->string('value');
            $table->decimal('won_count', 12, 2)->default(0);
            $table->decimal('lost_count', 12, 2)->default(0);

            $table->index(['team_id', 'variable']);
        });

        Schema::table('crm_leads', function (Blueprint $table) {
            $table->decimal('automated_probability', 5, 2)->nullable()->after('probability')->comment('Predictive lead scoring; empty without won/lost history');
            $table->boolean('is_automated_probability')->default(true)->after('automated_probability')->comment('Probability follows the automated one');
        });
    }

    public function down(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->dropColumn(['automated_probability', 'is_automated_probability']);
        });

        Schema::dropIfExists('crm_lead_scoring_frequencies');
    }
};
