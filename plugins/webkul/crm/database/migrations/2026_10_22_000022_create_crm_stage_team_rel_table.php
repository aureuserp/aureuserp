<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The sales teams a pipeline stage is used by. A stage without teams is shared by all teams.
     */
    public function up(): void
    {
        Schema::create('crm_stage_team_rel', function (Blueprint $table) {
            $table->foreignId('stage_id')->constrained('crm_stages')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('sales_teams')->cascadeOnDelete();

            $table->primary(['stage_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_stage_team_rel');
    }
};
