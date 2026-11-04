<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->string('lead_type')->comment('Lead type when the activity was done');
            $table->foreignId('activity_type_id')->nullable()->constrained('activity_types')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('done_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->comment('Salesperson when the activity was done')->constrained('users')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->comment('Sales team when the activity was done')->constrained('sales_teams')->nullOnDelete();
            $table->foreignId('stage_id')->nullable()->comment('Stage when the activity was done')->constrained('crm_stages')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('summary')->nullable();
            $table->text('note')->nullable()->comment('The "done" message posted on the lead');
            $table->date('date_deadline')->nullable();
            $table->timestamp('activity_created_at')->nullable();
            $table->timestamp('done_at')->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activity_logs');
    }
};
