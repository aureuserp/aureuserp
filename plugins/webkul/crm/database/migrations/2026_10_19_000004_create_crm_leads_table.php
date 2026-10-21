<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();

            $table->string('type')->default('opportunity')->index()->comment('Lead or Opportunity');
            $table->string('name')->comment('Name');
            $table->foreignId('partner_id')->nullable()->constrained('partners_partners')->nullOnDelete();
            $table->string('email')->nullable()->comment('Email');
            $table->string('phone')->nullable()->comment('Phone');
            $table->decimal('expected_revenue', 15, 4)->default(0)->comment('Expected Revenue');
            $table->decimal('probability', 5, 2)->default(0)->comment('Probability');
            $table->unsignedTinyInteger('priority')->default(0)->comment('Priority');
            $table->date('expected_closing_date')->nullable()->comment('Expected Closing');
            $table->dateTime('closed_at')->nullable()->comment('Closed At');
            $table->text('description')->nullable()->comment('Internal Notes');
            $table->foreignId('stage_id')->nullable()->constrained('crm_stages')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('sales_teams')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Salesperson');
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('lost_reason_id')->nullable()->constrained('crm_lost_reasons')->nullOnDelete();
            $table->text('lost_feedback')->nullable()->comment('Lost Feedback');
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('crm_lead_tags', function (Blueprint $table) {
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('crm_tags')->cascadeOnDelete();

            $table->primary(['lead_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_tags');

        Schema::dropIfExists('crm_leads');
    }
};
