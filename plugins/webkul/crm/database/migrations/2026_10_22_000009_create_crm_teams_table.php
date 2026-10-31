<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CRM settings of a sales team (the CRM fields of the team): whether it takes part in
     * rule-based lead assignment and which leads it accepts.
     */
    public function up(): void
    {
        Schema::create('crm_teams', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')->unique()->constrained('sales_teams')->cascadeOnDelete();
            $table->boolean('use_leads')->default(true)->comment('Team works with leads');
            $table->boolean('use_opportunities')->default(true)->comment('Team works with opportunities');
            $table->boolean('assignment_optout')->default(false)->comment('Skip this team in rule-based assignment');
            $table->json('assignment_domain')->nullable()->comment('Rules a lead must match to be assigned to this team');

            $table->timestamps();
        });

        $now = now();

        DB::table('sales_teams')->pluck('id')->chunk(500)->each(fn ($ids) => DB::table('crm_teams')->insert(
            $ids->map(fn ($id): array => ['team_id' => $id, 'created_at' => $now, 'updated_at' => $now])->all()
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_teams');
    }
};
