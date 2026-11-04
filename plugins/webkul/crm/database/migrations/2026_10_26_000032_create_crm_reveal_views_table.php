<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_reveal_views', function (Blueprint $table) {
            $table->id();

            $table->string('reveal_ip', 45)->nullable();
            $table->unsignedBigInteger('visitor_id')->nullable()->index()->comment('website_visitors.id; no foreign key because the website plugin is optional');
            $table->string('url', 2000)->nullable();
            $table->string('reveal_state')->default('to_process')->comment('to_process or not_found');
            $table->foreignId('reveal_rule_id')->nullable()->constrained('crm_reveal_rules')->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['visitor_id', 'reveal_rule_id']);
            $table->index(['reveal_state', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_reveal_views');
    }
};
