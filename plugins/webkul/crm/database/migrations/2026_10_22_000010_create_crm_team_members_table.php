<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sales team members for the CRM: each salesperson's lead capacity and assignment rules.
     * Kept in sync with the `sales_team_members` link of the Sales plugin.
     */
    public function up(): void
    {
        Schema::create('crm_team_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')->constrained('sales_teams')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('assignment_optout')->default(false)->comment('Pause rule-based assignment for this member');
            $table->unsignedInteger('assignment_max')->default(30)->comment('Maximum leads assigned per 30 days');
            $table->json('assignment_domain')->nullable()->comment('Rules a lead must match to be assigned to this member');

            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });

        $now = now();

        DB::table('sales_team_members')->select(['team_id', 'user_id'])->distinct()->get()->chunk(500)->each(fn ($rows) => DB::table('crm_team_members')->insertOrIgnore(
            $rows->map(fn ($row): array => ['team_id' => $row->team_id, 'user_id' => $row->user_id, 'created_at' => $now, 'updated_at' => $now])->all()
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_team_members');
    }
};
