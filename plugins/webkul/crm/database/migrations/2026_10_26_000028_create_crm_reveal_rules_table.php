<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_reveal_rules', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0)->comment('Lower runs first');
            $table->string('regex_url')->nullable()->comment('URL expression; empty = whole website, "/" = homepage only');
            $table->string('lead_for')->default('companies')->comment('companies or people (companies and their contacts)');
            $table->string('lead_type')->default('opportunity')->comment('lead or opportunity');
            $table->string('suffix')->nullable()->comment('Appended to the lead name: "<name> - <suffix>"');
            $table->unsignedTinyInteger('priority')->default(0);
            $table->boolean('filter_on_size')->default(true)->comment('Company enrichment only');
            $table->unsignedInteger('company_size_min')->default(0)->comment('Company enrichment only');
            $table->unsignedInteger('company_size_max')->default(1000)->comment('Company enrichment only');
            $table->string('contact_filter_type')->default('role')->comment('role or seniority; company enrichment only');
            $table->unsignedTinyInteger('extra_contacts')->default(1)->comment('1 to 5; company enrichment only');

            $table->foreignId('team_id')->nullable()->constrained('sales_teams')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_reveal_rules');
    }
};
